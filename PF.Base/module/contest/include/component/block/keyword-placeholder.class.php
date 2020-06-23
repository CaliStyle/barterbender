<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Block_Keyword_Placeholder extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aKeywordPlaceholder = Phpfox::getService('contest.mail.process')->getAllReplaces();


		foreach($aKeywordPlaceholder as &$sKeyword)
		{
			$sKeyword = _p('contest.keywordsub_' . $sKeyword);
		}
		$this->template()->assign(array(
			'aKeywordPlaceholder' => $aKeywordPlaceholder
		));
	}
	
}

?>