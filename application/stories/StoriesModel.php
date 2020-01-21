<?php

require_once Core::$config['application_root'] . 'stories' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'Image.php';

require_once Core::$config['application_root'] . 'firms' . DIRECTORY_SEPARATOR . 'FirmsModel.php';

require_once Core::$config['application_root'] . 'stories' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'StoriesSQLHelper.php';
require_once Core::$config['application_root'] . 'stories' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'StoriesDataHelper.php';

class StoriesModel extends AbstractModel
{
    use Application\Traits\ModelTrait;

    const BREADCRUMBS_POINT = 'Сюжеты';

    // Количество сюжетов на странице
    const LIMIT = 12;

    const THUMB_RESIZE_TYPE = 'crop_center';
    const THUMB_RESIZE_WIDTH = 270;
    const THUMB_RESIZE_HEIGHT = 270;

    const IMAGE_RESIZE_TYPE = 'crop_center';
    const IMAGE_RESIZE_WIDTH = 500;
    const IMAGE_RESIZE_HEIGHT = 500;

    public $breadcrumbs = [];

    public $firm_data = [];

    public $cabinet_link = '';

    public $stories = [];

    public $story = [];

    public $page = 0;

    public $show_loader = false;

    /**
     * @throws \Core\Errors\HttpNotFoundException
     */
    protected function dataProcess()
    {
        // Обработка лайков
        if (Application::$action == 'like') {

            // Запрос-неформат
            if (!isset($_POST['id_story']) || !(int)$_POST['id_story'] || !isset($_POST['is_like'])) {
                http_response_code(400);
                exit;
            }

            $id_story = (int)$_POST['id_story'];
            $is_like = (bool)$_POST['is_like'];

            // Ищем сюжет
            $result = Application::$db->query(StoriesSQLHelper::selStory(), [':id_story' => $id_story]);
            $this->story = $result->fetchAssocArray();
            if (!$this->story) {
                Application::abort(410);
            }

            // Идентифицируем лайк по ip и браузеру
            $remote_addr = Core::$user_ip;
            $http_user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

            // Не переданы данные для идентификации
            if (!$remote_addr || !$http_user_agent) {
                Application::abort(410);
            }

            // Сессия - это хэш от конкатенации ip и браузера
            $session_id = md5($remote_addr . $http_user_agent);

            // Не голосует ли пользователь повторно за сутки?
            $result = Application::$db->query(StoriesSQLHelper::selSessionStory(), [':session_id' => $session_id, ':story_id' => $id_story]);
            $already_liked = $result->fetchAssocArray();
            if ($already_liked) {
                http_response_code(400);
                exit;
            }

            // Регистрируем сегодняшний голос
            Application::$db->query(StoriesSQLHelper::insSessionStory(), [':session_id' => $session_id, ':story_id' => $id_story]);

            // Увеличиваем общее количество лайков
            $sql = $is_like ? StoriesSQLHelper::updStoryLikes() : StoriesSQLHelper::updStoryDislikes();
            Application::$db->query($sql, [':id' => $id_story]);

            // Для вывода количество лайков на данный момент
            $result = Application::$db->query(StoriesSQLHelper::selStory(), [':id_story' => $id_story]);
            $this->story = $result->fetchAssocArray();
            if (!$this->story) {
                Application::abort(410);
            }

            echo $is_like ? $this->story['likes'] : $this->story['dislikes'];
            exit;
        }

        if (!isset($_REQUEST['id_firm']) || !(int)$_REQUEST['id_firm']) {
            Application::abort(410);
        }

        $this->firm_data = $this->fetchFirmById((int)$_REQUEST['id_firm']);
        if (!$this->firm_data) {
            Application::abort(410);
        }

        $this->cabinet_link = $this->cabinetLink($this->firm_data);

        // Нет сюжетов - отправляем в кабинет добавлять сюжеты
        if (!$this->firm_data['stories_count']) {
            Application::redirect($this->cabinet_link);
        }

        // Передаем в js id_firm
        FE::addData('id_firm', $this->firm_data['id']);

        switch (Application::$action) {

            // Подгрузка сюжетов
            case 'load':

                if (!empty($_REQUEST['page'])) {
                    $this->page = (int)$_REQUEST['page'];
                    if ($this->page < 0) {
                        $this->page = 0;
                    }
                }

                $this->fillStories();

                break;

            case 'default':
            default:

                $this->breadcrumbs = [
                    ['name' => $this->firm_data['sprav_name'], 'url' => $this->firm_data['sprav_url'], 'active' => 0],
                    ['name' => $this->firm_data['rubr_name'], 'url' => $this->firm_data['sprav_url'] . $this->firm_data['rubr_url'] . '/', 'active' => 0],
                    ['name' => $this->firm_data['name'], 'url' => $this->firm_data['firm_url'], 'active' => 0],
                    ['name' => self::BREADCRUMBS_POINT, 'url' => '', 'active' => 1],
                ];

                if (isset($_REQUEST['id_story'])) {
                    $query = Application::$db->query(StoriesSQLHelper::selStory(), [':id_story' => (int)$_REQUEST['id_story']]);

                    $this->story = $query->fetchAssocArray();
                    if (!$this->story) {
                        Application::abort(410);
                    }

                    if (!$this->story['original_id'] || $this->story['width'] != self::IMAGE_RESIZE_WIDTH ||
                        $this->story['height'] != self::IMAGE_RESIZE_HEIGHT || $this->story['resize_type'] != self::IMAGE_RESIZE_TYPE) {

                        $image = Image::getResized($this->story['image_id'],
                            ['width' => self::IMAGE_RESIZE_WIDTH, 'height' => self::IMAGE_RESIZE_HEIGHT, 'resize_type' => self::IMAGE_RESIZE_TYPE]);

                        if ($image && $image['id'] !== $this->story['image_id']) {

                            Application::$db->query(StoriesSQLHelper::updStoryImage(), [':image_id' => $image['id'], ':id' => $this->story['id']]);

                            $this->story['thumb_id'] = $image['id'];
                            $this->story['original_id'] = $image['original_id'];
                            $this->story['width'] = $image['width'];
                            $this->story['height'] = $image['height'];
                            $this->story['resize_type'] = $image['resize_type'];
                            $this->story['quality'] = $image['quality'];
                            $this->story['file_name'] = $image['file_name'];
                            $this->story['extension'] = $image['extension'];
                            $this->story['mime_type'] = $image['mime_type'];
                            $this->story['alt'] = $image['alt'];
                        }

                    }

                    $this->story['created_at'] = $this->story['created_at'] == '0000-00-00 00:00:00' ? '' : date('Y-m-d', strtotime($this->story['created_at']));
                    $this->story['author'] = htmlspecialchars($this->story['author'], ENT_COMPAT);
                    $this->story['video_link'] = htmlspecialchars($this->story['video_link'], ENT_COMPAT);
                    $this->story['video_duration'] = StoriesDataHelper::formatDuration($this->story['video_duration']);
                    $this->story['image_url'] = Image::url($this->story['file_name'], $this->story['extension']);
                    $this->story['content'] = htmlspecialchars($this->story['content'], ENT_COMPAT);
                    $this->story['likes'] = (int)$this->story['likes'];
                    $this->story['dislikes'] = (int)$this->story['dislikes'];

                } else {

                    $this->fillStories();

                }

                break;

            case 'ShowNews':
                break;

            case 'UpdateSocial':
                break;
        }
    }

    private function fillStories()
    {
        if ($this->firm_data['stories_count'] > ($this->page + 1) * self::LIMIT) {
            $this->show_loader = true;
        }

        $result = Application::$db->query(StoriesSQLHelper::selStories(),
            [':id_firm' => $this->firm_data['id'], ':start' => $this->page * self::LIMIT, ':limit' => self::LIMIT]);

        while ($story = $result->fetchAssocArray()) {

            if (!$story['original_id'] || $story['width'] != self::THUMB_RESIZE_WIDTH ||
                $story['height'] != self::THUMB_RESIZE_HEIGHT || $story['resize_type'] != self::THUMB_RESIZE_TYPE) {

                $image = Image::getResized($story['thumb_id'],
                    ['width' => self::THUMB_RESIZE_WIDTH, 'height' => self::THUMB_RESIZE_HEIGHT, 'resize_type' => self::THUMB_RESIZE_TYPE]);

                if ($image && $image['id'] !== $story['thumb_id']) {

                    Application::$db->query(StoriesSQLHelper::updStoryThumb(), [':thumb_id' => $image['id'], ':id' => $story['id']]);

                    $story['thumb_id'] = $image['id'];
                    $story['original_id'] = $image['original_id'];
                    $story['width'] = $image['width'];
                    $story['height'] = $image['height'];
                    $story['resize_type'] = $image['resize_type'];
                    $story['quality'] = $image['quality'];
                    $story['file_name'] = $image['file_name'];
                    $story['extension'] = $image['extension'];
                    $story['mime_type'] = $image['mime_type'];
                    $story['alt'] = $image['alt'];
                }

            }

            $story['video_link'] = htmlspecialchars($story['video_link'], ENT_COMPAT);
            $story['video_duration'] = StoriesDataHelper::formatDuration($story['video_duration']);
            $story['thumb_url'] = Image::url($story['file_name'], $story['extension']);
            $story['content'] = htmlspecialchars($story['content'], ENT_COMPAT);
            $story['likes'] = (int)$story['likes'];
            $story['dislikes'] = (int)$story['dislikes'];

            $this->stories[] = $story;
        }
    }

}
