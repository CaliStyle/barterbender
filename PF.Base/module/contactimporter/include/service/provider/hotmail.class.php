<?php

/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Development
 * @package          Module_Contactimporter
 * @version          2.06
 *
 */
defined('PHPFOX') or exit('NO DICE!');

require_once 'email_abstract.class.php';

class Contactimporter_Service_Provider_Hotmail extends Contactimporter_Service_Provider_Email_Abstract
{

	protected $_name = 'hotmail';

	/**
	 * @return array
	 */
	public function getData()
	{
		if(isset($_REQUEST['contact'])){
			$aContacts = isset($_REQUEST['contact']) ? json_decode(urldecode($_REQUEST['contact'])) : null;
			$_SESSION['contactimporter'][$this -> _name] = $aContacts;	
		}
		
		if (isset($_SESSION['contactimporter'][$this -> _name]) && $_SESSION['contactimporter'][$this -> _name])
		{
			return $_SESSION['contactimporter'][$this -> _name];
		}

		return $aContacts;
	}
}
