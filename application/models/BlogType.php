<?php

class BlogTypeModel
{
    public function __construct()
    {
        $this->db = Yaf_Registry::get('db');
        $this->redis = Yaf_Registry::get('redis');
    }


    public function getChildByParent($typeId)
    {
        $res = $this->allType();
        $childs = '';
        if (array_key_exists($typeId, $res)) {
            if (array_key_exists('child', $res[$typeId])) {
                $childRes = array_keys($res[$typeId]['child']);
                $childs = implode(',', $childRes);
            }
        }
        return $childs;
    }

    /**
     * 获取blog所有的类型
     * 父类别可以无子类别， 子类别必有父类别
     * @return [type] [description]
     */
    public function allType()
    {
        $key = cachekey(__FUNCTION__);
        $data = $this->redis->hget(__CLASS__, $key);
        if (empty($data)) {
            $sql = "select * from blogtype";
            $res = $this->db->getAll($sql);
            $data = [];

            foreach ($res as $value) {
                if ($value['topid'] == 0) {
                    $data[$value['id']] = ['name' => $value['name']];
                } else {
                    $data[$value['topid']]['child'][$value['id']] = $value['name'];
                }
            }
            $this->redis->hset(__CLASS__, $key, $data);
        }
        return $data;
    }

    public function getBlogType($typeId)
    {
        $key = cachekey(__FUNCTION__);
        $data = $this->redis->hget(__CLASS__, $key);
        if (is_bool($data)) {
            $data = [];
            $sql = "select * from blogtype";
            $res = $this->db->getAll($sql);
            foreach ($res as $value) {
                $data[$value['id']]['name'] = $value['name'];
                $data[$value['id']]['topid'] = $value['topid'];
            }
            $this->redis->hset(__CLASS__, $key, $data);
        }
        $res[0] = $data[$typeId];
        if ($res[0]['topid'] > 0) {
            $res[1] = $data[$res[0]['topid']];
        }
        return $res;
    }
}