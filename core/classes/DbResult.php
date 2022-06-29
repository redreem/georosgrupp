<?php

class DbResult
{
    /**
     * @var mysqli_result
     */
    public $result = null;

    /**
     * @param $result mysqli_result
     */
    public function __construct($result)
    {
        $this->result = $result;
    }

    /**
     * @return array
     */
    public function fetchArray()
    {
        if (is_array($this->result)) {

            $a = current($this->result);
            next($this->result);
            $b = [];

            if(!is_array($a)) {
                return false;
            }
            foreach($a as $k => $v) {
                $b[] = $v;
            }
            return array_merge($b, $a);
        }
        return @mysqli_fetch_array($this->result);
    }

    /**
     * @return array
     */
    public function fetchAssocArray()
    {
        if(is_array($this->result)) {

            $a = current($this->result);
            next($this->result);
            return $a;
        }
        return @mysqli_fetch_assoc($this->result);
    }

    /**
     * @return object
     */
    public function fetchRow()
    {
        if(is_array($this->result)) {

            $a = $this->fetchAssocArray();

            if (is_array($a)) {

                $obj = new StdClass();

                foreach ($a as $key => $val){
                    $obj->$key = $val;
                }
            } else {
                $obj = $a;
            }

            return $obj;
        }
        return @mysqli_fetch_object($this->result);
    }

    /**
     * @return mixed
     */
    public function getCell()
    {
        if(is_array($this->result)) {

            $a = current($this->result);

            if(is_array($a)){
                return current($a);
            } else {
                return false;
            }
        }

        if ($this->numRows()) {

            $a = @mysqli_fetch_row($this->result);
            return $a[0];
        }
        return false;
    }

    /**
     * @return int
     */
    public function numRows()
    {
        if(is_array($this->result)) {

            return (int)count($this->result);
        }
        return (int)@mysqli_num_rows($this->result);
    }

    /**
     * @return int
     */
    public function numFields()
    {
        if(is_array($this->result)) {

            $a = current($this->result);
            return count($a);
        }
        return (int)mysqli_num_fields($this->result);
    }

    public function close()
    {
        @mysqli_free_result($this->result);
         return true;
    }

    /**
     * @internal param $void
     * @return resource
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Удаляем объект
     */
    public function __destruct()
    {
        $this->close();
    }
}