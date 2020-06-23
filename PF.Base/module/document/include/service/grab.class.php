<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright        [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Document
 */
class Document_Service_Grab extends Phpfox_Service
{
    public function get($sMethod,$aParams)
    {
        $scribdUrl = 'http://api.scribd.com/api';
        $sUrl = '';
        if ($sMethod != "")
        {
            $sUrl = $scribdUrl . '?method=' . $sMethod;
            foreach ($aParams as $key => $value)
            {
                $sUrl .= '&' . $key . '=' . $value;
            }
        }
        $scribdResults = simplexml_load_file($sUrl);
        if ($scribdResults['stat'] == 'ok')
        {
            return $scribdResults;
        }else
        {
            return false;
        }
    }   
}
?>
