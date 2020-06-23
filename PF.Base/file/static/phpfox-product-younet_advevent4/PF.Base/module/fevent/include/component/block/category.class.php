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
class Fevent_Component_Block_Category extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }
        $sCategory = $this->getParam('sCategory');
        $aCategories = Phpfox::getService('fevent.category')->getForBrowse($sCategory);

        if (!is_array($aCategories))
        {
            return false;
        }

        if (!count($aCategories))
        {
            return false;
        }
        $sJdpickerPhrases = Phpfox::getService('fevent')->getJdpickerPhrases();
        echo "<script type='text/javascript'>".$sJdpickerPhrases."</script>";
        $this->template()->assign(array(
                'sHeader' => ($sCategory === null ? _p('categories') : _p('sub_categories')),
                'aCategories' => $aCategories,
                'sCustomClassName' => 'ync-block',
                'sCategory' => $sCategory
            )
        );

        return 'block';
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('fevent.component_block_category_clean')) ? eval($sPlugin) : false);
	}
}

?>
