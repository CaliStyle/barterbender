<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\YNC_Member\Service\Custom;

use Phpfox;
use Phpfox_Error;
use Phpfox_Service;

defined('PHPFOX') or exit('NO DICE!');

class Group extends Phpfox_Service
{

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ynmember_custom_group');
        $this->_sTableCustomField = Phpfox::getT('ynmember_custom_field');
    }

    public function getGroupForEdit($iGroupId)
    {
        $aGroup = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('group_id = ' . $iGroupId)
            ->order('ordering ASC')
            ->execute('getSlaveRow');

        if (count($aGroup) <= 0) {
            return array();
        }

        list($sModule, $sVarName) = explode('.', $aGroup['phrase_var_name']);

        $aPhrases = $this->database()->select('language_id, text')
            ->from(Phpfox::getT('language_phrase'))
            ->where('var_name = \'' . $this->database()->escape($sVarName) . '\'')
            ->execute('getSlaveRows');

        foreach ($aPhrases as $aPhrase) {
            $aGroup['group_name'][$aGroup['phrase_var_name']][$aPhrase['language_id']] = $aPhrase['text'];
        }

        $aMappingCustomFields = Phpfox::getService('ynmember.custom')->getCustomFieldByGroupId($iGroupId);

        $aGroup['customfield'] = $aMappingCustomFields;

        return $aGroup;
    }

    public function addGroup($aGroup)
    {

        $oParseInput = Phpfox::getLib('parse.input');
        $group_name = $aGroup['group_name'];
        foreach ($aGroup['group_name'] as $sPhrase) {
            if (empty($sPhrase)) {
                continue;
            }

            $sParsedVar = Phpfox::getService('language.phrase.process')->prepare($sPhrase);

            break;
        }
        if (empty($sParsedVar)) {
            return Phpfox_Error::set(_p('group_name_cannot_be_empty'));
        }

        // Add the new phrase
        $time = PHPFOX_TIME;
        $sModuleId = 'core';
        $sProductId = 'phpfox';
        Phpfox::getService('language.phrase.process')->add(array(
            'var_name' => $sParsedVar . '_' . $time,
            'module' => $sModuleId . '|' . $sModuleId,
            'product_id' => $sProductId,
            'text' => $aGroup['group_name']
        ), true
        );

        $iGroupId = $this->database()->insert($this->_sTable, array(
            "phrase_var_name" => "core." . $sParsedVar . '_' . $time,
            "is_active" => 1,
            "ordering" => NULL,
        ));

        /*update custom field group id*/
        if (isset($aGroup['customfield']) && count($aGroup['customfield']) > 0) {
            foreach ($aGroup['customfield'] as $iFieldId) {
                $this->database()->update($this->_sTableCustomField,
                    array(
                        "group_id" => $iGroupId
                    ),
                    'field_id = ' . $iFieldId
                );
            }
        }

        $this->cache()->remove();

        return $iGroupId;

    }

    public function updateGroup($iGroupId, $aGroup)
    {

        $sProductId = 'phpfox';
        foreach ($aGroup['group_name'] as $sKey => $aPhrases) {
            foreach ($aPhrases as $sLang => $sValue) {
                if (Phpfox::getService('language.phrase')->isValid($sKey, $sLang)) {
                    Phpfox::getService('language.phrase.process')->updateVarName($sLang, $sKey, $sValue);
                } else {
                    list($sModule, $sVarName) = explode('.', $sKey);

                    // Add the new phrase
                    Phpfox::getService('language.phrase.process')->add(array(
                        'var_name' => $sVarName,
                        'module' => $sModule . '|' . $sModule,
                        'product_id' => $sProductId,
                        'text' => array($sLang => $sValue)
                    ), true
                    );
                }
            }
        }

        $this->cache()->remove();

        /*update custom field group id*/
        if (isset($aGroup['customfield']) && count($aGroup['customfield']) > 0) {
            foreach ($aGroup['customfield'] as $iFieldId) {
                $this->database()->update($this->_sTableCustomField,
                    array(
                        "group_id" => $iGroupId
                    ),
                    'field_id = ' . $iFieldId
                );
            }
        }

        return true;
    }

    public function getForListing()
    {
        $aFields = $this->database()->select('cf.*')
            ->from($this->_sTableCustomField, 'cf')
            ->order('cf.ordering ASC')
            ->execute('getRows');

        $aCustomFields = array();
        foreach ($aFields as $aField) {
            $aCustomFields[$aField['group_id']][] = $aField;
        }

        $aGroups = $this->database()->select('cg.*')
            ->from($this->_sTable, 'cg')
            ->order('cg.ordering ASC')
            ->execute('getSlaveRows');

        foreach ($aGroups as $iKey => $aGroup) {
            if (isset($aCustomFields[$aGroup['group_id']])) {
                $aGroups[$iKey]['child'] = $aCustomFields[$aGroup['group_id']];
            }
        }

        // if (isset($aCustomFields[0]))
        // {
        //     $aGroups['PHPFOX_EMPTY_GROUP']['child'] = $aCustomFields[0];
        // }

        return $aGroups;
    }

    public function deleteGroup($iGroupId)
    {

        $aGroup = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('group_id = ' . (int)$iGroupId)
            ->execute('getRow');

        if (!isset($aGroup['group_id'])) {
            return Phpfox_Error::set(_p('unable_to_find_custom_group'));
        }

        list($sModule, $sPhrase) = explode('.', $aGroup['phrase_var_name']);

        $this->database()->delete(Phpfox::getT('language_phrase'), 'module_id = \'' . $sModule . '\' AND var_name = \'' . $sPhrase . '\'');

        $this->database()->delete($this->_sTableCustomField, 'group_id = ' . $aGroup['group_id']);

        $this->database()->delete($this->_sTable, 'group_id = ' . $aGroup['group_id']);

        $this->cache()->remove();

        return true;

    }

    public function toggleActivity($iId)
    {

        $aField = $this->database()->select('group_id, is_active')
            ->from($this->_sTable)
            ->where('group_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aField['group_id'])) {
            return Phpfox_Error::set(Phpfox::getPhrase('directory.unable_to_find_the_custom_group'));
        }

        $this->database()->update($this->_sTable, array('is_active' => ($aField['is_active'] ? 0 : 1)), 'group_id = ' . $aField['group_id']);
        $this->database()->update($this->_sTableCustomField, array('is_active' => ($aField['is_active'] ? 0 : 1)), 'group_id = ' . $aField['group_id']);

        $this->cache()->remove();

        return true;
    }

    public function updateOrder($aVals)
    {

        foreach ($aVals as $iId => $iOrder) {
            $this->database()->update($this->_sTable, array('ordering' => (int)$iOrder), 'group_id = ' . (int)$iId);
        }

        $this->cache()->remove();

        return true;
    }

}