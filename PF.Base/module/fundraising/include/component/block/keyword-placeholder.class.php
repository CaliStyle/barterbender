<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Block_Keyword_Placeholder extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aKeywordPlaceholder = Phpfox::getService('fundraising.mail')->getAllReplaces();


		foreach($aKeywordPlaceholder as &$sKeyword)
		{
			$sKeyword = _p('keywordsub_' . $sKeyword);
		}

		$this->template()->assign(array(
			'aKeywordPlaceholder' => $aKeywordPlaceholder
		));
	}
	
}

?>