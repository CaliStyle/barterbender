<?php

namespace Apps\P_AdvMarketplace\Block;

use Phpfox_Component;
use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class CustomField extends Phpfox_Component
{
    public function process()
    {
        $bIsEdit = false;
        $iListingId = $this->request()->getInt('id');

        if ($iListingId) {
            $bIsEdit = true;
        } else {

            $iListingId = $this->getParam('iListingId');
            if ($iListingId) {
                $bIsEdit = true;
            }
        }

        if (!$bIsEdit) {
            $aCat = $this->getParam('iCatId');
            if (!empty($aCat)) {
                $aTotalGroups = phpfox::getService('advancedmarketplace.custom.group')->getFieldsByCatId($aCat['iCatId']);
            } else {
                return false;
            }
        } else {
            $aCat = $this->getParam('iCatId');
            if (!isset($aCat)) {
                $aCategoryIds = phpfox::getLib('database')->select('category_id')
                    ->from(phpfox::getT('advancedmarketplace_category_data'))
                    ->where('listing_id = ' . $iListingId)
                    ->execute('getSlaveRows');
                $aCatId = phpfox::getService('advancedmarketplace.custom.process')->getChildsOfCats($aCategoryIds);
                $aCat['iCatId'] = $aCatId['category_id'];
                $aTotalGroups = phpfox::getService('advancedmarketplace.custom.group')->getFieldsByCatId($aCatId['category_id'],
                    $iListingId);
            } else {
                $aCat = $this->getParam('iCatId');
                if (!empty($aCat)) {
                    $aTotalGroups = phpfox::getService('advancedmarketplace.custom.group')->getFieldsByCatId($aCat['iCatId']);
                } else {
                    return false;
                }

            }

        }

        $this->template()->assign(array(
            'iCustomCatId' => isset($aCat['iCatId']) ? $aCat['iCatId'] : $aCat['iCatId'],
            'aTotalGroups' => $aTotalGroups,
            'sCustomClassName' => 'ync-block',
        ));

        return 'block';
    }
}
