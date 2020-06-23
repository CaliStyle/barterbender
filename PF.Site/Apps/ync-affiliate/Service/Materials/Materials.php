<?php

/**
 * Created by PhpStorm.
 * User: dai
 * Date: 20/01/2017
 * Time: 10:44
 */

namespace Apps\YNC_Affiliate\Service\Materials;

use Phpfox;

Class Materials extends \Phpfox_Service
{
    public function __construct()
    {
        $this->_sTable = \Phpfox::getT('yncaffiliate_materials');
    }

    public function getMaterials($aWhere = [])
    {
        $aWhere[] = '1=1';
        return db()->select('*')
                    ->from($this->_sTable)
                    ->where(implode(' AND ', $aWhere))
                    ->order('ordering ASC')
                    ->execute('getSlaveRows');
    }
    public function getMaterialById($iId)
    {
        return db()->select('*')
                ->from($this->_sTable)
                ->where('material_id ='.(int)$iId)
                ->execute('getRow');
    }
    public function getMaterialsInFrontend($iPage,$iLimit,$aConds = [])
    {
        $sWhere = 'is_active = 1';
        if (count($aConds) > 0) {
            $sCond = implode('  ', $aConds);
            $sWhere .= ' ' . $sCond;
        }
        $iCnt = db()->select('COUNT(*)')
                    ->from($this->_sTable)
                    ->where($sWhere)
                    ->execute('getSlaveField');
        $aCodes = [];
        if($iCnt)
        {
            $aCodes = db()->select('*')
                        ->from($this->_sTable)
                        ->where($sWhere)
                        ->limit($iPage,$iLimit,$iCnt)
                        ->order('ordering ASC')
                        ->execute('getSlaveRows');
            foreach ($aCodes as $key => $aCode)
            {
                $aff_url = Phpfox::getService('yncaffiliate.link')->getAffiliateUrl(Phpfox::getUserId(),$aCode['link']);
                $sImagePath = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aCode['server_id'],
                    'path' => 'core.url_pic',
                    'file' => 'yncaffiliate/'.$aCode['image_path'],
                    'suffix' => '_'.$aCode['material_width'].'_'.$aCode['material_height'],
                    'return_url' => true
                ));
                $aCodes[$key]['iframe_code'] = '<div style="position:relative;width: '.$aCode['material_width'].'px"><iframe frameborder="0" title="'.$aCode['material_name'].'" src="'.$sImagePath.'" width="'.$aCode['material_width'].'" height ="'.$aCode['material_height'].'"></iframe><a style="position:absolute; top:0; left:0; display:inline-block; z-index:5;width:'.$aCode['material_width'].'px;height:'.$aCode['material_height'].'px" target="_blank" href="'.$aff_url.'"></a></div>';
                $aCodes[$key]['image_full_path'] = $sImagePath;
            }
        }
        return [$iCnt,$aCodes];
    }
}