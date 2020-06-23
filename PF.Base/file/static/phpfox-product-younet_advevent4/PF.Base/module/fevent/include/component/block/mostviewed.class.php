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
class Fevent_Component_Block_Mostviewed extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aParentModule = $this->getParam('aParentModule');
		$bIsPage = $aParentModule['module_id'] == 'pages' ? true : false;

		//	does not support in pages
		if($bIsPage == true){
			return false;
		}

		$pageID = $aParentModule['module_id'] == 'pages' ? $aParentModule['item_id'] : -1;

		$iLimit = $this->getParam('limit',4);
		if (!$iLimit) {
		    return false;
        }
		list($iTotal, $aMostViewed) = Phpfox::getService('fevent')->getTopEvent('viewed', $iLimit, $bIsPage, false, false, $pageID);
		
		/*convert time*/
		$oHelper = Phpfox::getService('fevent.helper');
		$aMostViewed = $oHelper->displayTimeByFormatForBlock($aMostViewed);  

		if (!$iTotal)
		{
			return false;
		}
		
		if(count($aMostViewed)){
			foreach ($aMostViewed as $key => &$aEvent) {
				
				$oHelper->getImageDefault($aEvent,'block');
				
			}
		}

		$this->template()->assign(array(
				'sHeader' => _p('most_viewed'),
				'aMostViewed' => $aMostViewed,
                'aFooter' => array(
                    _p('view_more') => $this->url()->makeUrl('fevent',['sort' => 'most-viewed', 'view' => 'all'])
                ),
                'sCustomClassName' => 'ync-block'
			)
		);
		
		return 'block';		
	}
    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('Most Viewed Events Limits'),
                'description' => _p('Define the limit of how many most viewed events can be displayed when viewing the events section. Set 0 will hide this block.'),
                'value' => 4,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
        ];
    }
    /**
     * @return array
     */
    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int',
                'min' => 0,
                'title' => _p('"Most Viewed Events Limit" must be greater than or equal to 0')
            ],
        ];
    }
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('fevent.component_block_viewed_clean')) ? eval($sPlugin) : false);
	}
}

?>