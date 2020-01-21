<?php

class Db
{

    public $SEC_PER_1_MINUTE = 60;
    public $SEC_PER_5_MINUTES = 5 * 60;
    public $SEC_PER_10_MINUTES = 10 * 60;
    public $SEC_PER_30_MINUTES = 30 * 60;
    public $SEC_PER_1_HOUR = 60 * 60;
    public $SEC_PER_2_HOUR = 2 * 60 * 60;
    public $SEC_PER_5_HOUR = 5 * 60 * 60;
    public $SEC_PER_10_HOUR = 10 * 60 * 60;
    public $SEC_PER_DAY = 24 * 60 * 60;

    protected $db_host;
    protected $db_user;
    protected $db_pass;
    protected $db_port;
    protected $db_socket;
    protected $db_name;
    protected $db_collate;

    /* @var $mysqli mysqli */
    protected $mysqli;

    public $query_list = [];
    public $last_query;

    public $debug_mode = false;

    public function __construct()
    {
        include_once 'DbExeption.php';
        include_once 'DbResult.php';
    }

    public function connect($db_config)
    {
        $this->db_host      = $db_config['host'];
        $this->db_user      = $db_config['user'];
        $this->db_pass      = $db_config['password'];
        $this->db_collate   = $db_config['password'];
        $this->db_socket    = ini_get('mysqli.default_socket');

        if (empty($db_config['port'])) {

            $this->db_port = ini_get('mysqli.default_port');
        } else {

            $this->db_port = $db_config['port'];
        }

        if (!is_object($this->mysqli)) {

            $this->mysqli = @new mysqli($this->db_host, $this->db_user, $this->db_pass, null, $this->db_port, $this->db_socket);

            $this->mysqli->set_charset($this->db_collate);

            if ($this->mysqli->connect_error) {

                throw new DbException(__METHOD__ . ': ' . $this->mysqli->connect_error);
            }
        }
    }

    public function setCharset($charset)
    {
        if (!$this->mysqli->set_charset($charset)) {
            throw new DbException(__METHOD__ . ': ' . $this->mysqli->error);
        }
        return $this;
    }

    public function setDatabaseName($database_name)
    {
        if (!$database_name) {

            throw new DbException(__METHOD__ . ': Не указано имя базы данных');
        }

        $this->db_name = $database_name;

        if (!$this->mysqli->select_db($this->db_name)) {

            throw new DbException(__METHOD__ . ': ' . $this->mysqli->error);
        }
        return $this;
    }

    public function getCharset()
    {
        return $this->mysqli->character_set_name();
    }

    public function getDatabaseName()
    {
        return $this->db_name;
    }

    public function getAffectedRows() {

        return $this->mysqli->affected_rows;
    }

    public function getQueryString()
    {
        return $this->last_query;
    }

    public function getQueries()
    {
        return $this->query_list;
    }

    public function getCaller()
    {
        if (! function_exists('debug_backtrace')) {
            return '';
        }
        $stack = debug_backtrace();
        $stack = array_reverse($stack);
        $caller = [];

        foreach ((array)$stack as $call) {

            if (@$call['class'] == __CLASS__) {

                continue;
            }
            $function = $call['function'];

            if (isset($call['class'])) {

                $function = $call['class'] . "->$function";
            }

            $caller[] = ([
                'call_file' => (isset($call['file']) ? $call['file'] : 'Unknown'),
                'call_func' => $function,
                'call_line' => (isset($call['line']) ? $call['line'] : 'Unknown')
            ]);
        }
        return $caller;
    }

    protected function realQuery($query)
    {
        $result = mysqli_query($this->mysqli, $query);
        $this->last_query = $query;
        $this->query_list[] = $query;

        if (is_object($result) && $result instanceof mysqli_result) {

            return new DbResult($result);
        }
        return $result;
    }

    public function multiQuery($query)
    {
        //$id = Profiler::start_timer('dbase','real multiquery: ' . $query);

        if (mysqli_multi_query($this->mysqli, $query)) {
            do {
                if ($result = mysqli_store_result($this->mysqli)) {
                    mysqli_free_result($result);
                }
            } while (mysqli_next_result($this->mysqli));
        }

        $res = new DbResult($result);
        //Profiler::stop_timer($id);
        return $res;
    }

    public function queryMem($table_name, $query, $bind = [], $cache_life_time = null, $cache_id = '') {

        $query_mem = str_replace('#' . $table_name, Core::$config['memory_table_prefix'] . $table_name, $query);
        $query_hard = str_replace('#' . $table_name, $table_name, $query);
        $this->query($query_mem, $bind, $cache_life_time, $cache_id);
        $this->query($query_hard, $bind, $cache_life_time, $cache_id);
    }

    public function bind(&$sql, $bind_arr)
    {
        krsort($bind_arr);

        if ($this->debug_mode) {

            echo '<span class="db_debug" style="display:none"><pre>' . print_r($bind_arr, true) . '</pre></span>';
        }

        foreach ($bind_arr as $key => $val) {

            $sql = str_replace($key, $val, $sql);
        }
    }

    /**
     * Метод, предназначенный для выполнения запроса к MySQL и возвращение результата в виде асоциативного массива с поддержкой кеша
     *
     * @param string $query - текст SQL-запроса
     * @param array $bind
     * @param integer $cache_life_time - время жизни кеша (false без кеша)
     * @param string $cache_id - Id файла кеша
     * @return object                   -
     */
    public function query($query, $bind = [], $cache_life_time = null, $cache_id = '') {

        if ($bind) {

            $this->bind($query, $bind);
        }

        if ($this->debug_mode) {

            echo '<span class="db_debug" style="display:none"><pre>' . $query . '</pre></span>';
        }


        $cache_life_time = ($cache_life_time && is_int($cache_life_time) ? $cache_life_time : null);

        if ($cache_life_time && Core::$config['sql']['use_cache']) {

            $result = [];

            $cache_file = md5($query);
            $cache_id = trim($cache_id);

            $cache_dir = ROOT_DIR . Core::$config['sql']['cache_dir'] . ($cache_id ? $cache_id . DIRECTORY_SEPARATOR : '') .
                substr($cache_file, 0, 2) . DIRECTORY_SEPARATOR .
                substr($cache_file, 2, 2) . DIRECTORY_SEPARATOR .
                substr($cache_file, 4, 2) . DIRECTORY_SEPARATOR;

            if(! file_exists($cache_dir)) {
                mkdir($cache_dir, 0777, true);
            }

            if (
                !(
                    file_exists($cache_dir . $cache_file)
                    &&
                    ($cache_life_time == -1 ? true : @time() - @filemtime($cache_dir . $cache_file) < $cache_life_time)
                )
            ) {
                //$id = Profiler::start_timer('dbase','real query (ttl: ' . $cache_life_time . '): ' . $query);
                $res = $this->realQuery($query);
                $result = [];

                if ($res && $res instanceof DbResult) {
                    while ($mfa = $res->fetchAssocArray()) {
                        $result[] = $mfa;
                    }
                }
                file_put_contents($cache_dir . $cache_file, serialize($result));
            } else {
                $result = unserialize(file_get_contents($cache_dir . $cache_file));
            }

            if (!isset($id)) {

                //$id = Profiler::start_timer('dbase','from cache (ttl: ' . $cache_life_time . '): ' . $query);
            }

            $res =  new DbResult($result);

        } else {
            //$id = Profiler::start_timer('dbase','real query (ttl: ' . $cache_life_time . '): ' . $query);
            $res = $this->realQuery($query);
        }
        //Profiler::stop_timer($id);
        return $res;
    }

    public function escape($value)
    {
        if (!is_numeric($value)) {

            $value = mysqli_real_escape_string($this->mysqli, $value);
        }
        return $value;
    }

    public function escStr($value)
    {
        $value = htmlspecialchars($value);

        $value = strtr($value, [
            '{'         => '&#123;',
            '}'         => '&#125;',
            '$'         => '&#36;',
            '&amp;gt;'     => '&gt;',
            "'"            => "&#39;"
        ]);

        if(!is_array($value)) {

            $value = $this->mysqli->real_escape_string( $value );
        } else {

            $value = array_map([$this, 'escape'], $value );
        }
        return $value;
    }

    public function lastInsertId()
    {
        return (int)mysqli_insert_id($this->mysqli);
    }

    public function getFoundRows()
    {
        $result = $this->query('SELECT FOUND_ROWS();');
        $strRow = $result->fetchArray();
        return (int)$strRow[0];
    }

    /**
     * Метод, предназначенный для возвращения количества всех найденных записей (после запроса типа "SELECT SQL_CALC_FOUND_ROWS * ...")
     *
     * @param $query
     * @param null $cache_life_time
     * @param string $cache_id
     * @return int
     */
    public function numAllRows($query, $cache_life_time = null, $cache_id = '')
    {
        if ($cache_life_time && Core::$config['sql']['use_cache']) {

            $cache_id = trim($cache_id);
            $cache_file = md5($query) . '.count';
            $cache_dir = ROOT_DIR . Core::$config['sql']['cache_dir'] . ($cache_id ? $cache_id . DIRECTORY_SEPARATOR : '') .
                substr($cache_file, 0, 2) . DIRECTORY_SEPARATOR .
                substr($cache_file, 2, 2) . DIRECTORY_SEPARATOR .
                substr($cache_file, 4, 2) . DIRECTORY_SEPARATOR;

            if(!file_exists($cache_dir)) {

                mkdir($cache_dir, 0777, true);
            }

            if (
            !(
                file_exists($cache_dir.$cache_file)
                &&
                ($cache_life_time == -1 ? true : time() - filemtime($cache_dir . $cache_file) < $cache_life_time)
            )
            ) {
                if ($query != $this->last_query) {

                    $res = $this->realQuery($query);
                } else {

                    $res = (int)$this->query("SELECT FOUND_ROWS()")->GetCell();
                    file_put_contents($cache_dir . $cache_file, $res);
                }
                return $res;
            } else {

                return file_get_contents($cache_dir . $cache_file);
            }
        }
        return (int)$this->query("SELECT FOUND_ROWS()")->getCell();
    }

    /**
     * @param void
     * @return Db
     */
    public function close()
    {
        $this->mysqli->close();
        return $this;
    }

    public function mysql_version()
    {
        return @mysqli_get_server_info($this->mysqli);
    }

    public function clearCache($cache_id)
    {
        $cache_id = trim($cache_id);

        if ($cache_id) {

            $cache_dir = ROOT_DIR . Core::$config['sql_cache_dir'] . $cache_id;
            return rmdir($cache_dir);
        } else {

            return false;
        }


    }

}