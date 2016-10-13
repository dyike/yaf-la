<?php

//生成缓存key
function cachekey($functionName, $parm = '')
{
    if ($parm) {
        if (is_array($parm)) {
            $tail = '';
            foreach ($parm as $value) {
                $tail .= "_" . $value;
            }
            $key = $functionName . $tail;
        } else {
            $key = $functionName . "_" . $parm;
        }

    } else {
        $key = $functionName;
    }
    return $key;
}

//分页
function page($url, $page, $count, $offset = 20, $status = '')
{
    //总页数
    $allPage = ceil($count / $offset);

    if ($status) {
        $status = '&'.$status;
    }
    $first = "<a href='".$url.'?page=1'.$status."'>首页</a>";
    $last = "<a href='".$url.'?page='.$allPage.$status."'>末页</a>";
    if ($page > 1)
    {
        $prePage = "&nbsp;<a href='".$url.'?page='.($page - 1).$status."'>上一页</a>&nbsp;";
    } else {
        $prePage = '&nbsp;&nbsp;';
    }

    if ($page < $allPage) {
        $nextPage = "&nbsp;<a href='".$url.'?page='.($page + 1).$status."'>下一页</a>&nbsp;";
    } else {
        $nextPage = '&nbsp;&nbsp;';
    }

    $page = "当前 第<span style='color:gray'>{$page}</span>&nbsp;页";
    $all = "共 ".$count." 篇&nbsp;&nbsp;共 ".$allPage." 页&nbsp;&nbsp;&nbsp;&nbsp";
    $html = $all.$first.$prePage.$page.$nextPage.$last;
    return $html;
}


//获取登陆状态
function getLoginStatus()
{
    $session = Yaf_Session::getInstance();
    if ($session->admin) {
        return true;
    } else {
        return false;
    }
}
