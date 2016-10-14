<?php

class AboutController extends Yaf_Controller_Abstract
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


    /*
    * 关于我
    */
    public function indexAction()
    {
        $blog = $this->blogModel->blogInfo(1);
        $arr = [];
        $commentList = $this->commentModel->blogComments(1);
        foreach ($commentList as $value) {
            if ($value['replyid'] <= 0) {
                $arr[$value['id']] = $value;
            } else {
                $arr[$value['replyid']]['reply'][] = $value;
            }
        }

        $this->blogModel->setLook(1);
        $blogType = $this->blogTypeModel->getblogType($blog['type']);
        $blogTag = $this->tagModel->blogToTags(1);
        $commentNums = $this->commentModel->countComment(1);
        $blog['look'] = $blog['look'] + $this->blogModel->getLook(1);
//        $this->rightPublic();
//        $data = [
//            'commentNums' => $commentNums,
//            'blogType' => $blogType,
//            'blogTag' => $blogTag,
//            'comments' => $arr,
//            'blog' => $blog,
//        ];
//        echo json_encode($data);
        $this->getView()->assign("commentNums", $commentNums);
        $this->getView()->assign("blogType", $blogType);
        $this->getView()->assign("blogTag", $blogTag);
        $this->getView()->assign("comments", $arr);
        $this->getView()->assign("blog", $blog);
        $this->getView()->display('about/index.phtml');
        return true;

    }

//    public function rightPublic()
//    {
//        $types = $this->blogTypeModel->allType();
//        $tags = $this->tagModel->hotTags(10);
//        $links = $this->linkModel->linkList();
//        if (getLoginStatus()) {
//            $this->getView()->assign('admin', true);
//        }
//        $this->getView()->assign('links', $links);
//        $this->getView()->assign('tags', $tags);
//        $this->getView()->assign('types', $types);
//        return true;
//    }

}