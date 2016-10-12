<?php
/**
 * 默认控制器
 */
class IndexController extends Yaf_Controller_Abstract
{

    public function init()
    {
        $this->blogModel = new BlogModel();
        $this->tagModel = new TagModel();
        $this->blogTypeModel = new BlogTypeModel();
        $this->commentModel = new CommentModel();
    }

    public function indexAction()
    {
        $typeId = $this->getRequest()->get("typeid");
        $tagId = $this->getRequest()->get("tagid");

        $page = $this->getRequest()->get("page");
        $page  = $page > 0 ? $page : 1;
        $url = BASE_URL.'index/index/';
        $offset = 16;
        $limit = ($page - 1) * $offset;

        $status = "";
        if ($typeId > 0) {
            $status = 'typeid = '.$typeId;
        } else {
            $typeId = 0;
        }

        if ($tagId > 0) {
            $status = 'tagid = '.$tagId;

            $blogs = $this->tagModel->tagJoin($tagId);

            $count = count($blogs);
            $list = [];
            $blogs = array_slice($blogs, $limit, $offset);
            foreach ($blogs as $value) {
                $list[] = $this->blogMdoel->blogInfo($value['blogid'], false);
            }
        } else {
            $tagId = 0;
            $childTypes = $this->blogTypeModel->getChildByParent($typeId);
            $count = $this->blogModel->blogCount($typeId, $childTypes);
            $list = $this->blogModel->blogList($offset, $limit, $typeId, $childTypes);
        }
        $pageHtml = page($url, $page, $count, $offset, $status);

        foreach ($list as $key => $value) {
            $list[$key]['comments'] = $this->commentModel->countComment($value['id']);
            $list[$key]['typesrr'] = $this->blogTypeModle->getBlogType($value['type']);
            $list[$key]['look'] = $list[$key]['look'] + $this->blogModel->getLook($value['id']);
        }

        $this->rightPublic();
        $this->getView()->assign("list", $list);
        $this->getView()->assign("page", $pageHtml);
    }




    public function rightPublic()
    {
        $types = $this->blogTypeModel->allTypes();
        $tags = $this->tagModel->hotTags(10);
    }
}

