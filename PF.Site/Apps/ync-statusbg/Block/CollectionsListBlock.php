<?php
namespace Apps\YNC_StatusBg\Block;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;

/**
 * Class CollectionsListBlock
 * @package Apps\YNC_StatusBg\Block
 */
class CollectionsListBlock extends Phpfox_Component
{
    public function process()
    {
        $aCollections = Phpfox::getService('yncstatusbg')->getCollectionsList();
        if (!$aCollections) {
            return false;
        }
        $this->template()->assign([
            'aCollections' => $aCollections,
            'iTotalCollection' => count($aCollections)
        ]);
        return 'block';
    }
}