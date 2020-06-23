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

class Document_Service_Document extends Phpfox_Service
{
  
      
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('document');
    }
         
    public function makeUrl($sUser, $sUrl, $aCallback = null)
    {
        return Phpfox::getLib('url')->makeUrl($sUser, array('document', $sUrl));
    }
    
    public function getPendingTotal()
    {
        return $this->database()->select('COUNT(*)')
            ->from($this->_sTable,'d')
            ->join(Phpfox::getT('user'),'u','d.user_id = u.user_id')
            ->where('d.is_approved != 1')
            ->execute('getSlaveField');
    }
    
    public function getStatus($iId)
    {
        $aStatus = $this->database()->select('is_approved, is_featured, time_stamp')->from($this->_sTable)->where('document_id = '.(int)$iId)->execute('getSlaveRow');
        
        $aStatus['is_new'] = false;
        
        $new_documents_period = phpfox::getParam('document.new_documents_period');
        $previous_time = strtotime('-'.$new_documents_period.' day');
        
        if ($new_documents_period && $aStatus['time_stamp']>$previous_time)
        {
            $aStatus['is_new'] = true; 
        }
        
        return $aStatus;
    }
    
    public function getFeatured($sCondition = '', $iLimit = 3)
    {
        $sCond = 'd.is_approved = 1'.$sCondition;
        
        $aRows = $this->database()->select('d.*, ' . Phpfox::getUserField())
			->from($this->_sTable, 'd')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = d.user_id')
			->where($sCond.' AND d.is_featured = 1')
			->limit($iLimit)
			->execute('getSlaveRows');
        
        return $aRows;
    }
    
    public function getMostDiscussed($sCondition = '', $iLimit = 3)
    {
        $sCond = 'd.is_approved = 1'.$sCondition;
        
        $aRows = $this->database()->select('d.*, ' . Phpfox::getUserField())
			->from($this->_sTable, 'd')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = d.user_id')
			->where($sCond)
			->order('d.total_comment DESC')
			->limit($iLimit)
			->execute('getSlaveRows');
        
        return $aRows;
    }
    
    public function getScribdDocId($iId)
    {
        return $this->database()->select('doc_id')->from($this->_sTable)->where('document_id = '.(int)$iId)->execute('getSlaveField');
    }

    public function searchDocument($aConds, $sSort = 'ca.document_id ASC', $iPage = '', $iLimit = '')
    {


        $iCnt = $this->database()->select('COUNT(*)')
            ->from($this->_sTable, 'ca')
            ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = ca.user_id')
            ->where($aConds)
            ->execute('getSlaveField');
        $aItems = array();
        if ($iCnt)
        {
            $aItems = $this->database()->select('ca.*, ' . Phpfox::getUserField())
                ->from($this->_sTable, 'ca')
                ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = ca.user_id')
                ->where($aConds)
                ->limit($iPage, $iLimit, $iCnt)
                ->order('ca.time_stamp desc')
                ->execute('getSlaveRows');
        }

        return array($iCnt, $aItems);
    }
    public function getMyDocumentsTotal()
    {
        return  $this->database()->select('COUNT(*)')->from(Phpfox::getT('document'))->where('view_id = 0 AND user_id = ' . (int) Phpfox::getUserId())->execute('getSlaveField');
    }

    /**
     * Apply settings show document of pages / groups
     * @param $sPrefix
     * @return string
     */
    public function getConditionsForSettingPageGroup($sPrefix = 'm')
    {
        $aModules = ['document'];
        // Apply settings show document of pages / groups
        if (Phpfox::getParam('document.display_document_created_in_group') && Phpfox::isAppActive('PHPfox_Groups')) {
            $aModules[] = 'groups';
        }
        if (Phpfox::getParam('document.display_document_created_in_page') && Phpfox::isAppActive('Core_Pages')) {
            $aModules[] = 'pages';
        }

        (($sPlugin = Phpfox_Plugin::get('document.service_document_getconditionsforsettingpagegroup')) ? eval($sPlugin) : false);

        return ' AND ' . $sPrefix . '.module_id IN ("' . implode('","', $aModules) . '")';
    }
}  
?>
