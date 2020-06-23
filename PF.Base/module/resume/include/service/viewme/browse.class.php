<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         VuDP, TienNPL
 * @package        Module_Resume
 * @version        3.01
 * 
 */
class Resume_Service_Viewme_Browse extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('resume_viewme');	
	}
	
	
	public function query()
	{
		$this->database()->group('rv.user_id');
	}
	
	public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false)
	{
	 	$this->database()->select('rb.headline,rb.image_path,rb.server_id,rv.time_stamp as viewed_timestamp,rb.resume_id as viewed_resume_id,');
		$this->database()
			->leftJoin(Phpfox::getT('resume_basicinfo'), 'rb',"rb.user_id = rv.user_id and rb.is_published=1 and rb.status='approved'");
		$this->database()->join(Phpfox::getT('user'), 'u1',"u1.user_id = rv.user_id");
	}
	
}

?>
