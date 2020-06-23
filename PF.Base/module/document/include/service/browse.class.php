<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author          Raymond Benc
 * @package         Phpfox_Service
 * @version         $Id: browse.class.php 414 2009-04-17 23:31:59Z Raymond_Benc $
 */
class Document_Service_Browse extends Phpfox_Service 
{    
    private $_sCategory = null;    
    
    private $_aCallback = false;
    
    private $_sTag = null;
    
    private $_bFull = false;
    
    /**
     * Class constructor
     */    
    public function __construct()
    {    
        $this->_sTable = Phpfox::getT('document');
    }
    
    public function query()
    {
        $this->database()->select('dt.*, ')->join(Phpfox::getT('document_text'), 'dt', 'dt.document_id = m.document_id');    
    }
    
    public function processRows(&$aRows)
    {
        foreach ($aRows as $iKey => $aRow)
        {                
            $aRows[$iKey]['link'] = ($this->_aCallback !== false ? Phpfox::getLib('url')->makeUrl($this->_aCallback['url'][0], array_merge($this->_aCallback['url'][1], array($aRow['title']))) : Phpfox::permalink('document', $aRow['document_id'], $aRow['title']));
        }
    }
    
    public function category($sCategory)
    {
        $this->_sCategory = $sCategory;
        
        return $this;
    }
    
    public function callback($aCallback)
    {
        $this->_aCallback = $aCallback;
        
        return $this;
    }    
    
    public function tag($sTag)
    {
        $this->_sTag = $sTag;
        
        return $this;
    }
    
    public function full($bFull)
    {
        $this->_bFull = $bFull;
        
        return $this;
    }
    
    public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false)
    {
        if (Phpfox::isModule('friend')) {
            if (Phpfox::getService('friend')->queryJoin($bNoQueryFriend)) {
                $this->database()->join(Phpfox::getT('friend'), 'friends',
                    'friends.user_id = m.user_id AND friends.friend_user_id = ' . Phpfox::getUserId());
            }
        }
         /*
        if ($this->_sTag != null)
        {
            $this->database()->innerJoin(Phpfox::getT('tag'), 'tag', 'tag.item_id = m.document_id AND tag.category_id = \''.(defined('PHPFOX_GROUP_VIEW') ? 'document_group' : 'document').'\'');
            if (!$bIsCount)
            {
                $this->database()->group('m.document_id');
            }            
        }*/
        if ($this->request()->get('req2') == 'tag' || $this->request()->get('req3') == 'tag')
        {
            $this->database()->innerJoin(Phpfox::getT('tag'), 'tag', 'tag.item_id = m.document_id AND tag.category_id = \'document\'');    
        }
        
        if ($this->_sCategory !== null)
        {        
            $this->database()->innerJoin(Phpfox::getT('document_category_data'), 'mcd', 'mcd.document_id = m.document_id');
            if (!$bIsCount)
            {
                $this->database()->group('m.document_id');
            }
        }  
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
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('document.service_browse__call'))
        {
            return eval($sPlugin);
        }
            
        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }    
}

?>