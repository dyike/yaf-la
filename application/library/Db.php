<?php

class Db
{
    private $time;
    private $is_log;   //是否记录日志
    private $handle;
    private $link;     //mmysqli资源句柄
    private $trans;   //事务

    public function __construct($db_config)
    {
        $this->time = $this->microtime();
        $this->connect($db_config['hostname'], $db_config['username'], $db_config['password'], $db_config['database']);
        $this->is_log = $db_config['log'];
        if ($this->is_log) {
            $handle = fopen($db_config['logfilepath'] . "dblog.txt", "a+");
            $this->handle = $handle;
        }
    }


    //数据库连接
    public function connect($dbhost, $dbuser, $dbpw, $dbname, $charset = "utf8")
    {
        $this->link = @mysqli_connect($dbhost, $dbuser, $dbpw, $dbname);
        if ($this->link) {
            $this->halt("数据库连接失败：".mysqli_connect_errno());
        }

        if (!@mysqli_select_db($this->link, $dbname)) {
            $this->halt('数据库选择失败');
        }
        mysql_query($this->link, "set names".$charset);
    }

    //查询
    public function query($sql)
    {
        $this->writeLog("查询" . $sql);
        $query = mysqli_query($this->link, $sql);
        if (!$query) {
            if ($this->trans) {
                $this->transRollBack();
            }
            $this->halt("Query Error:" . $sql);
        }
        return $query;
    }

    //获取一条记录
    //$fields 字段名
    public function getOne($sql, $fields = "")
    {
        $query = $this->query($sql. 'LIMIT 1');
        $res = mysqli_fetch_assoc($query);
        $this->writeLog("获取一条记录");
        if ($fields && array_key_exists($fields, $res)) {
            $res = $res[$fields];
        }
        return $res;
    }

    //获取全部记录
    public function getAll($sql, $resType = MYSQL_ASSOC)
    {
        $query = $this->query($sql);
        $i = 0;
        $res = [];
        while ($row = mysqli_fetch_array($query, $resType)) {
            $res[$i] = $row;
            $i++;
        }
        $this->writeLog("获取全部记录");
        return $res;
    }

    //插入数据
    public function insert($table, $data)
    {
        $field = '';
        $value = '';
        if (!is_array($data) || empty($data)) {
            $this->halt("没有要插入的数据");
            return false;
        }
        while (list($key, $val) = each($data)) {
            $field .= "$key,";
            $value .= "$val,";
        }
        $field = substr($field, 0, -1);
        $value = substr($value, 0, -1);
        $sql = "insert into $table($field) values($value)";
        $this->writeLog("插入");
        if (!$this->query($sql)) {
            return false;
        }
        return true;
    }

    //更新数据
    public function update($table, $data, $condition = "")
    {
        if (!is_array($data) || empty($data)) {
            $this->halt('没有要更新的数据');
            return false;
        }
        $value = '';
        while (list($key, $val) = each($data)) {
            $tmpKey = substr(trim($val), 0, strlen($val));

            if ($tmpKey == $key) {
                $tmpVal = trim(str_replace($key, '', trim($val)));
                $value .= $key ."=" .$key.$tmpVal.",";
            } else {
                $value .= "$key = " .$val .",";
            }
        }
        $value = substr($value, 0, -1);
        $sql = "update {$table} set {$value} where 1=1 and $condition";
        $this->writeLog("更新");
        if (!$this->query($sql)) {
            return false;
        }
        return true;
    }

    //删除数据
    public function delete($table, $condition = '')
    {
        if (empty($condition)) {
            $this->halt("每一设置删除条件");
            return false;
        }
        $sql = "delete from {$table} where 1=1 and $condition";
        $this->writeLog("删除". $sql);
        if (!$this->query($sql)) {
            return false;
        }
        return true;
    }

    //获取记录条数
    public function countRows($res)
    {
        if (!is_bool($res)) {
            $num = mysqli_num_rows($res);
            $this->writeLog("获取的记录条数为". $num);
            return $num;
        } else {
            return 0;
        }
    }

    //获取最后一条插入的id
    public function getInsertId()
    {
        $id = mysqli_insert_id($this->link);
        $this->writeLog("最后插入的id为".$id);
        return $id;
    }

    //错误提示
    public function halt($msg = '')
    {
        $msg .= "\r\n" . mysqlo_errno($this->link);
        $this->writeLog($msg);
        die($msg);
    }

    //开启事务
    public function transStart()
    {
        $this->writeLog("开启事务");
        $this->trans = TRUE;
        mysqli_autocommit($this->link, FALSE);
    }

    //事务回滚
    public function transRollBack()
    {
        $this->writeLog("事务回滚");
        mysqli_rollback($this->link);
        mysqli_autocommit($this->link, TRUE);
        $this->trans = FALSE;
    }

    //事务结束
    public function transFinish()
    {
        $this->writeLog("提交事务");
        mysqli_commit($this->link);
        mysqli_autocommit($this->link, TRUE);
        $this->trans = FALSE;
    }


    //关闭数据库
    public function close()
    {
        $this->writeLog("已关闭数据库连接");
        return @mysqli_close($this->link);
    }



    //写入日志文件
    public function writeLog($msg = '')
    {
        if ($this->is_log) {
            $text = date("Y-m-d H:i:s") . " " . $msg . "\r\n";
            fwrite($this->handle, $text);
        }
    }

}