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
class Document_Service_License_Process extends Phpfox_Service 
{
    /**
     * Class constructor
     */    
    public function __construct()
    {    
        $this->_sTable = Phpfox::getT('document_license');
    }
    public function add($aVals)
    {
       $iId = $this->database()->insert($this->_sTable,array(
                'license_name' => $aVals['name'],
                'reference_url' => $aVals['reference_url'],
                'image_url' => $aVals['image_url'],
                'time_stamp' => PHPFOX_TIME,
                'used' => 0
                ));
       return $iId;
        
    }
    public function update($aVals)
    {
        if ($aVals['id'])
        {
            if (isset($aVals['image_url']))
            {
                $this->database()->update($this->_sTable, array(
                                'license_name' => $aVals['name'],
                                'reference_url'=> $aVals['reference_url'],
                                'image_url' => $aVals['image_url'],
                                'time_stamp'=> PHPFOX_TIME)
                                , 'license_id =' . $aVals['id']);    
            }else
            {
                $this->database()->update($this->_sTable, array(
                                    'license_name' => $aVals['name'],
                                    'reference_url'=> $aVals['reference_url'],
                                    'time_stamp'=> PHPFOX_TIME)
                                    , 'license_id =' . $aVals['id']);
            }
            return $aVals['id'];
        }
        return false;
    }
    public function get()
    {
        return $this->database()->select('*')
                            ->from($this->_sTable)
                            ->execute('getRows');
    }
    public function getById($iId)
    {
        return $this->database()->select('*')
                        ->from($this->_sTable)
                        ->where('license_id =' . $iId)
                        ->execute('getRow');
    }
    public function delete($iId)
    {
        $this->database()->delete($this->_sTable,'license_id =' . $iId);
    }
    public function deleteMultiple($aIds)
    {
        foreach($aIds as $iId)
        {
            $this->delete($iId);   
        }
        return true;
    }
}  
?>
