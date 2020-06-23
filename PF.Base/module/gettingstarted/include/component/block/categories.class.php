<?php

/**
 *
 *
 * @copyright   [YOUNET_COPYRIGHT]
 * @author      YouNet Company
 * @package     YouNet_Event
 * @version     3.02p5
 */

defined('PHPFOX') or exit('NO DICE!');


class Gettingstarted_component_block_categories extends Phpfox_Component
{

    public function process()
    {
        $sCategory = $this->request()->get('req3');
        $aCategories = Phpfox::getService('gettingstarted.multicat')->getForBrowse($sCategory);
        if (!is_array($aCategories)) {
            return false;
        }
        if (!count($aCategories)) {
            return false;
        }
        $this->template()->assign(array(
                'sHeader' => ($sCategory? _p('gettingstarted.sub_categories') : _p('gettingstarted.categories')),
                'aCategories' => $aCategories,
                'sCategory' => $sCategory
            )
        );

        return 'block';

    }
}

?>

