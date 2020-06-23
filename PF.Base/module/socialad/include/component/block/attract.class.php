<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		MinhTA
 * @package  		Module_socialad
 */

// Add and edit request both go here 
class Socialad_Component_Block_Attract extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{

		$aUser =  Phpfox::getService('user')->getUser(Phpfox::getUserId());
        $uLink = Phpfox::getLib('url')->makeUrl($aUser['user_name']);
		$this->template()->assign(array(
			'sHeader' => _p('want_more_attention') . ' ?',
			'sAttractTitle' => _p('attract_title', array(
				'site_name' => Phpfox::getParam('core.site_title'),
			)),
			'aAttractedUser' => $aUser,
			'sAttractText' => _p('attract_text', array(
				'site_name' => Phpfox::getParam('core.site_title'),
				'full_name' => '<a href="'.$uLink.'">'.$aUser['full_name'].'</a>',
			)),

		));

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

