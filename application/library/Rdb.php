<?php

class Rdb
{
    //服务器连接句柄
    private $handle;

    public function __construct($config)
    {
        $this->connect($config);
    }


    public function __destruct()
    {
        $this->close();
    }

    /**
     * 使用长连接,但不会主动关闭
     * @param $config
     */
    public function connect($config)
    {
        if (!isset($config['port'])) {
            $config['port'] = 6379;
        }
        $this->handle = new Redis();
        $res = $this->handle->connect($config['host'], $config['port']);
        return $res;
    }

    //关闭连接
    public function close()
    {
        $this->handle->close();
        return true;
    }

    //得到redis
    public function getRedis()
    {
        return $this->handle;
    }

    /**
     * 写缓存
     * @param $key
     * @param $value
     * @param int $expire 0:表示无过期时间
     */
    public function set($key, $value, $expire = 0)
    {
        //永不超时
        if ($expire == 0) {
            $res = $this->handle->set($key, $value);
        } else {
            $res = $this->handle->setex($key, $expire, $value);
        }
        return $res;
    }

    /**
     * 读缓存
     * @param $key
     */
    public function get($key)
    {
        //是否一次读多个值
        $options = is_array($key) ? 'mGet' : 'get';
        return $this->handle->{$options}($key);
    }

    /**
     * 条件形式设置缓存,如果key不存在就设置,存在时设置失败
     * @param $key
     * @param $value
     * @return mixed
     */
    public function setnx($key, $value)
    {
        return $this->handle->setnx($key, $value);
    }

    /**
     * 删除缓存
     * @param $key
     * @return mixed
     */
    public function remove($key)
    {
        return $this->handle->delete($key);
    }

    /**
     * 值加加操作,类似++$i, 如果key不存在时自动设置为0后进行加加操作
     * @param $key
     * @param int $default
     * @return mixed
     */
    public function incr($key, $default = 1)
    {
        if ($default == 1) {
            return $this->handle->incr($key);
        } else {
            return $this->handle->incrBy($key, $default);
        }
    }

    /**
     * 值减减操作,类似--$i, 如果key不存在时自动设置为0后进行减减操作
     * @param $key
     * @param int $default
     * @return mixed
     */
    public function decr($key, $default = 1)
    {
        if ($default == 1) {
            return $this->handle->decr($key);
        } else {
            return $this->handle->decrBy($key, $default);
        }
    }

    /**
     * 清空当前数据库
     * @return mixed
     */
    public function clear()
    {
        return $this->handle-flushDB();
    }

    /**
     * lpush
     * @param $key
     * @param $value
     * @return mixed
     */
    public function lpush($key, $value)
    {
        return $this->handle->lpush($key, $value);
    }


    public function rpush($key, $value)
    {
        return $this->handle->rpush($key, $value);
    }


    public function lpop($key)
    {
        return $this->handle->lpop($key);
    }

    public function lrange($key, $start, $end)
    {
        return $this->handle->lrange($key, $start, $end);
    }


    public function hset($name, $key, $value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        return $this->handle->hset($name, $key, $value);
    }

    public function hget($name, $key = null)
    {
        if ($key) {
            $data = $this->handle->hget($name, $key);
            $value = json_decode($data, true);
            if (is_null($value)) {
                $value = $data;
            }
            return $value;
        }
        return $this->handle->hgetAll($name);
    }


    public function hdel($name, $key = null)
    {
        if ($key) {
            return $this->handle->hdel($name, $key);
        }
        return $this->handle->del($name);
    }

    /**
     * 存普通数据
     * @param string $key
     * @param string $data
     * @param int $time
     */
    public function saveData($key = '', $data = '', $time = 0)
    {
        $res = false;
        if ($key != '' && $data != '') {
            if (is_array($data)) {
                $data = json_encode($data);
            }
            $res = $this->set($key, $data, $time);
        }
        return $res;
    }

    /**
     * 获取value 只允许单个key,不允许数组
     * @param $key
     * @return mixed
     */
    public function getData($key)
    {
        $key = (string)$key;
        $data = $this->get($key);
        $value = json_decode($data, true);
        if (is_null($value)) {
            $value = $data;
        }
        return $value;
    }

}