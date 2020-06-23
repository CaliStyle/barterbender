<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Development
 * @package          Module_Contactimporter
 * @version          2.06
 *
 */
defined('PHPFOX') or exit('NO DICE!');

class Contactimporter_Component_Controller_Test extends Phpfox_Component
{
	public function process()
	{
		// var_dump(Phpfox::getService('contactimporter')->checkInviteIdExist('100004288896901', 3, 'facebook'));	
		// var_dump(Phpfox::getService('contactimporter')->checkInviteIdExist('848582167', 10, 'twitter'));	
		// var_dump(Phpfox::getService('contactimporter')->checkInviteIdExist('yt98ugoaJF', 10, 'linkedin'));

		// var_dump(Phpfox::getService('contactimporter')->getTotalSentInvitationsOfQueueInAPeriod(17, 3600 * 19, 'facebook' ));
		// var_dump(Phpfox::getService('contactimporter.process')->getLinkedinFriends(7));

		// Phpfox::getService('contactimporter')->getGrantedPermissionOfFacebookToken('facebook' ,'AAAEe4Y3IeMYBAAx3VQNR0xlYllrXQbOyo2InDMlXMXZCuqIy51lDdnsBaQIu046BTRquQXxfKpn7xaZAAC19Pr5naHpgi0fAi84wfU8QZDZD');
		$a= PHpfox::getService('contactimporter')->checkPermissionOfAccessToken('xmpp_login', 'facebook', 'AAAEe4Y3IeMYBAAx3VQNR0xlYllrXQbOyo2InDMlXMXZCuqIy51lDdnsBaQIu046BTRquQXxfKpn7xaZAAC19Pr5naHpgi0fAi84wfU8QZDZD' );
		var_dump($a);
	}

}