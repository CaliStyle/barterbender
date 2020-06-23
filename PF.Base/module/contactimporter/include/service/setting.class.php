<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Development
 * @package          Module_Contactimporter
 * @version          3.02
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>

<?php
class Contactimporter_Service_Setting extends Phpfox_Service  
{
    public function getApiSetting($sName = 'facebook')
    {        
        $aRow = Phpfox::getLib('phpfox.database')->select('*')
            ->from(Phpfox::getT('contactimporter_api_settings'))
            ->where('api_name = "' . $sName . '"')
            ->execute('getRow');
        return $aRow;
	}
}
?>