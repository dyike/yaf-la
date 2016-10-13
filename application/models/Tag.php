<?php

/**
 * 标签
 */
class TagModel
{
    public function __construct()
    {
        $this->db = Yaf_Registry::get('db');
        $this->redis = Yaf_Registry::get('redis');
    }


    public function tagJoin($tagId)
    {
        $sql = "select blogid from blogtag where tagid = {$tagId} group by blogid";
        $res = $this->db->getAll($sql);
        return $res;
    }


    /**
     * 热门的标签
     */
    public function hotTags($num = 5)
    {
        $key = cachekey(__FUNCTION__, $num);
        $data = $this->redis->hget(__CLASS__, $key);
        if (is_bool($data)) {
            $data = [];
            $sql = "select tagid from blogtag group by tagid order by count(*) desc limit" .$num;
            $res = $this->db->getAll($sql);
            foreach ($res as $value) {
                $data[$value['tagid']] = $this->tagName($value['tagid']);
            }
            $this->redis->hset(__CLASS__, $key, $data);
        }

        return $data;
    }


    /**
     * 获取tag的名称
     */
    public function tagName($tagId)
    {
        $allTags = $this->allTags();
        return $allTags[$tagId]['name'];
    }

    /**
     * 获取所有的标签和id
     */
    public function allTags()
    {
        $key = cachekey(__FUNCTION__);
        $data = $this->redis->hget(__CLASS__, $key);
        if (is_bool($data)) {
            $data = [];
            $sql = "select * from tag";
            $res = $this->db->getAll($sql);
            foreach ($res as $value) {
                $data[$value['id']] = $value;
            }
            $this->redis->hset(__CLASS__, $key, $data);
        }
        return $data;
    }
}