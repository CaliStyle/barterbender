<?php 
;

if (Phpfox::isModule('directory') && isset($aCallback) && isset($aCallback['item_id']) && $aCallback['module_id'] == 'directory')
{
    list($aBreadCrumbs, $aBreadCrumbTitle) = $this->template()->getBreadCrumb();
    $oldLink = $this->url()->makeUrl('pages.' . $aCallback['item_id'] .'.blog');
    if(isset($aBreadCrumbs[$oldLink])){
        if ($aCallback = Phpfox::callback($aItem['module_id'] . '.getBlogDetails', $aItem)){
            $newLink = $aCallback['url_home_photo'];
            $this->template()->clearBreadCrumb();
            $this->template()->setBreadcrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
            $this->template()->setBreadcrumb(Phpfox::getLib('parse.output')->shorten($aCallback['title'],50, '...'), $aCallback['url_home']);
            $this->template()
                ->setBreadCrumb(_p('blog.blogs_title'), $newLink)
                ->setBreadCrumb($aItem['title'], $this->url()->permalink('blog', $aItem['blog_id'], $aItem['title']), true)
                ;
        }
    }
}

;
?>