<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'php_image_resize' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'ImageResize.php';

use \Eventviva\ImageResize;

/**
 * Картинки
 */
class Image
{
    /**
     * URL дерева картинок
     *
     * @var     string
     * @access  public
     */
    const ROOT_URL = '/prod_src/uploads';

    /**
     * Корень дерева картинок в файловой системе
     *
     * @var     string
     * @access  public
     */
    const ROOT_DIR = DIRECTORY_SEPARATOR . 'prod_src' . DIRECTORY_SEPARATOR . 'uploads';

    /**
     * Массив префиксов для построения пути из имени
     *
     * Путь формируется из трех уровней по трем первым символам имени (sha256). Например,
     * /012/345/678 для 0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef.ext
     * /b36/6ca/9e5 для b366ca9e55c8ea21fec6164a0bf2f68d60943a3921c5726f121af227f24c732c.png
     *
     * @param   string $file_name
     * @return  array
     * @access  private
     * @static
     */
    private static function pathChunks($file_name)
    {
        return [mb_substr($file_name, 0, 3), mb_substr($file_name, 3, 3), mb_substr($file_name, 6, 3)];
    }

    /**
     * Собирает полный путь в файловой системе из имени и расширения
     *
     * @param   string $file_name
     * @return  string
     * @access  public
     * @static
     */
    public static function path($file_name, $extension)
    {
        $path = PUBLIC_DIR . self::ROOT_DIR . DIRECTORY_SEPARATOR .
            implode(DIRECTORY_SEPARATOR, self::pathChunks($file_name)) . DIRECTORY_SEPARATOR . $file_name;

        if ($extension) {
            $path .= '.' . $extension;
        }

        return $path;
    }

    /**
     * Собирает URL из имени и расширения
     *
     * @param   string $file_name
     * @return  string
     * @access  public
     * @static
     */
    public static function url($file_name, $extension)
    {
        $url = self::ROOT_URL . '/' . implode('/', self::pathChunks($file_name)) . '/' . $file_name;

        if ($extension) {
            $url .= '.' . $extension;
        }

        return $url;
    }

    /**
     * Инфа картинки по id
     *
     * @param   int $id
     * @return  mixed
     * @access  public
     * @static
     */
    public static function getById($id)
    {
        $id = (int)$id;
        $result = Application::$db->query("SELECT * FROM " . Core::$config['db']['base_new'] . ".images WHERE id = :id", [':id' => $id]);

        if (!$result) {
            return false;
        }

        return $result->fetchAssocArray();
    }

    /**
     * Поиск картинок по опциям
     *
     * @param   array $options
     * @return  mixed
     * @access  public
     * @static
     */
    public static function findByOptions($options)
    {
        $result = Application::$db->query("
            SELECT *
            FROM " . Core::$config['db']['base_new'] . ".images
            WHERE original_id = ':original_id'
            AND width = ':width'
            AND height = ':height'
            AND resize_type = ':resize_type'
        ", [
            ':original_id' => $options['original_id'],
            ':width' => $options['width'],
            ':height' => $options['height'],
            ':resize_type' => $options['resize_type']
        ]);

        if (!$result) {
            return false;
        }

        $data = [];

        while ($row = $result->fetchAssocArray()) {
            $data[] = $row;
        }

        return $data;
    }

    /**
     * Добавляет в базу картинку
     *
     * @param   array $data
     * @return  mixed   данные добавленной картинки или false при ошибке
     * @access  public
     * @static
     */
    public static function insert($data)
    {
        $result = Application::$db->query("
            INSERT INTO " . Core::$config['db']['base_new'] . ".images (
                original_id,
                width,
                height,
                resize_type,
                quality,
                file_size,
                mime_type,
                file_name,
                extension,
                original_name,
                alt
            )
            VALUES (
                :original_id,
                ':width',
                ':height',
                ':resize_type',
                ':quality',
                ':file_size',
                ':mime_type',
                ':file_name',
                ':extension',
                ':original_name',
                ':alt'
            )", [
            ':original_id' => $data['original_id'] ? "'" . Application::$db->escape($data['original_id']) . "'" : 'NULL',
            ':width' => Application::$db->escape($data['width']),
            ':height' => Application::$db->escape($data['height']),
            ':resize_type' => Application::$db->escape($data['resize_type']),
            ':quality' => Application::$db->escape($data['quality']),
            ':file_size' => Application::$db->escape($data['file_size']),
            ':mime_type' => Application::$db->escape($data['mime_type']),
            ':file_name' => Application::$db->escape($data['file_name']),
            ':extension' => Application::$db->escape($data['extension']),
            ':original_name' => Application::$db->escape($data['original_name']),
            ':alt' => Application::$db->escape($data['alt'])
        ]);

        if (!$result) {
            return false;
        }

        return Application::$db->lastInsertId();
    }

    /**
     * Поиск подходящей по опциям картинки и ресайз на лету при необходимости
     *
     * @param   int $id
     * @param   array $options
     * @return  mixed   данные картинки или false при ошибке
     * @access  public
     * @static
     */
    public static function getResized($id, $options)
    {
        $image = self::getById((int)$id);
        if (!$image) {
            return false;
        }

        if ($image['original_id']) {
            $image = self::getById((int)$image['original_id']);
            if (!$image) {
                return false;
            }
        }

        // Ресайз ресайзенных картинок недопустим
        if ($image['original_id']) {
            return false;
        }

        // Быть может, уже есть такая картинка
        $options['original_id'] = $image['id'];
        $found = self::findByOptions($options);
        if (count($found)) {
            return self::getById($found[0]['id']);
        }

        $resize = new ImageResize(self::path($image['file_name'], $image['extension']));
        $resize->crop($options['width'], $options['height']);

        $image['original_id'] = $image['id'];
        $image['width'] = $options['width'];
        $image['height'] = $options['height'];
        $image['resize_type'] = $options['resize_type'];

        return self::create($image, $resize);
    }

    public static function createFromDataURL($data_url)
    {
        $resize = new ImageResize($data_url);

        $image = [];
        $image['original_id'] = null;
        $image['width'] = $resize->getSourceWidth();
        $image['height'] = $resize->getSourceHeight();
        $image['resize_type'] = '';
        $image['original_name'] = '';
        $image['alt'] = '';

        return self::create($image, $resize);
    }

    public static function create($image, $resize)
    {
        switch ($resize->source_type) {
            case IMAGETYPE_JPEG:
                $image['quality'] = $resize->quality_jpg;
                $image['mime_type'] = 'image/jpeg';
                $image['extension'] = 'jpg';
                break;
            case IMAGETYPE_PNG:
                $image['quality'] = $resize->quality_png;
                $image['mime_type'] = 'image/png';
                $image['extension'] = 'png';
                break;
            case IMAGETYPE_WEBP:
                $image['quality'] = $resize->quality_webp;
                $image['mime_type'] = 'image/webp';
                $image['extension'] = 'webp';
                break;
            case IMAGETYPE_GIF:
                $image['mime_type'] = 'image/gif';
                $image['quality'] = 0;
                $image['extension'] = 'gif';
                break;
            default:
                break;
        }

        $image['file_name'] = hash('sha256', md5(uniqid(mt_rand())) . microtime(true));

        $path = self::path($image['file_name'], $image['extension']);

        $dir = dirname($path);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $resize->save($path);

        $image['file_size'] = filesize($path);

        $id = self::insert($image);

        return self::getById($id);
    }
}
