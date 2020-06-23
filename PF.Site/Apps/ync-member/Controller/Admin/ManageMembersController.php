<?php

namespace Apps\YNC_Member\Controller\Admin;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class ManageMembersController extends \Phpfox_Component
{
    public function process()
    {
        // Page Number & Limit Per Page
        $iPage = $this->getParam('page', 1);
        $iPageSize = 10;
        $aVals = [];
        $aConds = [];
        // Search Filter
        $oSearch = Phpfox::getLib('search')->set([
            'type' 	 => 'request',
            'search' => 'search',
        ]);
        $bIsSearch = false;
        $aSearch = $this->getParam('search');
        if($aSearch){
            $aVals['full_name'] 	= $aSearch['full_name'];
            $aVals['email'] 		= $aSearch['email'];
            $aVals['user_group_id']	= $aSearch['user_group_id'];
            $aVals['is_featured']  		= $aSearch['is_featured'];
            $bIsSearch = true;
        }
        else {
            $aVals = [
                'full_name' =>      '',
                'email'	=>          '',
                'user_group_id' =>  '',
                'is_featured' =>        '',
            ];
        }

        $aUserGroups = Phpfox::getService('user.group')->get();

        if(isset($aVals['full_name']) && trim($aVals['full_name']) != '')
        {
            $aConds[] = "AND u.full_name like '%{$aVals['full_name']}%'";
        }
        if(isset($aVals['email']) && trim($aVals['email']) != '')
        {
            $aConds[] = "AND u.email like '%{$aVals['email']}%'";
        }
        if(isset($aVals['user_group_id']) && $aVals['user_group_id'])
        {
            $aConds[] = "AND u.user_group_id = {$aVals['user_group_id']}";
        }
        if($aVals['is_featured'])
        {
            switch ($aVals['is_featured']) {
                case 'featured':
                    $aConds[] = "AND uf.user_id IS NOT NULL";
                    break;
                case 'not_featured':
                    $aConds[] = "AND uf.user_id IS NULL";
                    break;
            }
        }

        $aConds[] = "AND u.view_id = 0";

        list($iCount,$aList) = Phpfox::getService('ynmember.browse')->getManageMember($aConds, $iPage, $iPageSize);
        // Set pager
        Phpfox::getLib('pager')->set([
            'page'  => $iPage,
            'size'  => $iPageSize,
            'count' => $iCount,
            'ajax' => 'ynmember.changePageManageMembers',
            'popup'	=> true,
        ]);

        $corePath = Phpfox::getParam('core.path_actual').'PF.Site/Apps/ync-member';

        $this -> template() -> setTitle(_p('Members'));
        $this -> template() -> assign([
            'aList' => $aList,
            'aUserGroups' => $aUserGroups,
            'aForms'		=> $aVals,
            'corePath' => $corePath,
            'sUrl' => $this->url()->makeUrl('admincp.ynmember.managemembers'),
            'bIsSearch' => $bIsSearch,
        ]);
    }
}