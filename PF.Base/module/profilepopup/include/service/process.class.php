<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright      YouNet Company
 * @author         LyTK
 * @package        Module_ProfilePopup
 * @version        3.01
 */
class ProfilePopup_Service_Process extends Phpfox_Service
{

        /**
         * Class constructor
         */
        public function __construct()
        {
                $this->_sTable = '';
        }

        public function __call($sMethod, $aArguments)
        {
                if ($sPlugin = Phpfox_Plugin::get('profilepopup.service_process__call'))
                {
                        return eval($sPlugin);
                }

                Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
        }

        /**
         * Insert custom field type which getting from system
         * 
         * @param array $aVals array of value of custom field type
         * @return int returned ID of inserted record
         */
        public function addCustomFieldType($aVals, $sItemType = 'user')
        {
                if (!isset($aVals['is_custom_field']))
                {
                        $aVals['is_custom_field'] = 1;
                }
                if (!isset($aVals['is_active']))
                {
                        $aVals['is_active'] = 1;
                }
                if (!isset($aVals['is_display']))
                {
                        $aVals['is_display'] = 0;
                }
                if (isset($sItemType) === false || $sItemType === null)
                {
                        $sItemType = 'user';
                }

                $aInsert = array(
                    'is_custom_field' => $aVals['is_custom_field'],
                    'group_id' => $aVals['group_id'],
                    'field_id' => $aVals['field_id'],
                    'name' => $aVals['name'],
                    'phrase_var_name' => $aVals['phrase_var_name'],
                    'is_active' => $aVals['is_active'],
                    'is_display' => $aVals['is_display'],
                    'ordering' => $aVals['ordering'],
                    'item_type' => $sItemType
                );

                $iId = $this->database()->insert(Phpfox::getT('profilepopup_item'), $aInsert);

                return $iId;
        }

        /**
         * Update active status of profile popup item 
         * 
         * @param int $iItemID profile popup item ID
         * @param int $iIsActive status active
         * @return boolean
         */
        public function updateStatusItem($iItemID, $iIsActive, $sItemType = 'user')
        {
                if (isset($iIsActive) === false || $iIsActive === null)
                {
                        $iIsActive = 0;
                }
                if (isset($sItemType) === false || $sItemType === null)
                {
                        $sItemType = 'user';
                }

                $aUpdate = array(
                    'is_active' => $iIsActive
                );

                $this->database()->update(Phpfox::getT('profilepopup_item'), $aUpdate, 'item_id = ' . (int) $iItemID . ' AND item_type = \'' . $this->database()->escape($sItemType) . '\'');

                return true;
        }

        /**
         * Synchronize custom field from system to profile popup item table
         * 
         * @return boolean
         */
        public function synchronizeAllCustomFieldInSystem()
        {
                $oProfilePopup = Phpfox::getService('profilepopup');
                $this->updateStatusAllCustomField(0);
                $aItemsIsCustomField = $oProfilePopup->getItemsIsCustomField();
                $aAllCustomFieldInSystem = $oProfilePopup->getAllCustomFieldInSystem();
                $iMaxOrdering = $oProfilePopup->getMaxOrdering();
                $iMaxOrdering = ($iMaxOrdering != NULL) ? intval($iMaxOrdering) : 0;

                if (is_array($aAllCustomFieldInSystem) === true && count($aAllCustomFieldInSystem) > 0)
                {
                        foreach ($aAllCustomFieldInSystem as $aNewItem)
                        {
                                $aOldItemUpdate = null;
                                $bIsInsert = true;
                                $bIsUpdate = false;
                                $iIsActive = 0;
                                foreach ($aItemsIsCustomField as $aOldItem)
                                {
                                        if ($aNewItem['group_id'] == $aOldItem['group_id'] && $aNewItem['field_id'] == $aOldItem['field_id'])
                                        {
                                                $aOldItemUpdate = $aOldItem;
                                                $bIsInsert = false;
                                                $bIsUpdate = true;
                                                if (intval($aNewItem['ctg_is_active']) == 1 && intval($aNewItem['ctf_is_active']) == 1)
                                                {
                                                        $iIsActive = 1;
                                                }
                                                break;
                                        }
                                }

                                if ($bIsInsert === true)
                                {
                                        $iMaxOrdering++;
                                        $this->addCustomFieldType(array('group_id' => $aNewItem['group_id'], 'field_id' => $aNewItem['field_id'], 'name' => $aNewItem['field_name'], 'phrase_var_name' => $aNewItem['phrase_var_name'], 'ordering' => $iMaxOrdering));
                                }
                                if ($bIsUpdate === true)
                                {
                                        $this->updateStatusItem($aOldItemUpdate['item_id'], $iIsActive);
                                }
                        }
                }

                //      delete inactive custom field
                $this->deleteInactiveCustomField();

                return true;
        }

        /**
         * Update display status of profile popup item
         * 
         * @param type $iItemID profile popup item ID
         * @param int $iIsDisplay status display
         * @return boolean
         */
        public function updateDisplayItem($iItemID, $iIsDisplay, $sItemType = 'user')
        {
                if (isset($iIsDisplay) === false || $iIsDisplay === null)
                {
                        $iIsDisplay = 0;
                }
                if (isset($sItemType) === false || $sItemType === null)
                {
                        $sItemType = 'user';
                }

                $aUpdate = array(
                    'is_display' => $iIsDisplay
                );

                $this->database()->update(Phpfox::getT('profilepopup_item'), $aUpdate, 'item_id = ' . (int) $iItemID . ' AND item_type = \'' . $this->database()->escape($sItemType) . '\'');

                return true;
        }

        /**
         * Update display status for ALL profile popup item
         * 
         * @param int $iIsDisplay status display
         * @return boolean
         */
        public function setDisplayStatusForAllItem($iIsDisplay, $sItemType = 'user')
        {
                if (isset($iIsDisplay) === false || $iIsDisplay === null)
                {
                        $iIsDisplay = 0;
                }
                if (isset($sItemType) === false || $sItemType === null)
                {
                        $sItemType = 'user';
                }
                $sWhere = '';
                if (isset($sItemType) === true || $sItemType !== null)
                {
                        $sWhere .= ' AND item_type = \'' . $this->database()->escape($sItemType) . '\'';
                }

                $aUpdate = array(
                    'is_display' => $iIsDisplay
                );

                $this->database()->update(Phpfox::getT('profilepopup_item'), $aUpdate, '1=1' . $sWhere);

                return true;
        }

        /**
         * Update order of profile popup item
         * 
         * @param type $iItemID profile popup item ID
         * @param int $iOrdering order
         * @return boolean
         */
        public function updateOrderingItem($iItemID, $iOrdering, $sItemType = 'user')
        {
                if (isset($iOrdering) === false || $iOrdering === null)
                {
                        $iOrdering = 0;
                }
                if (isset($sItemType) === false || $sItemType === null)
                {
                        $sItemType = 'user';
                }

                $aUpdate = array(
                    'ordering' => $iOrdering
                );

                $this->database()->update(Phpfox::getT('profilepopup_item'), $aUpdate, 'item_id = ' . (int) $iItemID . ' AND item_type = \'' . $this->database()->escape($sItemType) . '\'');

                return true;
        }

        /**
         * Update active status for ALL profile popup item
         * 
         * @param int $iIsActive status active
         * @return boolean
         */
        public function updateStatusAllCustomField($iIsActive = null, $sItemType = 'user')
        {
                if (isset($iIsActive) === false || $iIsActive === null)
                {
                        $iIsActive = 0;
                }

                $aUpdate = array(
                    'is_active' => $iIsActive
                );
                if (isset($sItemType) === false || $sItemType === null)
                {
                        $sItemType = 'user';
                }

                $this->database()->update(Phpfox::getT('profilepopup_item'), $aUpdate, 'is_custom_field = 1 ' . ' AND item_type = \'' . $this->database()->escape($sItemType) . '\'');

                return true;
        }

        /**
         * Delete inactive profile popup items
         * 
         * @return boolean
         */
        public function deleteInactiveCustomField($sItemType = 'user')
        {
                if (isset($sItemType) === false || $sItemType === null)
                {
                        $sItemType = 'user';
                }
                $this->database()->delete(Phpfox::getT('profilepopup_item'), 'is_custom_field = 1 AND is_active = 0 ' . ' AND item_type = \'' . $this->database()->escape($sItemType) . '\'');
                return true;
        }

        public function updateOrderingItemByModule($iItemID, $iOrdering, $sItemType = 'user', $sModule = null)
        {
        	if(null == $sModule){
        		return false;	
        	}
			
                if (isset($iOrdering) === false || $iOrdering === null)
                {
                        $iOrdering = 0;
                }
                if (isset($sItemType) === false || $sItemType === null)
                {
                        $sItemType = 'user';
                }

                $aUpdate = array(
                    'ordering' => $iOrdering
                );

                $this->database()->update(Phpfox::getT('profilepopup_module_item'), $aUpdate, ' module_id = \'' . $this->database()->escape($sModule) . '\'' . ' AND item_id = ' . (int) $iItemID . ' AND item_type = \'' . $this->database()->escape($sItemType) . '\'');

                return true;
        }

        public function setDisplayStatusForAllItemByModule($iIsDisplay, $sItemType = 'user', $sModule = null)
        {
        	if(null == $sModule){
        		return false;	
        	}
			
                if (isset($iIsDisplay) === false || $iIsDisplay === null)
                {
                        $iIsDisplay = 0;
                }
                if (isset($sItemType) === false || $sItemType === null)
                {
                        $sItemType = 'user';
                }
                $sWhere = '';
                if (isset($sItemType) === true || $sItemType !== null)
                {
                        $sWhere .= ' AND item_type = \'' . $this->database()->escape($sItemType) . '\'';
                }

                $aUpdate = array(
                    'is_display' => $iIsDisplay
                );

                $this->database()->update(Phpfox::getT('profilepopup_module_item'), $aUpdate, '1=1' . ' AND module_id = \'' . $this->database()->escape($sModule) . '\'' . $sWhere);

                return true;
        }

        public function updateDisplayItemByModule($iItemID, $iIsDisplay, $sItemType = 'user', $sModule = null)
        {
        	if(null == $sModule){
        		return false;	
        	}
			
                if (isset($iIsDisplay) === false || $iIsDisplay === null)
                {
                        $iIsDisplay = 0;
                }
                if (isset($sItemType) === false || $sItemType === null)
                {
                        $sItemType = 'user';
                }

                $aUpdate = array(
                    'is_display' => $iIsDisplay
                );

                $this->database()->update(Phpfox::getT('profilepopup_module_item'), $aUpdate, ' module_id = \'' . $this->database()->escape($sModule) . '\'' .  ' AND item_id = ' . (int) $iItemID . ' AND item_type = \'' . $this->database()->escape($sItemType) . '\'');

                return true;
        }

}

