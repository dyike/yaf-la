<?php

/**
 * blog模型
 */
class BlogModel
{
    public function __construct()
    {
        $this->db = Yaf_Registry::get('db');
        $this->redis = Yaf_Registry::get('redis');

    }


    /**
     * blog详情
     * @param  [type]  $blogId  [description]
     * @param  boolean $content [description]
     * @return [type]           [description]
     */
    public function blogInfo($blogId, $content = true)
    {
        $key = cachekey(__FUNCTION__, $blogId);
        $data = $this->redis->hget(__CLASS__, $key);
        if (is_bool($data)) {
            $sql = "select * from blog where id = {$blogId}";
            $data = $this->db->getOne($sql);
            $this->redis->hset(__CLASS__, $key, $data);
        }

        if ($content) {
            $data['content'] = $this->blogContent($blogId);
        }

        return $data;
    }

    /**
     * blog内容
     * @param  [type] $blogId [description]
     * @return [type]         [description]
     */
    public function blogContent($blogId)
    {
        $key = cachekey(__FUNCTION__, $blogId);
        $data = $this->redis->hget(__CLASS__, $key);
        if (is_bool($data)) {
            $sql = "select content from content where blogid = {$blogId}";
            $data = $this->db->getOne($sql, "content");
            $this->redis->hset(__CLASS__, $key, $data);
        }
        return $data;
    }


    public function blogCount($blogType = 0, $childTypes= '')
    {
        $key = cachekey(__FUNCTION__, $blogType);
        $data = $this->redis->hget(__CLASS__, $key);
        if (is_bool($data)) {
            $where = '';
            if ($childTypes) {
                $where = "and type in ({$blogType}, {$childTypes})";
            } else {
                if ($blogType > 0) {
                    $where = "and type = ".$blogType;
                }
            }
            $sql = "select count(*) as c from blog where status > 0 and id > 1 {$where}";
            $data = $this->db->getOne($sql, 'c');
            $this->redis->hset(__CLASS__, $key, $data);
        }
        return $data;
    }

    /**
     * 获取博客列表 -1 删除, 0 草稿, 1正常, 2置顶
     */
    public function blogList($offset = 20, $limit = 0, $blogType = 0, $childType = '')
    {
        $key = cachekey(__FUNCTION__, [$offset, $limit, $blogType]);
        $data = $this->redis->hget(__CLASS__, $key);

        if (is_bool($data)) {
            $where = '';
            if ($childType) {
                $where = "and type in ({$blogType}, {$childType})";
            } else {
                if ($blogType > 0) {
                    $where = "and type =".$blogType;
                }
            }
            $sql = "select * from blog where status > 0 and id > 1 {$where} order by status desc, id desc, limit {$limit}, {$offset}";
            $data = $this->db->getAll($sql);
            $this->redis->hset(__CLASS__, $key, $data);
        }
        return $data;
    }


    /**
     * 获取数据库中的阅读量
     * @param $blogId
     * @return int
     */
    public function getLook($blogId)
    {
        $key = cachekey($blogId);
        $data = $this->redis->hget("bloglook", $key);

        if (is_bool($data)) {
            $data = 0;
            $this->redis->hset("bloglook", $key, $data);
        }
        return $data;
    }

    public function setLook($blogId)
    {
        $key = cachekey($blogId);
        $data = $this->redis->hget("bloglook", $key);
        if (is_bool($data)) {
            $data = 1;
            $this->redis->hset("bloglook", $key, $data);
        } else {
            $this->redis->hset("bloglool", $key, $data + 1);
            if (($data + 1)%10 == 0) {
                $this->db->update("blog", ['look' => "look+10"], 'id = '.$blogId);
            }
        }
    }
}