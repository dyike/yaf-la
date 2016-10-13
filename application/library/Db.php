<?php

class Db
{
    private $link;   //连接标识符
    private $PDOStatement;  //保存PDOStatement对象
    private $lastInsertId;
    private $trans;   //事务管理

    public function __construct($dbConfig)
    {
        $this->PDOStatement = null;
        if (!class_exists("PDO")) {
            $this->halt('不支持PDO,请先开启');
        }
        $this->connect($dbConfig['hostname'], $dbConfig['username'], $dbConfig['password'], $dbConfig['database']);
    }


    /*
     * 数据库连接
     */
    public function connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect = false)
    {
        $dns = "mysql:host=".$dbhost.";"."dnname=".$dbname;

        try {
            if ($pconnect) {
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

    public function query($sql)
    {
        $link = $this->link;
        if (!$link) {
            return false;
        }
        //判断是否有结果集,如果有的话就释放
        if (!empty($this->PDOStatement)) {
            $this->PDOStatement = null;
        }
        $this->PDOStatement = $link->prepare($sql);
        $res = $this->PDOStatement->execute();
        return $res;
    }

    /*
     * 获取所有记录
     */
    public function getAll($sql = null)
    {
        if ($sql != null) {
            $this->query($sql);
        }
        $res =$this->PDOStatement->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }

    /*
     * 获取一条记录
     */
    public function getOne($sql = null, $fields ='')
    {
        if ($sql != null) {
            $this->query($sql.'limit 1');
        }
        $res = $this->PDOStatement->fetch(PDO::FETCH_ASSOC);
        if ($fields && array_key_exists($fields, $res)) {
            $res = $res[$fields];
        }
        return $res;
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
            $field .= "$key,";
            $value .= "$val,";
        }
        $field = substr($field, 0, -1);
        $value = substr($value, 0, -1);
        $sql = "insert into {$table} ($field) values($value)";
        return $this->execute($sql);
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
                $value .= $key ."=" .$key.$tmpVal.",";
            } else {
                $value .= "$key = " .$val .",";
            }
        }
        $value = substr($value, 0, -1);
        $sql = "update {$table} set {$value} where 1=1 and $condition";
        return $this->execute($sql);
    }

    public function delete($table, $condition = '')
    {
        if (empty($condition)) {
            $this->halt("没有设置删除条件");
            return false;
        }
        $sql = "delete from {$table} where 1=1 and $condition";
        return $this->execute($sql);
    }


    public function getLastInsertId()
    {
        $link = $this->link();
        if (!$link) {
            return false;
        }
        return $link->lastInsertId();
    }


    /*
     * 执行增删改操作,返回受影响的记录条数
     */
    public function execute($sql)
    {
        $link = $this->link;
        if (!$link) {
            return false;
        }

        if (!empty($this->PDOStatement)) {
            $this->PDOStatement = null;
        }
        $res = $link->exec($sql);
        if ($res) {
            $this->lastInsertId = $link->lastInsertId();
            return $res;
        } else {
            return false;
        }

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