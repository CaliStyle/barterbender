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
class Document_Service_Category_Process extends Core_Service_Systems_Category_Process
{
    private $_iStringLengthCategoryName;
    /**
     * Class constructor
     */    
    public function __construct()
    {
        parent::__construct();
        $this->_iStringLengthCategoryName = 40;
        $this->_sTable = Phpfox::getT('document_category');
        $this->_aLanguages = Phpfox::getService('language')->getAll();
    }
    
    public function add($aVals, $sName = 'name')
    {
        $finalPhrase = $this->addPhrase($aVals, $sName);
        $iCategoryId = $this->database()->insert($this->_sTable, [
            'parent_id' => (!empty($aVals['parent_id']) ? (int) $aVals['parent_id'] : 0),
            'is_active' => 1,
            'name' => $finalPhrase,
            'name_url' => $finalPhrase,
            'time_stamp' => PHPFOX_TIME
        ]);

        $this->cache()->remove();
        return $iCategoryId;
    }

    public function update($iId, $aVals)
    {
        if ($iId == $aVals['parent_id']) {
            return Phpfox_Error::set(_p('parent_category_and_child_is_the_same'));
        }

        $aCategory = db()->select('*')
            ->from($this->_sTable)
            ->where('category_id =' . $iId)
            ->execute('getRow');

        $aLanguages = Phpfox::getService('language')->getAll();

        // Update phrase
        if (Phpfox::isPhrase($aVals['name'])){
            foreach ($aLanguages as $aLanguage){
                if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])){
                    Phpfox::getService('ban')->checkAutomaticBan($aVals['name_' . $aLanguage['language_id']]);

                    $name = $aVals['name_' . $aLanguage['language_id']];
                    Phpfox::getService('language.phrase.process')->updateVarName($aLanguage['language_id'], $aVals['name'], $name);
                }
                else {
                    return Phpfox_Error::set((_p('Provide a "{{ language_name }}" name.', ['language_name' => $aLanguage['title']])));
                }

                if (strlen($aVals['name_' . $aLanguage['language_id']]) > $this->_iStringLengthCategoryName) {
                    return Phpfox_Error::set(_p('Category "{{ language_name }}" name must be less than {{ limit }}', ['limit' => $this->_iStringLengthCategoryName, 'language_name' => $aLanguage['title']]));
                }
            }
        } else {
            $name = $aVals['name_'.$aLanguages[0]['language_id']];
            $phrase_var_name = 'document_category_' . md5('Document Category'. $name . PHPFOX_TIME);

            //Validate phrases
            $aText = [];
            foreach ($aLanguages as $aLanguage){
                if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])){
                    Phpfox::getService('ban')->checkAutomaticBan($aVals['name_' . $aLanguage['language_id']]);
                    $aText[$aLanguage['language_id']] = $aVals['name_' . $aLanguage['language_id']];
                }
                else {
                    return Phpfox_Error::set((_p('Provide a "{{ language_name }}" name.', ['language_name' => $aLanguage['title']])));
                }

                if (strlen($aVals['name_' . $aLanguage['language_id']]) > $this->_iStringLengthCategoryName) {
                    return Phpfox_Error::set(_p('Category "{{ language_name }}" name must be less than {{ limit }}', ['limit' => $this->_iStringLengthCategoryName, 'language_name' => $aLanguage['title']]));
                }

                $aValsPhrase = [
                    'var_name' => $phrase_var_name,
                    'text' => $aText
                ];
            }
            $aVals['name'] = Phpfox::getService('language.phrase.process')->add($aValsPhrase);
        }

        $this->database()->update($this->_sTable, array('name' => $aVals['name'], 'parent_id' => (int) $aVals['parent_id']), 'category_id = ' . (int) $iId);


        if (($aVals['parent_id'] != $aCategory['parent_id'])) {

            $aDocuments = $this->database()->select('document_id')
                ->from(Phpfox::getT('document_category_data'))
                ->where("category_id = " . (int)$aCategory['category_id'])
                ->execute('getSlaveRows');

            if ($aCategory['parent_id'] > 0) {
                if ($aVals['parent_id'] == 0) {
                    foreach ($aDocuments as $aDoc) {
                        $this->database()->delete(Phpfox::getT('document_category_data'),
                            ['document_id' => $aDoc['document_id'], 'category_id' => $aCategory['parent_id']]);
                    }
                } else {
                    foreach ($aDocuments as $aDoc) {
                        $this->database()->update(Phpfox::getT('document_category_data'), ['category_id' => $aVals['parent_id']],
                            ['document_id' => $aDoc['document_id'], 'category_id' => $aCategory['parent_id']]);
                    }
                }
            } else {
                foreach ($aDocuments as $aDoc) {
                    $this->database()->insert(Phpfox::getT('document_category_data'),
                        ['document_id' => $aDoc['document_id'], 'category_id' => $aVals['parent_id']]);
                }
            }
        }

        $this->cache()->remove();

        return true;
    }
    
    public function delete($iId)
    {
        $this->database()->update($this->_sTable, array('parent_id' => 0), 'parent_id = ' . (int) $iId);

        //Delete phrase of category
        $aCategory = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('category_id=' . (int) $iId)
            ->execute('getSlaveRow');

        if (isset($aCategory['name']) && Phpfox::isPhrase($aCategory['name'])){
            Phpfox::getService('language.process')->delete($aCategory['name'], true);
        }

        $this->database()->delete($this->_sTable, 'category_id = ' . (int) $iId);
        
        $this->cache()->remove();

        return true;
    }
    
   
    public function updateOrder($aVals)
    {
        foreach ($aVals as $iId => $iOrder)
        {
            $this->database()->update($this->_sTable, array('ordering' => $iOrder), 'category_id = ' . (int) $iId);
        }
        
        $this->cache()->remove();

        return true;
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
        if ($sPlugin = Phpfox_Plugin::get('document.service_category_process__call'))
        {
            return eval($sPlugin);
        }
            
        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

    public function updateActivity($iId, $iType)
    {
        Phpfox::isAdmin(true);
        $this->database()->update(($this->_sTable), array('is_active' => (int)($iType == '1' ? 1 : 0)), 'category_id' . ' = ' . (int)$iId);

        $this->cache()->remove();
    }
}

?>