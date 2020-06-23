<?php

/**
 * [PHPFOX_HEADER]
 *
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		PhuongNV
 * @package  		yn_member
 */

namespace Apps\YNC_Member\Block;

// DO WE NEED THIS? @TODO consider remove before production
class AdvancedSearch extends \Phpfox_Component
{
    public function process()
    {
        $this->template()->assign([
            'apiKey' => \Phpfox::getParam('core.google_api_key'),
        ]);
        return 'block';
    }
}