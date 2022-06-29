<?php

class AdminserviceModel extends AbstractModel
{

    public $breadcrumbs = [];

    public $token = '';

    public $message = '';

    public $cache_dir = '';
    public $cache_list = [];

    public $file_stat = 'cache_stat.dat';

    public function __construct()
    {
        $this->cache_dir = ROOT_DIR . 'cache/';
        parent::__construct();
    }

    /**
     * @throws \Core\Errors\HttpNotFoundException
     */
    protected function dataProcess()
    {
        $this->token = ($_REQUEST['token']) ?? '';
        $this->checkAccess();

        $act = ($_REQUEST['act']) ?? 'default';

        $cache_path = ROOT_DIR . Core::$config['sql']['cache_dir'];

        $directory = $cache_path;
        $scanned_cache_directory = array_diff(scandir($directory), ['..', '.']);
        $this->cache_list = $scanned_cache_directory;

        switch ($act) {

            case 'clear_cache_sql':
                $this->clearCache();
                break;

            case 'clear_cache_image':
                $this->clearCacheImages();
                break;

            case 'clear_slowly':
                $dir_data = $this->getOldFiles($cache_path);
                $this->clearSlowly($dir_data);
                break;

            case 'remove_sql_folder':
                $this->removeSqlFolder();
                break;

            case 'default':
            default:
                $this->message = $this->getMessage();
                break;
        }
    }

    /**
     * Очистка кэша sql
     */
    private function clearCache()
    {
        $cache_path = ROOT_DIR . Core::$config['sql']['cache_dir'];
        $cache = ($_REQUEST['cache']) ?? '';
        $cache = trim($cache);
        $cache = trim($cache, '.');
        $cache = trim($cache, '/');

        $cache_folder = $cache_path . $cache;

        $message = "Кэш SQL {$cache} очищен";

        if (empty($cache)) {
            $message = 'Весь SQL кэш очищен';
        }

        if (is_dir($cache_folder)) {
            Service::empty_dir($cache_folder, false);
        } else {
            $message = 'Не удалось очистить SQL кэш';
        }

        $this->toHome($message);
    }

    /**
     * Очистка кэша изображений
     */
    private function clearCacheImages()
    {
        $cache_image_path = $cache_dir_absolute = ROOT_DIR . 'public' . DIRECTORY_SEPARATOR . Core::$config['img']['cache_dir'];

        $message = 'Кэш изображений очищен';

        if (is_dir($cache_image_path)) {
            Service::empty_dir($cache_image_path, false);
        } else {
            $message = 'Не удалось очистить кэш изображений';
        }

        $this->toHome($message);
    }

    /**
     * @throws \Core\Errors\HttpNotFoundException
     */
    private function checkAccess()
    {
        $adminservice_token = Core::$config['adminservice_token'];
        if ($this->token !== $adminservice_token) {
            Application::abort(410);
        }
    }

    private function toHome($message = '', $data = [])
    {
        if (!empty($message)) {
            setcookie('message', $message);
        }
        $url = '/adminservice/?token=' . $this->token;
        Application::redirect($url);
    }

    private function getMessage()
    {
        $message = $_COOKIE['message'] ?? '';
        setcookie('message', '', time() - 1);
        return $message;

    }

    public function fillArrayWithFileNodes(DirectoryIterator $dir)
    {
        $data = [];
        foreach ($dir as $node) {
            if ($node->isDir() && !$node->isDot()) {
                $data[$node->getFilename()] = $this->fillArrayWithFileNodes(new DirectoryIterator($node->getPathname()));
            } else if ($node->isFile()) {
                $data[] = $node->getFilename();
            }
        }
        return $data;
    }

    public function getOldFiles($path, $time = 60 * 60 * 5, $limit_files = 100)
    {
        $now = time();
        $ite = new RecursiveDirectoryIterator($path);
        $files = [];
        $bytes_total = 0;
        $nb_files = 0;
        foreach (new RecursiveIteratorIterator($ite) as $filename => $file) {
            if ($file->isDir()) {
                @rmdir($filename);
            } elseif ($file->isFile()) {
                $is_old_file = ($now - $file->getCTime() > $time);
                if ($is_old_file) {
                    $nb_files++;
                    if ($nb_files > $limit_files) {
                        break;
                    }
                    $file_size = $file->getSize();
                    $bytes_total += $file_size;

                    $files[] = $filename;
                }
            }
        }

        return ['total_files' => $nb_files, 'total_size' => $bytes_total, 'files' => $files];
    }

    public function getJsonData($file)
    {
        $json = [];
        if (file_exists($file)) {
            $json = json_decode(file_get_contents($file), true);
        }
        return $json;
    }

    public function writeJsonData($file, $data = [])
    {
        file_put_contents($file, json_encode($data));
    }

    public function clearSlowly($dir_data)
    {
        $files = $dir_data['files'];

        foreach ($files as $file) {
            @unlink($file);
        }

        $stat_file = $this->cache_dir . $this->file_stat;
        $stat = $this->getJsonData($stat_file);

        $stat['total_files'] = ($stat['total_files'] ?? 0) + $dir_data['total_files'];
        $stat['total_size'] = ($stat['total_size'] ?? 0) + $dir_data['total_size'];

        $this->writeJsonData($stat_file, $stat);
    }

    public function removeSqlFolder()
    {
        @rmdir($this->cache_dir);
    }
}