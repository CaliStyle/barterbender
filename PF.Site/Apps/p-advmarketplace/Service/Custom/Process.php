<?php

namespace Apps\P_AdvMarketplace\Service\Custom;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Service;
use Phpfox_Error;

class Process extends Phpfox_Service
{
    private $_aCategories = array();

    public function __construct()
    {
    }

    public function _verifyGroup($aVals)
    {
        if (!isset($aVals['category'])) {
            return Phpfox_Error::set(_p('advancedmarketplace.provide_a_category_this_listing_will_belong_to'));
        }

        foreach ($aVals['category'] as $iCategory) {
            if (empty($iCategory)) {
                continue;
            }

            if (!is_numeric($iCategory)) {
                continue;
            }

            $this->_aCategories[] = $iCategory;
        }

        if (!count($this->_aCategories)) {
            return Phpfox_Error::set(_p('advancedmarketplace.provide_a_category_this_listing_will_belong_to'));
        }

        return true;
    }

    public function addCustomGroup($aVals)
    {
        phpfox::isUser(true);

        foreach ($aVals['group'] as $sPhrase) {
            if (empty($sPhrase)) {
                continue;
            }

            $sVarName = Phpfox::getService('language.phrase.process')->prepare($sPhrase);

            break;
        }
        if (empty($sVarName)) {
            return Phpfox_Error::set(_p('custom.provide_a_name_for_this_group'));
        }

        if (!$this->_verifyGroup($aVals)) {
            return false;
        }

        $sVarName = 'custom_group_' . $sVarName;

        if ($this->database()->select('COUNT(*)')->from(Phpfox::getT('advancedmarketplace_custom_group'))->where('phrase_var_name = \'' . $this->database()->escape($aVals['module_id'] . '.' . $sVarName) . '\'')->execute('getField')) {
            return Phpfox_Error::set(_p('custom.there_is_already_a_group_with_the_same_name'));
        }

        $iId = $this->database()->insert(Phpfox::getT('advancedmarketplace_custom_group'), array(
                'phrase_var_name' => $aVals['module_id'] . '.' . $sVarName,
                'ordering' => isset($aVals['ordering']) ? $aVals['ordering'] : 0,
                'is_active' => isset($aVals['is_active']) ? $aVals['is_active'] : 1
            )
        );
        if ($iId) {
            Phpfox::getService('language.phrase.process')->add(array(
                'var_name' => $sVarName,
                'module' => $aVals['module_id'] . '|' . $aVals['module_id'],
                'product_id' => $aVals['product_id'],
                'text' => $aVals['group']
            ), true
            );

            foreach ($aVals['category'] as $iCategory) {
                if (empty($iCategory)) {
                    continue;
                }

                if (!is_numeric($iCategory)) {
                    continue;
                }

                phpfox::getLib('database')->insert(Phpfox::getT('advancedmarketplace_category_customgroup_data'),
                    array('category_id' => $iCategory, 'group_id' => $iId));
            }
        }

        return $iId;
    }

    public function addCustomField($aVals)
    {
        $sVarName = '';
        foreach ($aVals['name'] as $iId => $aText) {
            if (empty($aText['text'])) {
                continue;
            }

            $sVarName = Phpfox::getService('language.phrase.process')->prepare($aText['text']);

            break;
        }
        if (empty($sVarName)) {
            return Phpfox_Error::set(_p('custom.provide_a_name_for_the_custom_field'));
        }
        $sFieldName = substr($sVarName, 0, 20);
        $sVarName = 'custom_' . $sVarName;
        if (empty($aVals['group_id'])) {
            return Phpfox_Error::set('Please select the group');
        }
        $bAddToOptions = false;
        switch ($aVals['var_type']) {
            case 'select':
            case 'radio':
                $sTypeName = 'VARCHAR(150)';
                $bAddToOptions = true;
                break;
            case 'multiselect':
            case 'checkbox':
                $sTypeName = 'MEDIUMTEXT';
                $bAddToOptions = true;
                break;
            case 'text':
                $sTypeName = 'VARCHAR(255)';
                break;
            case 'textarea':
                $sTypeName = 'MEDIUMTEXT';
                break;
            default:
                return Phpfox_Error::set(_p('custom.not_a_valid_type_of_custom_field'));
                break;
        }

        if ($bAddToOptions && !empty($aVals['option']) && is_array($aVals['option'])) {
            $iTotalOptions = 0;
            foreach ($aVals['option'] as $aOption) {
                foreach ($aOption as $aLanguage) {
                    if (isset($aLanguage['text']) && !empty($aLanguage['text'])) {
                        $iTotalOptions++;
                        // there may be more languages, counting them would give an incorrect number of options
                        break;
                    }
                }
            }

            if (!$iTotalOptions) {
                return Phpfox_Error::set(_p('custom.you_have_selected_that_this_field_is_a_select_custom_field_which_requires_at_least_one_option'));
            }
        }

        $iCustomFieldCount = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('advancedmarketplace_custom_field'))
            ->where('phrase_var_name = \'' . $this->database()->escape($aVals['module_id'] . '.' . $sVarName) . '\'')
            ->execute('getField');

        if ($iCustomFieldCount > 0) {
            $sVarName = $sVarName . ($iCustomFieldCount + 1);
            $sFieldName = $sFieldName . ($iCustomFieldCount + 1);
        }

        $aSql = array(
            'field_name' => $sFieldName,
            //	'module_id' => $aVals['module_id'],
            //	'product_id' => $aVals['product_id'],
            //'type_id' => $aVals['type_id'],
            'group_id' => $aVals['group_id'],
            'phrase_var_name' => $aVals['module_id'] . '.' . $sVarName,
            'type_name' => $sTypeName,
            'var_type' => $aVals['var_type'],
            'is_required' => (isset($aVals['is_required']) ? (int)$aVals['is_required'] : 0),
            'ordering' => (isset($aVals['ordering']) ? (int)$aVals['ordering'] : 0),
            'is_active' => (isset($aVals['is_active']) && !empty($aVals['is_active'])) ? $aVals['is_acitve'] : '0'
        );

        switch ($aVals['var_type']) {
            case 'select':
            case 'multiselect':
            case 'checkbox':
            case 'radio':
                break;
            default:
        }

        // Insert into DB
        $iFieldId = $this->database()->insert(Phpfox::getT('advancedmarketplace_custom_field'), $aSql);

        if ($bAddToOptions && !empty($aVals['option']) && is_array($aVals['option'])) {
            $this->_addOptions($iFieldId, $aVals);
        }
        // Add the new phrase
        if (!Phpfox::getService('language.phrase')->isValid($aVals['module_id'] . '.' . $sVarName)) {
            foreach ($aVals['name'] as $sLang => $aName) {
                Phpfox::getService('language.phrase.process')->add(array(
                    'var_name' => $sVarName,
                    'module' => $aVals['module_id'] . '|' . $aVals['module_id'],
                    'product_id' => $aVals['product_id'],
                    'text' => array($sLang => $aName['text'])
                ), true
                );
            }

        }

        $this->cache()->remove();

        return array(
            $iFieldId,
            $this->_aOptions
        );

    }

    private function _addOptions($iFieldId, &$aVals)
    {
        // it 	adds a new language phrase and the var_name is in the form "cf_option_" + <field_id> + <seq_number>
        // but the sequence number may overlap an existing option, so we need to make sure this value is unique
        $aExisting = array();
        if (isset($aVals['current'])) {
            foreach ($aVals['current'] as $sVarName => $aVal) {
                $aExisting[] = str_replace('advancedmarketplace.cf_option_' . $aVals['field_id'] . '_', '', $sVarName);
            }
        }

        foreach ($aVals['option'] as $iKey => $aOptions) {
            if (isset($aVals['option'][$iKey]['added']) && $aVals['option'][$iKey]['added'] == true) {
                continue;
            }
            $aOptionsAdded = array();
            $iSeqNumber = in_array($iKey, $aExisting) ? (max($aExisting) + 1) : $iKey;
            $aExisting[] = $iSeqNumber;
            foreach ($aOptions as $sLang => $aOption) {
                if (empty($aOption['text'])) {
                    continue;
                }

                $sPhraseVar = 'cf_option_' . $iFieldId . '_' . $iSeqNumber;

                Phpfox::getService('language.phrase.process')->add(array(
                    'var_name' => $sPhraseVar,
                    //'cf_option_' . Phpfox::getService('language.phrase.process')->prepare($aOption['text']),//$sOptionVarName . '_feed',
                    'module' => $aVals['module_id'] . '|' . $aVals['module_id'],
                    'product_id' => $aVals['product_id'],
                    'text' => array($sLang => $aOption['text'])
                ));

                // Only add one option per language
                if (!in_array($iKey, $aOptionsAdded)) {
                    $this->_aOptions[$iKey . $sLang] = $this->database()->insert(Phpfox::getT('advancedmarketplace_custom_option'),
                        array(
                            'field_id' => $iFieldId,
                            'phrase_var_name' => $aVals['module_id'] . '.' . $sPhraseVar
                        )
                    );
                    $aOptionsAdded[] = $iKey;
                }
            }
            $aVals['option'][$iKey]['added'] = true;
        }

        return true;
    }

    public function toggleFieldActivity($iId)
    {
        Phpfox::getUserParam('custom.can_manage_custom_fields', true);

        $aField = $this->database()->select('field_id, is_active')
            ->from(Phpfox::getT('advancedmarketplace_custom_field'))
            ->where('field_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aField['field_id'])) {
            return Phpfox_Error::set(_p('custom.unable_to_find_the_custom_field'));
        }

        $this->database()->update(Phpfox::getT('advancedmarketplace_custom_field'),
            array('is_active' => ($aField['is_active'] ? 0 : 1)), 'field_id = ' . $aField['field_id']);

        $this->cache()->remove('custom_field');

        return true;
    }

    public function toggleGroupActivity($iId)
    {
        $aField = $this->database()->select('group_id, is_active')
            ->from(Phpfox::getT('advancedmarketplace_custom_group'))
            ->where('group_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aField['group_id'])) {
            return Phpfox_Error::set(_p('custom.unable_to_find_the_custom_group'));
        }

        $this->database()->update(Phpfox::getT('advancedmarketplace_custom_group'),
            array('is_active' => ($aField['is_active'] ? 0 : 1)), 'group_id = ' . $aField['group_id']);

        $this->cache()->remove('custom_field');
        $this->cache()->remove('custom_public_');

        return true;
    }

    public function updateGroupOrder($aVals)
    {
        foreach ($aVals as $iId => $iOrder) {
            $this->database()->update(Phpfox::getT('advancedmarketplace_custom_group'),
                array('ordering' => (int)$iOrder), 'group_id = ' . (int)$iId);
        }

        $this->cache()->remove('custom_field');

        return true;
    }

    public function updateFieldOrder($aVals)
    {

        foreach ($aVals as $iId => $iOrder) {
            $this->database()->update(Phpfox::getT('advancedmarketplace_custom_field'),
                array('ordering' => (int)$iOrder), 'field_id = ' . (int)$iId);
        }

        $this->cache()->remove('custom_field');

        return true;
    }

    public function updateGroup($iId, $aVals)
    {
        foreach ($aVals['group'] as $sKey => $aPhrases) {
            foreach ($aPhrases as $sLang => $sValue) {
                if (Phpfox::getService('language.phrase')->isValid($sKey, $sLang)) {
                    Phpfox::getService('language.phrase.process')->updateVarName($sLang, $sKey, $sValue);
                } else {
                    list($sModule, $sVarName) = explode('.', $sKey);

                    // Add the new phrase
                    Phpfox::getService('language.phrase.process')->add(array(
                        'var_name' => $sVarName,
                        'module' => $sModule . '|' . $sModule,
                        'product_id' => $aVals['product_id'],
                        'text' => array($sLang => $sValue)
                    ), true
                    );
                }
            }
        }

        return true;
    }

    public function deleteField($iId)
    {
        $aField = $this->database()->select('*')
            ->from(phpfox::getT('advancedmarketplace_custom_field'))
            ->where('field_id = ' . (int)$iId)
            ->execute('getRow');

        if (!isset($aField['field_id'])) {
            return Phpfox_Error::set(_p('custom.unable_to_find_the_custom_field_you_want_to_delete'));
        }

        list($sModule, $sPhrase) = explode('.', $aField['phrase_var_name']);

        $this->database()->delete(Phpfox::getT('language_phrase'),
            'module_id = \'' . $sModule . '\' AND var_name = \'' . $sPhrase . '\'');

        $aOptions = $this->database()->select('*')
            ->from(Phpfox::getT('custom_option'))
            ->where('field_id = ' . $aField['field_id'])
            ->execute('getRows');

        foreach ($aOptions as $aOption) {
            list($sModule, $sPhrase) = explode('.', $aOption['phrase_var_name']);

            $this->database()->delete(Phpfox::getT('language_phrase'),
                'module_id = \'' . $sModule . '\' AND var_name = \'' . $sPhrase . '\'');
        }

        $this->database()->delete(Phpfox::getT('custom_option'), 'field_id = ' . $aField['field_id']);
        $this->database()->delete(phpfox::getT('advancedmarketplace_custom_field'),
            'field_id = ' . $aField['field_id']);
        $this->cache()->remove();

        return true;
    }
}
