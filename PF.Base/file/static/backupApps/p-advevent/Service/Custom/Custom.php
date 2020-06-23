<?php
namespace Apps\P_AdvEvent\Service\Custom;

use Phpfox;
use Phpfox_Service;
use Phpfox_Error;

class Custom extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('fevent_custom_field');
        $this->_sTableOption = Phpfox::getT('fevent_custom_option');
    }

    public function getForCustomEdit($iId)
    {
        $aField = $this->database()->select('*')->from($this->_sTable)->where('field_id = '.(int)$iId)->execute('getRow');

        list($sModule, $sVarName) = explode('.', $aField['phrase_var_name']);

        // Get the name of the field in every language
        $aPhrases = $this->database()->select('language_id, text')
            ->from(Phpfox::getT('language_phrase'))
            ->where('var_name = \'' . $this->database()->escape($sVarName) . '\'')
            ->execute('getSlaveRows');

        foreach ($aPhrases as $aPhrase)
        {
            $aField['name'][$aField['phrase_var_name']][$aPhrase['language_id']] = $aPhrase['text'];
        }

        if ($aField['var_type'] == 'select' || $aField['var_type'] == 'multiselect' || $aField['var_type'] == 'radio' || $aField['var_type'] == 'checkbox')
        {
            $aOptions = $this->database()->select('option_id, field_id, phrase_var_name')
                ->from($this->_sTableOption)
                ->where('field_id = ' . $aField['field_id'])
                ->order('option_id ASC')
                ->execute('getSlaveRows');

            foreach ($aOptions as $iKey => $aOption)
            {
                list($sModule, $sVarName) = explode('.', $aOption['phrase_var_name']);

                $aPhrases = $this->database()->select('language_id, text, var_name')
                    ->from(Phpfox::getT('language_phrase'))
                    ->where('var_name = \'' . $this->database()->escape($sVarName) . '\'')
                    ->execute('getSlaveRows');

                foreach ($aPhrases as $aPhrase)
                {
                    if (!isset($aField['option'][$aOption['option_id']][$aOption['phrase_var_name']][$aPhrase['language_id']]))
                    {
                        $aField['option'][$aOption['option_id']][$aOption['phrase_var_name']][$aPhrase['language_id']] = array();
                    }
                    $aField['option'][$aOption['option_id']][$aOption['phrase_var_name']][$aPhrase['language_id']]['text'] = $aPhrase['text'];
                }
            }
        }
        return $aField;
    }


    public function getFieldsByCateId($iId)
    {
        $aCustomFields = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('category_id = ' . (int) $iId . ' AND is_active = 1')
            ->order('ordering')
            ->execute('getRows');
        foreach($aCustomFields as $iKey => $aCustomField)
        {
            if($aCustomField["var_type"] != "text" && $aCustomField["var_type"] != "textarea")
            {
                $aCustomOptions = $this->getCustomOptions($aCustomField["field_id"]);
                if(isset($aCustomOptions[0]))
                {
                    $aCustomFields[$iKey]["options"] = $aCustomOptions;
                }
            }
        }
        return $aCustomFields;
    }

    public function getCustomOptions($iId)
    {
        return $this->database()->select('*')
            ->from(Phpfox::getT('fevent_custom_option'))
            ->where('field_id = ' . (int) $iId)
            ->execute('getRows');
    }

    public function getCustomFieldsForEdit($iEventId)
    {
        $aCustomFields = $this->database()->select('cf.*, cv.value')
            ->from($this->_sTable, 'cf')
            ->leftJoin(Phpfox::getT('fevent_custom_value'), 'cv', 'cf.field_id = cv.field_id')
            ->where('cv.event_id = ' . (int) $iEventId)
            ->order('cf.ordering')
            ->execute('getRows');

        foreach($aCustomFields as $iKey => $aCustomField)
        {
            if($aCustomField["var_type"] != "text" && $aCustomField["var_type"] != "textarea")
            {
                $aCustomOptions = $this->getCustomOptions($aCustomField["field_id"]);
                if(isset($aCustomOptions[0]))
                {
                    if(!empty($aCustomField['value']))
                    {
                        $aValues = json_decode($aCustomField['value'], true);
                        if(!is_array($aValues))
                        {
                            $aValues = array($aCustomField['value']);
                        }
                        foreach($aCustomOptions as $iKey2 => $aCustomOption)
                        {
                            if(in_array(_p($aCustomOption['phrase_var_name']), $aValues))
                            {
                                $aCustomOptions[$iKey2]["selected"] = true;
                            }
                        }
                    }
                    $aCustomFields[$iKey]["options"] = $aCustomOptions;
                }
            }
        }
        return $aCustomFields;
    }

    public function checkKeyCustomFields($aCustomFields, $key = 0){

        foreach($aCustomFields as $ikey=>$aCustom){
            if($aCustom['field_id'] == $key){
                return $ikey;
            }
        }
        return -1;
    }

    public function getCustomsByFieldIdAndEventId($field_id, $event_id)
    {
        return $this->database()->select('*')
            ->from(Phpfox::getT('fevent_custom_value'))
            ->where('field_id = ' . (int) $field_id.' and event_id = '.(int)$event_id)
            ->execute('getRow');
    }

    public function display($aCategories, &$sCustoms, $level)
    {
        $sCustoms .= '<ul '. ($level ? 'style="padding-top:5px;"' : '' ).'>';

        $first = true;

        if($level==0) {
            $style = 'style="padding-top:10px;"';
        } else {
            $style = ' style="margin-bottom:0; padding-left:10px;"';
        }

        foreach($aCategories as $aKey=>$aCategory)
        {
            $class = "";
            if($aKey !== 'PHPFOX_EMPTY_GROUP') {
                $class="group";
            }

            if($first) {
                $class_first = " first";
            } else {
                $class_first = "";
            }
            $first = false;

            $sCustoms .= '<li class="'.$class.$class_first.'"'.$style.'>';

            if($aKey === 'PHPFOX_EMPTY_GROUP') {
                $sCustoms .= _p("custom.general");
            } else {
                $del_l = ""; $del_r = "";
                if(!$aCategory['is_active']) {
                    $del_l = "<del>"; $del_r = "</del>";
                }
                $sCustoms .= '<a style="cursor:default; font-weight:bold;">'.$del_l._p($aCategory['name']).'</a>'.$del_r;
            }

            if(isset($aCategory['child'])) {
                $sCustoms .= '<div class="sortable"><ul class="dont-unbind">';
                foreach($aCategory['child'] as $aField) {
                    $del_l = ""; $del_r = "";
                    if(!$aField['is_active']) {
                        $del_l = "<del>"; $del_r = "</del>";
                    }

                    $cat_name = "";
                    if(!empty($aCategory['category_name'])) {
                        $cat_name = $aCategory['category_name'];
                    }

                    $sCustoms .= '<li class="field" style="margin-bottom:0;">
                            <div style="display:none;"><input type="hidden" name="field['.$aField['field_id'].']" value="'.$aField['ordering'].'" /></div>
                            <a href="#?id='.$aField['field_id'].'&amp;type=field" class="js_drop_down" id="js_field_'.$aField['field_id'].'"><img src="' . Phpfox::getLib('template')->getStyle('image', 'misc/draggable.png') . '" alt="" /> '.$del_l._p($aField['phrase_var_name']).$cat_name.$del_r.'</a>
                        </li>';
                }
                $sCustoms .= '</ul></div>';
            }

            $level++;
            if(!empty($aCategory['subs'])) {
                $this->display($aCategory['subs'], $sCustoms, $level);
            }

            $sCustoms .= '</li>';
        }

        $sCustoms .= '</ul>';
    }
}