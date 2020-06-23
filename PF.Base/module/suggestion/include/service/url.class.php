<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[YOUNETCO]
 * @author  		NghiDV
 * @package  		Module_Suggestion
 * @version 		$Id: process.class.php 1 2011-11-25 15:29:17Z YOUNETCO $
 */
class Suggestion_Service_Url extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
		$this->_sTable = Phpfox::getT('suggestion');                
    }
    
    /*
     * @param sLink: url redirect to
     * @param sTitle: title display
     * @param bExternal: open in new tab
     * @return full link tag <a href="[url]" [blank]>[title]</a>
     */
    public function makeLink($sLink, $sTitle, $bExternal=true){
        $sLink = html_entity_decode($sLink);
        //$sTitle = html_entity_decode($sTitle);
        $sLink = urldecode($sLink);
        
        if ($bExternal==true) $sBlank = 'target="_blank"'; else $sBlank='';
        return '<a href="'.$sLink.'" '.$sBlank.'>'.$sTitle.'</a>';
    }
    /** 
    * If a call is made to an unknown method attempt to connect
    * it to a specific plug-in with the same name thus allowing 
    * plug-in developers the ability to extend classes.
    *
    * @param string $sMethod is the name of the method
    * @param array $aArguments is the array of arguments of being passed
    */
    public function __call($sMethod, $aArguments)
    {
            if ($sPlugin = Phpfox_Plugin::get('suggestion.service_url__call'))
            {
                    return eval($sPlugin);
            }

            Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }	  
}
?>