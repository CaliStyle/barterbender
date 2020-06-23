<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/01/2017
 * Time: 18:28
 */

namespace Apps\YNC_Blogs\Controller\Admin;

use Phpfox;
use Phpfox_Component;

class ImportCoreBlogController extends Phpfox_Component
{
    public function process()
    {
        // Page Number & Limit Per Page
        $iPage = $this->search()->getPage();
        $iPageSize = (int) setting('yn_advblog_size_import_blog', 10);
        if ($iPageSize <= 0) {
            $iPageSize = 10;
        }
        $iCount = 0;
        $aConds = array();

        // Search Filter
        Phpfox::getLib('search')->set(array(
            'type' => 'request',
            'search' => 'search',
        ));

        $requestObject = $this->request();
        $bIsSearch = false;
        $search = $requestObject->get('search') ? $requestObject->get('search') : [];
        $vals = $requestObject->get('val');

        $initStartTime = strtotime(date('m/d/Y',(strtotime(date('m/d/Y') . '23:59:59')) - 7 * 86400) . ' 00:00:00');

        $search = array_merge($search, [
            'start_time_month' => !empty($vals['start_time_month']) ? $vals['start_time_month'] : date('m', $initStartTime),
            'start_time_day' => !empty($vals['start_time_day']) ? $vals['start_time_day'] : date('d', $initStartTime),
            'start_time_year' => !empty($vals['start_time_year']) ? $vals['start_time_year'] : date('Y', $initStartTime),
            'end_time_month' => !empty($vals['end_time_month']) ? $vals['end_time_month'] : date('m'),
            'end_time_day' => !empty($vals['end_time_day']) ? $vals['end_time_day'] : date('d'),
            'end_time_year' => !empty($vals['end_time_year']) ? $vals['end_time_year'] : date('Y'),
        ]);

        
        if (!empty($search['title'])) {
            $aConds[] = "AND blog.title like '%" . db()->escape($search['title']) . "%'";
        }
        if (!empty($search['author'])) {
            $aConds[] = "AND u.full_name like '%" . db()->escape($search['author']) . "%'";
        }

        $startTime = Phpfox::getLib('date')->mktime(0, 0, 0, $search['start_time_month'], $search['start_time_day'], $search['start_time_year']);
        $aConds[] = "AND blog.time_stamp >= " . $startTime;
        $endTime = Phpfox::getLib('date')->mktime(23, 59, 59, $search['end_time_month'], $search['end_time_day'], $search['end_time_year']);
        $aConds[] = "AND blog.time_stamp <= " . $endTime;

        $aList = Phpfox::getService('ynblog.blog')->getCoreBlog($aConds, $iPage, $iPageSize, $iCount);

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
            'aForms' => $search,
            'aCategories' => Phpfox::getService('ynblog.category')->getForAdmin(),
            'corePath' => $corePath,
            'sUrl' => $this->url()->makeUrl('admincp.ynblog.manageblogs'),
            'bIsSearch' => !empty($search),
        ));
    }
}