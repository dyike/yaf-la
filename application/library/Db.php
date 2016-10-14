<?php

class Db
{
    private $link;   //连接标识符
    private $trans;   //事务管理

    public function __construct($dbConfig)
    {
        $this->PDOStatement = null;
        if (!class_exists("PDO")) {
            $this->halt('不支持PDO,请先开启');
        }
        $this->connect($dbConfig['hostname'], $dbConfig['username'], $dbConfig['password'], $dbConfig['database'], $dbConfig['pconnect']);
    }


    /*
     * 数据库连接
     */
    public function connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect)
    {
        $dns = "mysql:host=".$dbhost.";"."dbname=".$dbname;

        try {
            if ($pconnect == 1) {
                $this->link = new PDO($dns, $dbuser, $dbpw, [PDO::ATTR_PERSISTENT => true]);
            } else {
                $this->link = new PDO($dns, $dbuser, $dbpw);
            }

        } catch (PDOException $e) {
            $this->halt("数据库连接失败:".$e->getMessage());
        }

        if (!$this->link) {
            $this->halt("PDO连接错误");
            return false;
        }
        $this->link->exec("set names uft8");

    }

    /*
     * 执行一条sql
     */
    public function query($sql, $type = null)
    {
        $res = $this->link->prepare($sql);
        $res->execute();
        if (is_null($type)) {
            return $res;
        } elseif ($type == 1) {
            return $res->rowCount();
        } elseif ($type == 2) {
            return $this->link->lastInsertId();
        }
    }

    /*
     * 获取所有记录
     */
    public function getAll($sql)
    {
        $res = $this->query($sql);
        $result = $res->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    /*
     * 获取一条记录
     */
    public function getOne($sql, $fields ='')
    {
        $res = $this->query($sql.' limit 1');
        $result = $res->fetch(PDO::FETCH_ASSOC);
        if ($fields && array_key_exists($fields, $result)) {
            $result = $result[$fields];
        }
        return $result;
    }

    /*
     * 插入记录
     */
    public function insert($table, $data)
    {
        $field = '';
        $value = '';
        if (!is_array($data) || empty($data)) {
            $this->halt("没有要插入的数据");
            return false;
        }
        while (list($key, $val) = each($data)) {
            $field .= $key.',';
            $value .= "'".$val."',";
        }
        $fields = substr($field, 0, -1);
        $values = substr($value, 0, -1);
        $sql = "insert into {$table} ($fields) values($values)";
        return $this->query($sql, 2);
    }

    /*
     * 更新操作
     */
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
                $value .= $key." = '".$key.$tmpVal."',";
            } else {
                $value .= "$key = '".$val."',";
            }
        }
        $values = substr($value, 0, -1);
        $sql = "update {$table} set {$values} where 1=1 and $condition";
        return $this->query($sql, 1);
    }

    /*
     * 删除操作
     */
    public function delete($table, $condition = '')
    {
        if (empty($condition)) {
            $this->halt("没有设置删除条件");
            return false;
        }
        $sql = "delete from {$table} where 1=1 and $condition";
        return $this->query($sql, 1);
    }



    //开启事务
    public function transStart()
    {
        $this->trans = TRUE;
        $this->link->beginTransaction();
    }

    //事务回滚
    public function transRollBack()
    {
        $this->link->rollBack();
        $this->trans = FALSE;
    }

    //事务结束
    public function transFinish()
    {
        $this->link->commit();
        $this->trans = FALSE;
    }

    /*
     * 关闭数据库连接
     */
    public function close()
    {
        return $this->link = null;
    }

    /**
     * 自定义错误提示
     */
    public function halt($msg = '')
    {
        $msg .= "\r\n" . $this->link->errorCode();
        die($msg);
    }
}