<?php

/**
 * Created by PhpStorm.
 * User: dai
 * Date: 20/01/2017
 * Time: 10:44
 */

namespace Apps\YNC_Affiliate\Service\Link;

use Phpfox;

Class Link extends \Phpfox_Service
{
    public function __construct()
    {
        $this->_activeModule = implode('\',\'',Phpfox::getLib('module')->getModules());
        $this->_sTable = Phpfox::getT('yncaffiliate_suggests');
        $this->_sLinkTable = Phpfox::getT('yncaffiliate_links');
    }
    public function getAffiliateUrl($iUserId = null, $sHref,$bIsDynamic = false)
    {
        $sHref = base64_encode($sHref);
        if(!$iUserId)
        {
            $iUserId = (int)Phpfox::getUserId();
        }
        if($bIsDynamic){
            return Phpfox::getLib('url')->permalink('yaf.d',$iUserId).$sHref;
        }
        return Phpfox::getLib('url')->permalink('yaf',$iUserId).$sHref;

    }
    public function getSuggestLinks()
    {
        $aLinks = db()->select('*')
                        ->from($this->_sTable)
                        ->where('module_id IN (\''.$this->_activeModule.'\')')
                        ->execute('getSlaveRows');
        if($aLinks)
        {
            foreach($aLinks as $key => $aLink)
            {
                $aLinks[$key]['href'] = $this->getTargetUrl($aLink);
                $aLinks[$key]['aff_link'] = $this->getAffiliateUrl((int)Phpfox::getUserId(),$aLinks[$key]['href']);
            }
        }
        return $aLinks;
    }
    public function getTargetUrl($aLink)
    {
        $aHref = Phpfox::getParam('core.path');
        if($aLink['module_id'] == 'profile')
        {
            return $aHref.Phpfox::getUserBy('user_name');
        }
        else
        {
            $aRoute = db()->select('*')
                            ->from(Phpfox::getT('menu'))
                            ->where('module_id = \''.$aLink['module_id'].'\' AND m_connection = \'main\'')
                            ->execute('getRow');
        }
        if($aRoute)
        {
            $aHref .= (str_replace('.','/',$aRoute['url_value']));
        }
        return $aHref;
    }
    public function checkIsTracking($sLink,$iUserId)
    {
        return db()->select('link_id')
                    ->from(Phpfox::getT('yncaffiliate_links'))
                    ->where('target_url = \''.$sLink.'\' AND user_id = '.$iUserId)
                    ->execute('getSlaveField');
    }
    public function getLinkTracking($iUserId = null,$iPage,$iLimit,$aConds = [])
    {
        if(!$iUserId)
        {
            $iUserId = (int)Phpfox::getUserId();
        }
        $sWhere = 'al.user_id = '.$iUserId;
        if (count($aConds) > 0) {
            $sCond = implode('  ', $aConds);
            $sWhere .= ' ' . $sCond;
        }
        $aTrackings = [];
        $iCnt =  db()->select('COUNT(al.link_id)')
                    ->from($this->_sLinkTable,'al')
                    ->where($sWhere)
                    ->execute('getSlaveField');
        if($iCnt)
        {
            $aTrackings = db()->select('*')
                                ->from($this->_sLinkTable,'al')
                                ->where($sWhere)
                                ->order('al.last_click DESC')
                                ->limit($iPage, $iLimit, $iCnt)
                                ->execute('getSlaveRows');
        }
        return [$iCnt,$aTrackings];
    }
}