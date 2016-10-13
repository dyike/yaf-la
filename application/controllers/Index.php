<?php
/**
 * 默认控制器
 */
class IndexController extends Yaf_Controller_Abstract
{

    private $blogModel;
    private $tagModel;
    private $commentModel;
    private $linkModel;
    private $blogTypeModel;

    public function init()
    {
        $this->blogModel = new BlogModel();
        $this->tagModel = new TagModel();
        $this->blogTypeModel = new BlogTypeModel();
        $this->commentModel = new CommentModel();
        $this->linkModel = new LinkModel();
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
        //分页处理
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
        $types = $this->blogTypeModel->allType();
        $tags = $this->tagModel->hotTags(10);
        $links = $this->linkModel->linkList();
        if (1) {
            $this->getView()->assign('admin', true);
        }
        $this->getView()->assign('links', $links);
        $this->getView()->assign('tags', $tags);
        $this->getView()->assign('types', $types);

        return true;
    }


    public function aboutAction()
    {
        $blog = $this->blogModel->blogInfo(1);
        $commentList = $this->commentModel->blogComments(1);

        $arr = [];
        foreach ($commentList as $value) {
            if ($value['replyid'] <= 0) {
                $arr[$value['id']] = $value;
            } else {
                $arr[$value['replyid']]['reply'][] = $value;
            }
        }

        $this->blogModel->setLook(1);

    }


}

