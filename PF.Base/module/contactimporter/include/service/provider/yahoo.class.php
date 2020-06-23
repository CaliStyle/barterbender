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

class Contactimporter_Service_Provider_Yahoo extends Contactimporter_Service_Provider_Email_Abstract
{

	protected $_name = 'yahoo';

}
