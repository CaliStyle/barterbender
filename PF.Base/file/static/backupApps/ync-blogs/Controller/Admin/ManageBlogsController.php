<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/01/2017
 * Time: 11:58
 */

namespace Apps\YNC_Blogs\Controller\Admin;

use Phpfox;
use Phpfox_Component;

class ManageBlogsController extends Phpfox_Component
{
    public function process()
    {
        // Page Number & Limit Per Page
        $iPage = $this->search()->getPage();
        $iPageSize = 5;
        $iCount = 0;

        $aVals = array();
        $aConds = array();

        // Search Filter
        Phpfox::getLib('search')->set(array(
            'type' => 'request',
            'search' => 'search',
        ));

        $bIsSearch = false;
        $aSearch = $this->getParam('search');
        if ($aSearch) {
            $aVals['title'] = $aSearch['title'];
            $aVals['author'] = $aSearch['author'];
            $aVals['feature'] = $aSearch['feature'];
            $aVals['category_id'] = $aSearch['category'];
            $aVals['post_status'] = $aSearch['post_status'];
            $bIsSearch = true;
        } else {
            $aVals['title'] = '';
            $aVals['author'] = '';
            $aVals['feature'] = '';
            $aVals['category_id'] = '';
            $aVals['post_status'] = '';
        }

        if ($aVals['title']) {
            $aConds[] = "AND blog.title like '%" . db()->escape($aVals['title']) . "%'";
        }
        if ($aVals['author']) {
            $aConds[] = "AND u.full_name like '%" . db()->escape($aVals['author']) . "%'";
        }
        if ($aVals['category_id'] && $aVals['category_id'] != 0) {
            $aConds[] = "AND acd.category_id = {$aVals['category_id']}";
        }
        if ($aVals['feature']) {
            switch ($aVals['feature']) {
                case 'featured':
                    $aConds[] = "AND blog.is_featured = 1";
                    break;
                case 'not_featured':
                    $aConds[] = "AND blog.is_featured = 0";
                    break;
            }
        }
        if ($aVals['post_status']) {
            switch ($aVals['post_status']) {
                case 'approved':
                    $aConds[] = "AND blog.is_approved = 1 AND blog.post_status = 'public'";
                    break;
                case 'denied':
                    $aConds[] = "AND blog.is_approved = 0 AND blog.post_status = 'denied'";
                    break;
                case 'draft':
                    $aConds[] = "AND blog.post_status = 'draft'";
                    break;
                case 'pending':
                    $aConds[] = "AND blog.is_approved = 0 AND blog.post_status = 'public'";
                    break;
            }
        }

        $aList = Phpfox::getService('ynblog.blog')->getManageBlog($aConds, $iPage, $iPageSize, $iCount);

        // Set pager
        Phpfox::getLib('pager')->set(array(
            'page' => $iPage,
            'size' => $iPageSize,
            'count' => $iCount,
            'ajax' => 'ynblog.changePageManageBlog',
            'popup' => true,
        ));

        $corePath = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/ync-blogs';

        $this->template()->setTitle(_p('manage_blogs'))->setBreadCrumb(_p('manage_blogs'));
        $this->template()->assign(array(
            'aList' => $aList,
            'aCategories' => Phpfox::getService('ynblog.category')->getForAdmin(),
            'aForms' => $aVals,
            'corePath' => $corePath,
            'sUrl' => $this->url()->makeUrl('admincp.ynblog.manageblogs'),
            'bIsSearch' => $bIsSearch,
        ));
    }
}
