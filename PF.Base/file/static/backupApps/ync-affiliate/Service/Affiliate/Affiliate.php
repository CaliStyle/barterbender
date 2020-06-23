<?php

/**
 * Created by PhpStorm.
 * User: dai
 * Date: 20/01/2017
 * Time: 10:44
 */

namespace Apps\YNC_Affiliate\Service\Affiliate;

use Phpfox;

Class Affiliate extends \Phpfox_Service
{
    private $_sAssocTable;
    public function __construct()
    {
        $this->_sTable = \Phpfox::getT('yncaffiliate_accounts');
        $this->_sAssocTable = \Phpfox::getT('yncaffiliate_assoc');
    }

    public function getManageAffiliate($aConds = array(), $iPage = 0, $iLimit = NULL, $iCount = NULL)
    {
        $sWhere = '1=1';
        if (count($aConds) > 0) {
            $sCond = implode('  ', $aConds);
            $sWhere .= ' ' . $sCond;
        }
        $iCount = db()
            ->select("COUNT(acc.account_id)")
            ->from($this->_sTable, 'acc')
            ->where($sWhere)
            ->execute("getSlaveField");

        $aAffiliates = array();
        if ($iCount) {
            $aAffiliates = db()
                ->select("acc.*")
                ->from($this->_sTable, 'acc')
                ->join(Phpfox::getT('user'),'u','u.user_id = acc.user_id')
                ->where($sWhere)
                ->order('acc.account_id DESC')
                ->limit($iPage, $iLimit, $iCount)
                ->execute('getSlaveRows');
        }

        return array($iCount, $aAffiliates);
    }
    public function checkIsAffiliate($iUserId)
    {
        $aRow = db()->select('account_id,status')
                        ->from($this->_sTable)
                        ->where('user_id ='.(int)$iUserId)
                        ->execute('getRow');
        if(!$aRow)
        {
            return false;
        }
        return $aRow['status'];
    }
    public function getClient($iUserId = null, $iFromLevel = 0, $iLasAssocId = 0, $iSearchUserId = 0, $iNoLimit = 0)
    {
        if (!$iUserId) {
            $iUserId = (int)Phpfox::getUserId();
        }

        $iClientLimit = (int) setting('ynaf_number_users_per_level_network_clients');
        if ($iClientLimit < 1) {
	        $iClientLimit = 1;
        }
        $iMaxLevel = setting('ynaf_number_commission_levels');

        // level offset to use when load more for middle clients
        if ($iFromLevel >= $iMaxLevel){
            return;
        }
        // pass 1 to start recursive
        $result = $this->_getClient($iUserId, $iClientLimit, $iFromLevel + 1, $iMaxLevel, $iLasAssocId, $iSearchUserId, $iNoLimit);

        return $result;
    }
    protected function _getClient($iUserId, $iClientLimit, $iFromLevel, $iMaxLevel, $iLasAssocId, $iSearchUserId, $iNoLimit)
    {
        // max level reached, return
        if ($iFromLevel > $iMaxLevel) {
            return array();
        }
        $aResult = [];
        if(!$iNoLimit)
        {
            $aClients = db()->select('yas.*')
                ->from($this->_sAssocTable,'yas')
                ->where('yas.user_id = '.$iUserId.' AND yas.assoc_id >'.$iLasAssocId)
                ->limit($iClientLimit)
                ->execute('getSlaveRows');
        }
        else{
            $aClients = db()->select('yas.*')
                            ->from($this->_sAssocTable,'yas')
                            ->where('yas.user_id = '.$iUserId.' AND yas.assoc_id >'.$iLasAssocId)
                            ->execute('getSlaveRows');
        }
        foreach ($aClients as $key => $aClient)
        {
            $aClientDetail = db()->select('ug.title as group_name,u.joined,u.email,u.user_image,'.Phpfox::getUserField())
                                ->from(Phpfox::getT('user'),'u')
                                ->join(Phpfox::getT('user_group'),'ug','ug.user_group_id = u.user_group_id')
                                ->where('u.user_id ='.$aClient['new_user_id'].' AND u.status_id = 0')
                                ->execute('getRow');
            if(empty($aClientDetail))
            {
                unset($aClients[$key]);
                continue;
            }
            $aChilds = [];
            $iFound = 0;
            if(!$iSearchUserId || ($aClient['new_user_id'] != $iSearchUserId))
            {
                $aChilds = $this->_getClient($aClient['new_user_id'],$iClientLimit,$iFromLevel + 1,$iMaxLevel, 0, $iSearchUserId,$iNoLimit);
            }
            else{
                $iFound = 1;
            }
            $iDirectClient = $this->countDirectClient($aClient['new_user_id']);

            $iTotalClient = $this->countAllClient($aClient['new_user_id']);
            $aOneClient = [
                'user_id' => $aClient['new_user_id'],
                'assoc_id' => $aClient['assoc_id'],
                'clients' => $aChilds,
                'loaded_clients' => count($aChilds),
                'total_client' => $iTotalClient,
                'direct_client' => $iDirectClient,
                'is_last' => ($iFromLevel == $iMaxLevel - 1) ? 1:0,
                'detail' => $aClientDetail,
                'level' => $iFromLevel
            ];
            if (!$iSearchUserId|| ($iSearchUserId && count($aChilds)) || $iFound) {
                $aResult[] = $aOneClient;
            }

        }
        return $aResult;
    }
    public function countAllClient($iUserId)
    {
        $aResult = [];
        $aResult[0][] = $iUserId;
        $iClientCount = 0;
        $iMaxLevel = setting('ynaf_number_commission_levels');
        for ($level = 0; $level < $iMaxLevel; $level++)
        {
            if (empty($aResult[$level])) {
                $aResult[$level + 1] = array();
            } else {
                $aClients = db()->select('yas.new_user_id')
                                ->from($this->_sAssocTable,'yas')
                                ->join(':user','u','u.user_id = yas.new_user_id')
                                ->where('yas.user_id IN ('.implode(',',$aResult[$level]).')')
                                ->execute('getSlaveRows');
                $aResult[$level + 1] = [];
                foreach ($aClients as $aClient)
                {
                    $aResult[$level + 1][] = $aClient['new_user_id'];
                }
                $iClientCount += count($aResult[$level + 1]);
            }
        }
        return $iClientCount;
    }
    public function getTree($aClients,$iMaxLevel,$iTotalDirect,$iSearchUserId,$iUserId,$iLastAssocId,$iLevel,$iLoadedClient)
    {
        $sHtml = '';
        return $this->_renderClientTree($aClients,$iMaxLevel,$iTotalDirect,$iSearchUserId,$iUserId,$iLastAssocId,$iLevel,$iLoadedClient,$sHtml);
    }
    protected function _renderClientTree($aClients,$iMaxLevel,$iTotalDirect,$iSearchUserId,$iUserId,$iLastAssocId,$iLevel,$iLoadedClient,$sHtml)
    {
        $oOutput = Phpfox::getLib('parse.output');
        if(count($aClients))
        {
            foreach ($aClients as $aClient)
            {
                $sImage =  Phpfox::getLib('phpfox.image.helper')->display(['user'=> $aClient['detail'],'suffix' => '_100_square']);
                $sLink = Phpfox::getLib('url')->makeUrl($aClient['detail']['user_name']);
                $sName = $oOutput->clean($aClient['detail']['full_name']);
                $iLastAssocId = $aClient['assoc_id'];
                $iLastUserId = $aClient['user_id'];
                $iLevel = $aClient['level'];
                $sHtml .= '<li class="yncaffiliate_level_item ';
                $sHtml .= ($aClient['level'] == ($iMaxLevel - 1)) ? 'yncaffiliate_item_penultimate">' : '">';
                $sHtml .= '<div class="yncaffiliate_avatar">';
                $sHtml .= $sImage;
                $sHtml .= '</div>';
                $sHtml .= '<a href="'.$sLink.'" title="'.$sName.'" class="yncaffiliate_client_name">'.$sName.'</a>';
                $sHtml .= '<span class="yncaffiliate_btn_action_explain"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i></span>';
                if($aClient['direct_client'] && ($aClient['level'] < $iMaxLevel) && !$iSearchUserId)
                {
                    $sHtml .= '<span class="yncaffiliate_btn_action_items_more"><i class="fa fa-plus fa-lg" aria-hidden="true"></i></span>';
                }
                $sHtml .= '<div class="yncaffiliate_client_item_info">';
                $sHtml .= '<a href="'.$sLink.'">'.$sName.'</a>';
                $sHtml .= '<p>'._p('client_level').': '.$aClient['level'].'</p>';
                $sHtml .= '<p>'._p('total_affiliates').': '.$aClient['total_client'].'</p>';
                $sHtml .= '<p>'._p('user_group').': '.(\Core\Lib::phrase()->isPhrase($aClient['detail']['group_name']) ? _p($aClient['detail']['group_name']) : $aClient['detail']['group_name']).'</p>';
                $sHtml .= '<p>'._p('registration_date').': '.Phpfox::getTime(Phpfox::getParam('core.extended_global_time_stamp'),$aClient['detail']['joined']).'</p>';
                $sHtml .= '<p>'._p('client_email').': '.$aClient['detail']['email'].'</p>';
                $sHtml .= '<span class="yncaffiliate_btn_action_close"><i class="fa fa-times fa-lg" aria-hidden="true"></i></span>';
                $sHtml .= '</div>';
                if(count($aClient['clients']))
                {
                    if($aClient['level'] == ($iMaxLevel - 1))
                    {
                        $sHtml .= '<ul class="yncaffiliate_level_items_more yncaffiliate_last_level clearfix">';
                    }
                    else
                    {
                        $sHtml .= '<ul class="yncaffiliate_level_items_more clearfix">';
                    }
                    $sHtml = $this->_renderClientTree($aClient['clients'],$iMaxLevel,$aClient['direct_client'],$iSearchUserId,$aClient['user_id'],$aClient['assoc_id'],$aClient['level'],$aClient['loaded_clients'],$sHtml);
                    $sHtml .= '</ul>';
                }
            }
            if($iLoadedClient < $iTotalDirect && !$iSearchUserId)
            {
                $sHtml .= '<li class="yncaffiliate_level_item yncaffiliate_btn_more">';
                $sHtml .= '<a href="" id="ynaf_loadmore_'.$iLastUserId.'" onclick="return ynaLoadMoreClient('.$iUserId.','.$iLevel.','.$iLastAssocId.','.$iLoadedClient.','.$iMaxLevel.','.$iTotalDirect.','.$iSearchUserId.','.$iLastUserId.');">'.($iLevel == $iMaxLevel ? '' :_p('more_l')).'</a>';
                $sHtml .= '</li>';
            }
        }
        else{
            return '<li class="yncaffiliate_level_item">'._p('no_more_clients_found').'</li>';
        }
        return $sHtml;
    }
    public function countDirectClient($iUserId = null)
    {
        if(!$iUserId)
        {
            $iUserId = (int)Phpfox::getUserId();
        }
        return db()->select('COUNT(*)')
                    ->from($this->_sAssocTable,'yas')
                    ->join(':user','u','u.user_id = yas.new_user_id')
                    ->where('yas.user_id ='.$iUserId)
                    ->execute('getSlaveField');
    }
    public function searchUser($sText)
    {
        $aResult = db()->select('u.user_id,u.full_name,'.Phpfox::getUserField())
                        ->from(Phpfox::getT('user'),'u')
                        ->where('u.full_name like \'%'.$sText.'%\'')
                        ->limit(10)
                        ->execute('getRows');
        return $aResult;
    }

    public function getFattenClients($iUserId)
    {
        $aClients = $this->getClient($iUserId,0,0,0,1);
        $aResult = $this->_flattenClients($aClients);
        return $aResult;
    }

    protected function _flattenClients($aClients) {
        $aResult = array();
        foreach ($aClients as $aClient) {
            $one_client = array(
                'user_name' => $aClient['detail']['full_name'],
                'user_id' => $aClient['user_id'],
                'level' => $aClient['level'],
                'total_client' => $aClient['total_client'],
                'user_group' => \Core\Lib::phrase()->isPhrase($aClient['detail']['group_name']) ? _p($aClient['detail']['group_name']) : $aClient['detail']['group_name'],
                'registration_date' => Phpfox::getTime(Phpfox::getParam('core.extended_global_time_stamp'),$aClient['detail']['joined']),
                'email' => $aClient['detail']['email']
            );
            $sub_clients = $aClient['clients'];
            $aResult[] = $one_client;
            $aResult = array_merge($aResult, $this->_flattenClients($sub_clients));
        }
        return $aResult;
    }

    public function getAffiliateClient($aConds = array(), $iPage = 0, $iLimit = NULL)
    {
        $sWhere = '';
        $sWhere .= '1=1';
        if (count($aConds) > 0) {
            $sCond = implode('  ', $aConds);
            $sWhere .= ' ' . $sCond;
        }
        $iCount = db()
            ->select("COUNT(ass.assoc_id)")
            ->from($this->_sAssocTable, 'ass')
            ->join(Phpfox::getT('user'),'u1','u1.user_id = ass.user_id')
            ->join(Phpfox::getT('user'),'u2','u2.user_id = ass.new_user_id')
            ->where($sWhere)
            ->execute("getSlaveField");
        $aAffiliates = array();
        if ($iCount) {
            $aAffiliates = db()
                ->select("ass.*,u1.full_name as aff_name,u1.user_name as aff_user_name,u2.full_name as client_name,u2.user_name as client_user_name,l.target_url")
                ->from($this->_sAssocTable, 'ass')
                ->join(Phpfox::getT('user'),'u1','u1.user_id = ass.user_id')
                ->join(Phpfox::getT('user'),'u2','u2.user_id = ass.new_user_id')
                ->leftJoin(Phpfox::getT('yncaffiliate_links'),'l','l.link_id = ass.link_id')
                ->where($sWhere)
                ->order('ass.assoc_id DESC')
                ->limit($iPage, $iLimit, $iCount)
                ->execute('getSlaveRows');
        }

        return array($iCount, $aAffiliates);
    }

    public function getAssoc($iUserId,$bActive = false)
    {
        if($bActive)
        {
            return db()->select('*')
                        ->from($this->_sAssocTable,'yaas')
                        ->join($this->_sTable,'yaa','yaa.user_id = yaas.user_id')
                        ->where('new_user_id = '.$iUserId.' AND yaa.status = \'approved\'')
                        ->execute('getRow');
        }
        return db()->select('*')
                    ->from($this->_sAssocTable)
                    ->where('new_user_id ='.$iUserId)
                    ->execute('getRow');
    }
    public function getAllClients($iUserId)
    {
        $aResult[0][] = $iUserId;
        $iMaxLevel = setting('ynaf_number_commission_levels');
        for ($level = 0; $level < $iMaxLevel; $level++)
        {
            $aClients = [];
            if(isset($aResult[$level]) && $aResult[$level])
            {
                $aClients = db()->select('new_user_id')
                                ->from($this->_sAssocTable)
                                ->where('user_id IN ('.implode(',',$aResult[$level]).')')
                                ->execute('getRows');
            }
            foreach ($aClients as $aClient)
            {
                $aResult[$level + 1][] = $aClient['new_user_id'];
            }
        }
        return $aResult;
    }
    public function countAffiliates($aConds = [])
    {
        $sWhere = '1=1';
        if(count($aConds))
        {
            $sCond = implode('  ', $aConds);
            $sWhere .= ' ' . $sCond;
        }
        return db()->select('COUNT(*)')
                    ->from($this->_sTable)
                    ->where($sWhere)
                    ->execute('getField');
    }
    public function countClients($aConds = [])
    {
        $sWhere = '1=1';
        if(count($aConds))
        {
            $sCond = implode('  ', $aConds);
            $sWhere .= ' ' . $sCond;
        }
        return db()->select('COUNT(*)')
            ->from($this->_sAssocTable)
            ->where($sWhere)
            ->execute('getField');
    }
    public function getDetail($iUserId)
    {
        return db()->select('*')
                    ->from($this->_sTable)
                    ->where('user_id ='.(int)$iUserId)
                    ->execute('getRow');
    }
}