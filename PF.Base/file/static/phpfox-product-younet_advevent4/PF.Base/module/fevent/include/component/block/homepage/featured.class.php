<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
class Fevent_Component_Block_Homepage_Featured extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
	    $bInHomepage = $this->getParam('bInHomepage', false);
	    if(!$bInHomepage) {
	        return false;
        }
        $aParentModule = $this->getParam('aParentModule');
        $bIsPage = $aParentModule['module_id'] == 'pages' ? true : false;
        list($iTotal, $aFeatured) = Phpfox::getService('fevent')->getFeatured($bIsPage, false);

        if ($iTotal) {
            $this->template()->assign(array(
                'sCorePath' => Phpfox::getParam('core.path'),
                'aFeatured' => $aFeatured,
                'sCustomClassName' => 'ync-block',
                'sHeader' => ''
            ));
            return 'block';
        }
        else {
            return false;
        }
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{

	}
}

?>