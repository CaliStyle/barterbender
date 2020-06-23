<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\YouNet_UltimateVideos\Controller\Admin;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class ManageVideosController extends \Phpfox_Component
{
    public function process()
    {
        // Page Number & Limit Per Page
        $iPage = $this->getParam('page');
        $iPageSize = 5;
        $aVals = array();
        $aConds = array();
        // Search Filter
        $oSearch = Phpfox::getLib('search')->set(array(
            'type' => 'request',
            'search' => 'search',
        ));
        $bIsSearch = false;
        $aSearch = $this->getParam('search');
        if ($aSearch) {
            $aVals['title'] = $aSearch['title'];
            $aVals['owner'] = $aSearch['owner'];
            $aVals['category_id'] = $aSearch['category'];
            $aVals['source'] = $aSearch['source'];
            $aVals['feature'] = $aSearch['feature'];
            $aVals['approve'] = $aSearch['approve'];
            $bIsSearch = true;
        } else {
            $aVals = array(
                'title' => '',
                'owner' => '',
                'category_id' => '',
                'source' => '',
                'feature' => '',
                'approve' => '',
            );
        }
        if ($aVals['title']) {
            $aConds[] = "AND uvid.title like '%{$aVals['title']}%'";
        }
        if ($aVals['owner']) {
            $aConds[] = "AND u.full_name like '%{$aVals['owner']}%'";
        }
        if ($aVals['source']) {
            $aConds[] = "AND uvid.type = {$aVals['source']}";
        }
        if ($aVals['category_id'] && $aVals['category_id'] != 0) {
            $aConds[] = "AND uvid.category_id = {$aVals['category_id']}";
        }
        if ($aVals['feature']) {
            switch ($aVals['feature']) {
                case 'featured':
                    $aConds[] = "AND uvid.is_featured = 1";
                    break;
                case 'not_featured':
                    $aConds[] = "AND uvid.is_featured = 0";
                    break;
            }
        }
        if ($aVals['approve']) {
            switch ($aVals['approve']) {
                case 'approved':
                    $aConds[] = "AND uvid.is_approved = 1";
                    break;
                case 'denied':
                    $aConds[] = "AND uvid.is_approved = 0";
                    break;
            }
        }

        list($iCount, $aList) = Phpfox::getService('ultimatevideo')->getManageVideo($aConds, $iPage, $iPageSize);

        // Set pager
        phpFox::getLib('pager')->set(array(
            'page' => $iPage,
            'size' => $iPageSize,
            'count' => $iCount,
            'ajax' => 'ultimatevideo.changePageManageVideo',
            'popup' => true,
        ));
        if ($aVals['category_id']) {
            $aCategories = Phpfox::getService('ultimatevideo.multicat')->getSelectBox(array('id' => '', 'name' => 'search[category]', 'class' => 'form-control'), $aVals['category_id'], null, null, 1);
        } else {
            $aCategories = Phpfox::getService('ultimatevideo.multicat')->getSelectBox(array('id' => '', 'name' => 'search[category]', 'class' => 'form-control'), null, null, null, 1);
        }
        $corePath = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/YouNet_UltimateVideos';

        $this->template()->setTitle(_p('manage_videos'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("ultimate_videos"), Phpfox::getLib('url')->makeUrl('admincp.app', ['id' => 'YouNet_UltimateVideos']))
            ->setBreadCrumb(_p('manage_videos'));
        $this->template()->assign(array(
            'aList' => $aList,
            'aCategories' => $aCategories,
            'aForms' => $aVals,
            'corePath' => $corePath,
            'sUrl' => $this->url()->makeUrl('admincp.ultimatevideo.managevideos'),
            'bIsSearch' => $bIsSearch,
        ));
    }
}