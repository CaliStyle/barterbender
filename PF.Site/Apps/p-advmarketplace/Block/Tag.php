<?php

namespace Apps\P_AdvMarketplace\Block;

use Phpfox_Component;
use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class Tag extends Phpfox_Component
{
    public function process()
    {
        if (!phpfox::isModule('tag')) {
            return false;
        } else {
            $bIsSearch = $this->getParam('bIsSearch');
            $blockLocation = $this->getParam('location', 0);  // 1,9 left 3,10 right
            $isSideLocation = Phpfox::getService('advancedmarketplace.helper')->bIsSideLocation($blockLocation);
            if ($bIsSearch && !$isSideLocation) {
                return false;
            }

            if (!Phpfox::getParam('tag.enable_tag_support')) {
                return false;
            }

            $aRows = $this->getParam('aListing');

            if (Phpfox::isModule('tag')) {
                $aTags = Phpfox::getService('tag')->getTagsById('advancedmarketplace', $aRows['listing_id']);
                if (isset($aTags[$aRows['listing_id']])) {
                    $aRows['tag_list'] = $aTags[$aRows['listing_id']];
                }
            }

            if (empty($aRows['tag_list'])) {
                return false;
            }

            foreach ($aRows['tag_list'] as $iKey => $value) {
                $aRows['tag_list'][$iKey]['tag_url'] = Phpfox::callback('advancedmarketplace' . '.getTagLink',
                        $aRows['user_id']) . urlencode($aRows['tag_list'][$iKey]['tag_url']);
            }

            $this->template()->assign(array(
                'aTags' => $aRows,
                'sHeader' => _p('advancedmarketplace.topics'),
                'sCustomClassName' => 'ync-block',
            ));

            return 'block';
        }
    }
}
