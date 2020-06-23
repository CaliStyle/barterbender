<?php

namespace Apps\P_AdvMarketplace\Service\Customfield;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Service;
use Phpfox_Error;

class Group extends Phpfox_Service
{
    public function __construct()
    {
    }

    public function get()
    {
        $aGroups = $this->database()->select('cg.*')
            ->from(phpfox::getT('advancedmarketplace_custom_group'), 'cg')
            //->join(Phpfox::getT('module'), 'm', 'm.module_id = cg.module_id AND m.is_active = 1')
            ->order('cg.ordering ASC')
            ->execute('getSlaveRows');

        return $aGroups;
    }

    public function getGroupsByCatId($iCatId)
    {
        $aGroupIds = phpfox::getLib('database')->select('cg.group_id')
            ->from(phpfox::getT('advancedmarketplace_category_customgroup_data'), 'cg')
            ->where('cg.category_id = ' . $iCatId)
            ->execute('getFields');

        return $aGroupIds;
    }

    public function getFieldsByCatId($iCatId)
    {
        $aGroupIds = phpfox::getLib('database')->select('cd.group_id, cg.phrase_var_name as group_name')
            ->from(phpfox::getT('advancedmarketplace_category_customgroup_data'), 'cd')
            ->join(phpfox::getT('advancedmarketplace_custom_group'), 'cg', 'cg.group_id = cd.group_id')
            ->where('cg.is_active = 1 and cd.category_id = ' . $iCatId)
            ->order('cg.ordering ASC')
            ->execute('getRows');
        if (!empty($aGroupIds)) {
            $aFields = array();
            foreach ($aGroupIds as $iId) {
                $aFields[$iId['group_id']] = array();
                $aFields[$iId['group_id']]['id'] = $iId['group_id'];
                $aFields[$iId['group_id']]['group_name'] = $iId['group_name'];
                $aCustomFields = phpfox::getLib('database')->select('cf.var_type, cf.phrase_var_name as field_name, cf.field_id')
                    ->from(phpfox::getT('advancedmarketplace_custom_field'), 'cf')
                    ->where('cf.is_active = 1 and cf.group_id = ' . $iId['group_id'])
                    ->order('cf.ordering ASC')
                    ->execute('getRows');
                //var_dump($aCustomFields); die();

                foreach ($aCustomFields as $aField) {
                    if (!empty($aField)) {
                        $aFields[$iId['group_id']][$aField['field_id']] = array();
                        $aFields[$iId['group_id']][$aField['field_id']]['field_name'] = $aField['field_name'];
                        $aFields[$iId['group_id']][$aField['field_id']]['var_type'] = $aField['var_type'];
                        $aOptions = phpfox::getLib('database')->select('co.phrase_var_name as option_name, co.option_id')
                            ->from(phpfox::getT('advancedmarketplace_custom_option'), 'co')
                            ->where('co.field_id = ' . $aField['field_id'])
                            ->execute('getRows');
                        $aFields[$iId['group_id']][$aField['field_id']]['html'] = $this->_getConvertHtmlField($aField['var_type'],
                            $aOptions, $aField['field_name']);
                        /*foreach($aOptions as $aOption)
                         {
                            $aFields[$iId['group_id']][$aField['field_id']][$aOption['option_id']] = array();
                            $aFields[$iId['group_id']][$aField['field_id']][$aOption['option_id']]['html'] = $aOption['option_name'];
                            }*/
                    }

                }
                //$aFields['group_id']['custom_field']['custom_option'] = $aFields;

                /*$aFields = phpfox::getLib('database')->select('cg.phrase_var_name as group_name, cg.is_active as group_active, cf.var_type, cf.phrase_var_name as field_name, co.phrase_var_name as option_name')
                 ->from(phpfox::getT('advancedmarketplace_custom_group'), 'cg')
                 ->join(phpfox::getT('advancedmarketplace_custom_field'), 'cf', 'cf.group_id = cg.group_id')
                 ->join(phpfox::getT('advancedmarketplace_custom_option'), 'co', 'co.field_id = cf.field_id')
                 ->where('cg.group_id = '.$iId['group_id'])
                 ->group('cg.group_id')
                 ->execute('getRows');*/
            }

            return $aFields;
        }

        return array();
    }

    private function _getConvertHtmlField($var_type, $aOptions, $fieldname)
    {

        if (empty($var_type)) {
            return '';
        }

        $sHtml = '<div class="table_left">' . _p($fieldname) . '</div>';

        switch ($var_type) {
            case 'select':
                $sHtml .= '<select>';
                foreach ($aOptions as $aOption) {
                    $sHtml .= "<option value=\"" . $aOption['option_id'] . "\">" . _p($aOption['option_name']) . "</option>";
                }
                $sHtml .= '</select><br/>';
                break;
            case 'textarea':
                $sHtml .= '<div class="table_right">';
                $sHtml .= '<textarea class="custom_textarea form-control" cols="60" style="width:90%;" rows="8" name="custom">';
                $sHtml .= '</textarea><br/>';
                break;
            case 'text':
                $sHtml .= '<div class="table_right">';
                $sHtml .= '<input size="30" maxlength="255" name="custom">';
                $sHtml .= '<br/>';
                break;
            case 'radio':
                $sHtml .= '<div class="table_right">';
                foreach ($aOptions as $aOption) {
                    $sHtml .= '<div class="custom_block_form_radio">';
                    if (isset($aOption['selected']) && $aOption['selected'] == true) {
                        $sHtml .= '<input checked="checked" name="custom"  type="radio" value="' . $aOption['option_id'] . '"><label>' . _p($aOption['option_name']) . '</label>';
                    } else {
                        $sHtml .= '<input  type="radio" name="custom" value="' . $aOption['option_id'] . '"><label>' . _p($aOption['option_name']) . '</label>';
                    }
                    $sHtml .= '</div>';
                }
                $sHtml .= '</div>';
                break;
            case 'multiselect':
                $sHtml .= '<div class="table_right">';
                $sHtml .= '<select name="custom"  multiple="multiple">';
                foreach ($aOptions as $aOption) {
                    if (isset($aOption['value']) && isset($aOption['selected']) && $aOption['selected'] == true) {
                        $sHtml .= '<option selected="selected"   value="' . $aOption['option_id'] . '">' . _p($aOption['option_name']) . '</option>';
                    } else {
                        $sHtml .= '<option value="' . $aOption['option_id'] . '">' . _p($aOption['option_name']) . '</option>';
                    }
                }
                $sHtml .= '</select>';
                $sHtml .= '</div>';
                break;
            case 'checkbox':
                $sHtml .= '<div class="table_right">';
                foreach ($aOptions as $aOption) {
                    $sHtml .= '<div class="custom_block_form_checkbox">';
                    if (isset($aOption['selected']) && $aOption['selected'] == true) {
                        $sHtml .= '<input checked="checked" name="custom"  type="checkbox" value="' . $aOption['option_id'] . '"><label>' . _p($aOption['option_name']) . '</label>';
                    } else {
                        $sHtml .= '<input  type="checkbox" name="custom" value="' . $aOption['option_id'] . '"><label>' . _p($aOption['option_name']) . '</label>';
                    }
                    $sHtml .= '</div>';
                }
                $sHtml .= '</div>';
                break;
            default:
                break;
        }

        return $sHtml;
    }

    public function getForListing($iCatId)
    {
        $aFields = $this->database()->select('cf.*')
            ->from(phpfox::getT('advancedmarketplace_custom_field'), 'cf')
            ->order('cf.ordering ASC')
            ->execute('getRows');

        $aCustomFields = array();
        foreach ($aFields as $aField) {
            $aCustomFields[$aField['group_id']][] = $aField;
        }

        $aGroups = $this->database()->select('cg.*')
            ->from(Phpfox::getT('advancedmarketplace_custom_group'), 'cg')
            ->join(Phpfox::getT('advancedmarketplace_category_customgroup_data'), 'cd', 'cd.group_id = cg.group_id')
            ->where('cd.category_id = ' . $iCatId)
            ->order('cg.ordering ASC')
            ->execute('getSlaveRows');

        foreach ($aGroups as $iKey => $aGroup) {
            if (isset($aCustomFields[$aGroup['group_id']])) {
                $aGroups[$iKey]['child'] = $aCustomFields[$aGroup['group_id']];
            }
        }

        if (isset($aCustomFields[0])) {
            $aGroups['PHPFOX_EMPTY_GROUP']['child'] = $aCustomFields[0];
        }

        return $aGroups;
    }

    public function deleteGroup($iId)
    {
        Phpfox::getUserParam('custom.can_manage_custom_fields', true);

        $aGroup = $this->database()->select('*')
            ->from(phpfox::getT('advancedmarketplace_custom_group'))
            ->where('group_id = ' . (int)$iId)
            ->execute('getRow');

        if (!isset($aGroup['group_id'])) {
            return Phpfox_Error::set(_p('custom.unable_to_find_the_group_you_plan_on_deleting'));
        }

        list($sModule, $sPhrase) = explode('.', $aGroup['phrase_var_name']);

        $this->database()->delete(Phpfox::getT('language_phrase'),
            'module_id = \'' . $sModule . '\' AND var_name = \'' . $sPhrase . '\'');

        $this->database()->update(Phpfox::getT('advancedmarketplace_custom_field'), array('group_id' => 0),
            'group_id = ' . $aGroup['group_id']);

        $this->database()->delete(phpfox::getT('advancedmarketplace_custom_group'),
            'group_id = ' . $aGroup['group_id']);

        $this->cache()->remove('custom_field');

        return true;
    }

    public function getGroupForEdit($iId)
    {
        $aGroup = $this->database()->select('*')
            ->from(phpfox::getT('advancedmarketplace_custom_group'))
            ->where('group_id = ' . (int)$iId)
            ->execute('getRow');

        list(, $sVarName) = explode('.', $aGroup['phrase_var_name']);

        $aPhrases = $this->database()->select('language_id, text')
            ->from(Phpfox::getT('language_phrase'))
            ->where('var_name = \'' . $this->database()->escape($sVarName) . '\'')
            ->execute('getSlaveRows');

        foreach ($aPhrases as $aPhrase) {
            $aGroup['group'][$aGroup['phrase_var_name']][$aPhrase['language_id']] = $aPhrase['text'];
        }

        return $aGroup;
    }

    public function getFieldForCustomEdit($iId)
    {
        $aField = $this->database()->select('cf.*')
            ->from(Phpfox::getT('advancedmarketplace_custom_field'), 'cf')
            ->where('cf.field_id = ' . (int)$iId)
            ->execute('getRow');

        list(, $sVarName) = explode('.', $aField['phrase_var_name']);

        // Get the name of the field in every language
        $aPhrases = $this->database()->select('language_id, text')
            ->from(Phpfox::getT('language_phrase'))
            ->where('var_name = \'' . $this->database()->escape($sVarName) . '\'')
            ->execute('getSlaveRows');

        foreach ($aPhrases as $aPhrase) {
            $aField['name'][$aField['phrase_var_name']][$aPhrase['language_id']] = $aPhrase['text'];
        }

        if ($aField['var_type'] == 'select' || $aField['var_type'] == 'multiselect'
            || $aField['var_type'] == 'radio' || $aField['var_type'] == 'checkbox') {
            $aOptions = $this->database()->select('option_id, field_id, phrase_var_name')
                ->from(Phpfox::getT('advancedmarketplace_custom_option'))
                ->where('field_id = ' . $aField['field_id'])
                ->execute('getSlaveRows');

            foreach ($aOptions as $iKey => $aOption) {
                list(, $sVarName) = explode('.', $aOption['phrase_var_name']);

                $aPhrases = $this->database()->select('language_id, text, var_name')
                    ->from(Phpfox::getT('language_phrase'))
                    ->where('var_name = \'' . $this->database()->escape($sVarName) . '\'')
                    ->execute('getSlaveRows');

                foreach ($aPhrases as $aPhrase) {
                    if (!isset($aField['option'][$aOption['option_id']][$aOption['phrase_var_name']][$aPhrase['language_id']])) {
                        $aField['option'][$aOption['option_id']][$aOption['phrase_var_name']][$aPhrase['language_id']] = array();
                    }
                    if ((preg_match('/[.]*_feed/', $aPhrase['var_name'])) > 0) {
                        $aField['option'][$aOption['option_id']][$aOption['phrase_var_name']][$aPhrase['language_id']]['feed'] = $aPhrase['text'];
                    } else {
                        $aField['option'][$aOption['option_id']][$aOption['phrase_var_name']][$aPhrase['language_id']]['text'] = $aPhrase['text'];
                    }

                }
            }
        }

        return $aField;
    }
}
