<?php
/**
 * @copyright		[FOXEXPERT_COPYRIGHT]
 * @author  		Belan Ivan
 * @package  		App_AdvancedFooter
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright		[FOXEXPERT_COPYRIGHT]
 * @author  		Belan Ivan
 * @package  		App_LandingPage
 */



$installer = new Core\App\Installer();
$installer->onInstall(function () use ($installer) {
    (new \Apps\Advancedfooter\Installation\Version\v453())->process();
});
