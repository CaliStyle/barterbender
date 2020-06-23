<?php
/**
 * @copyright		[FOXEXPERT_COPYRIGHT]
 * @author  		Belan Ivan
 * @package  		App_AdvancedFooter
 */
namespace Apps\Advancedfooter\Ajax;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Ajax;
/**
 * Class Ajax
 * @package Apps\Core_Blogs\Ajax
 */
class Ajax extends Phpfox_Ajax
{
    public function updateActivity()
    {
        Phpfox::getService('advancedfooter.social')->updateSocialActivity($this->get('id'), $this->get('active'));
    }

    public function updateMenuActivity()
    {
        Phpfox::getService('advancedfooter.menu')->updateActivity($this->get('id'), $this->get('active'));
    }
}
