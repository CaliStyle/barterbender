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
class Fevent_Component_Block_Past extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aParentModule = $this->getParam('aParentModule');
		$bIsPage = $aParentModule['module_id'] == 'pages' ? $aParentModule['item_id'] : 0;
        $iLimit = $this->getParam('limit',4);
        if (!$iLimit) {
            return false;
        }
		list($iTotal, $aPast) = Phpfox::getService('fevent')->getPast($bIsPage, false,$iLimit);
		
		
		/*convert time*/
		$oHelper = Phpfox::getService('fevent.helper');
		$aPast = $oHelper->displayTimeByFormatForBlock($aPast);  
		if (!$iTotal)
		{
			return false;
		}

		if(count($aPast)){
			foreach ($aPast as $key => &$aEvent) {
				$oHelper->getImageDefault($aEvent,'block');
			}
		}

		$this->template()->assign(array(
				'sHeader' => _p('past_events2'),
				'aPast' => $aPast,
				'bViewMore' => $iTotal > $iLimit,
                'aFooter' => array(
                    _p('view_more') => $this->url()->makeUrl('fevent',['when' => 'past', 'view' => 'all'])
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
                'info' => _p('Past Events Limits'),
                'description' => _p('Define the limit of how many past events can be displayed when viewing the events section. Set 0 will hide this block.'),
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
                'title' => _p('"Past Events Limit" must be greater than or equal to 0')
            ],
        ];
    }
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('fevent.component_block_past_clean')) ? eval($sPlugin) : false);
	}
}

?>