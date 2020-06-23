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

        $aConds = [];

        $search = $this->request()->get('search');
        $isSearch = !empty($search);

        if ($search['title']) {
            $aConds[] = "AND blog.title like '%" . db()->escape($search['title']) . "%'";
        }
        if ($search['author']) {
            $aConds[] = "AND u.full_name like '%" . db()->escape($search['author']) . "%'";
        }
        if ($search['category'] && $search['category'] != 0) {
            $aConds[] = "AND acd.category_id = {$search['category']}";
        }
        if ($search['feature']) {
            switch ($search['feature']) {
                case 'featured':
                    $aConds[] = "AND blog.is_featured = 1";
                    break;
                case 'not_featured':
                    $aConds[] = "AND blog.is_featured = 0";
                    break;
            }
        }
        if ($search['post_status']) {
            switch ($search['post_status']) {
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
        ));

        $corePath = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/ync-blogs';

        $this->template()->setTitle(_p('manage_blogs'))->setBreadCrumb(_p('manage_blogs'));
        $this->template()->assign(array(
            'aList' => $aList,
            'aCategories' => Phpfox::getService('ynblog.category')->getForAdmin(),
            'aForms' => $search,
            'corePath' => $corePath,
            'sUrl' => $this->url()->makeUrl('admincp.ynblog.manageblogs'),
            'bIsSearch' => $isSearch,
        ));
    }
}
