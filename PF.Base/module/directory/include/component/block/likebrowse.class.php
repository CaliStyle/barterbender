<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_LikeBrowse extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{

		$aLikes = Phpfox::getService('like')->getLikes($this->request()->get('type_id'), $this->request()->getInt('item_id'));
	
		$sErrorMessage = '';

		if (!count($aLikes))
		{
				$sErrorMessage = _p('like.nobody_likes_this');
			
		}

		$this->template()->assign(array(
				'aLikes' => $aLikes,
				'sErrorMessage' => $sErrorMessage,
                'sCustomClassName' => 'ync-block'
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
	}
}

?>