<?php

class LinkModel
{
    public function __construct()
    {
        $this->db = Yaf_Registry::get('db');
        $this->redis = Yaf_Registry::get('redis');
    }

    /*
     * 获取所有的友情链接
     */
    public function linkList($admin = false)
    {
        $key = cachekey(__FUNCTION__, $admin);
        $data = $this->redis->hget(__CLASS__, $key);
        if (is_bool($data)) {
            if (!$admin) {
                $where = "where status = 1";
            } else {
                $where = '';
            }
            $sql = "select * from link" . $where;
            $data = $this->db->getAll($sql);
            $this->redis->hset(__CLASS__, $key, $data);
        }
        return $data;
    }

    /*
     * 增加友情链接
     */
    public function linkAdd($title, $url)
    {
        $this->redis->remove(__CLASS__);
        $data = ['title' => $title, 'url' => $url, 'created' => date('Y-m-d H:i:s')];
        return $this->db->insert('link', $data);
    }

    /**
     * 更新状态
     * @param $id
     * @param $status
     * @return mixed
     */
    public function updateStatus($id, $status)
    {
        $this->redis->remove(__CLASS__);
        return $this->db->update('link', ['status' => $status], 'id = '.$id);
    }

    /*
     * 删除友情链接
     */
    public function deleteLink($id)
    {
        $this->redis->remove(__CLASS__);
        return $this->db->delete('link', 'id = '.$id);
    }
}