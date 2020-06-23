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
            $sFromTime = $this->getParam('js_start_time__datepicker', $aSearch['from_time']);
            if ($sFromTime) {
                $aVals['from_time'] = $sFromTime;
            }
            $sEndTime = $this->getParam('js_end_time__datepicker', $aSearch['end_time']);
            if ($sEndTime) {
                $aVals['end_time'] = $sEndTime;
            }

            \Phpfox::getLog('dev.log')->info($sFromTime);
            \Phpfox::getLog('dev.log')->info($sEndTime);
            $bIsSearch = true;
        }

        if (!empty($aVals['title'])) {
            $aConds[] = "AND blog.title like '%" . db()->escape($aVals['title']) . "%'";
        }
        if (!empty($aVals['author'])) {
            $aConds[] = "AND u.full_name like '%" . db()->escape($aVals['author']) . "%'";
        }
        if (!empty($aVals['from_time'])) {
            $aVals['from_time'] = Phpfox::getService('ynblog.helper')->time_to_timestamp_begin($aVals['from_time']);
            $aConds[] = "AND blog.time_stamp >= {$aVals['from_time']}";
        }
        if (!empty($aVals['end_time'])) {
            $aVals['end_time'] = Phpfox::getService('ynblog.helper')->time_to_timestamp_end($aVals['end_time']);
            $aConds[] = "AND blog.time_stamp <= {$aVals['end_time']}";
        }

        $aList = Phpfox::getService('ynblog.blog')->getCoreBlog($aConds, $iPage, $iPageSize, $iCount);

        // Set pager
        Phpfox::getLib('pager')->set(array(
            'page' => $iPage,
            'size' => $iPageSize,
            'count' => $iCount,
            'ajax' => 'ynblog.changePageImportCoreBlog',
            'popup' => true,
        ));

        $corePath = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/ync-blogs';

        $this->template()->setTitle(_p('manage_blogs'))->setBreadCrumb(_p('manage_blogs'));
        $this->template()->assign(array(
            'aList' => $aList,
            'aForms' => $aVals,
            'aCategories' => Phpfox::getService('ynblog.category')->getForAdmin(),
            'corePath' => $corePath,
            'sUrl' => $this->url()->makeUrl('admincp.ynblog.manageblogs'),
            'bIsSearch' => $bIsSearch,
        ));
    }
}