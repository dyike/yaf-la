<?php

class CommentModel
{
    public function __construct()
    {
        $this->db = Yaf_Registry::get('db');
        $this->redis = Yaf_Registry::get('redis');
    }


    /**
     * 获取blog的客户评论的数量
     * @param $blogId
     */
    public function countComment($blogId)
    {
        $key = cachekey(__FUNCTION__, $blogId);
        $data = $this->redis->hget(__CLASS__, $key);
        if (is_bool($data)) {
            $sql = "select count(*) as c from comment where blogid = {$blogId} and replyid <= 0";
            $data = $this->db->getOne($sql, 'c');
            $this->redis->hset(__CLASS__, $key, $data);
        }
        return $data;
    }
}