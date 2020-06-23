<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Service_Category_Process extends Core_Service_Systems_Category_Process
{
    private $_iStringLengthCategoryName;

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->_sTable = Phpfox::getT('directory_category');
        $this->_iStringLengthCategoryName = 40;
        $this->_sModule = 'directory';
    }

    public function updateOrder($aVals)
    {
        foreach ($aVals as $iId => $iOrder) {
            $this->database()->update($this->_sTable, array('ordering' => $iOrder), 'category_id = ' . (int)$iId);
        }

        $this->cache()->removeGroup('directory_category');

        return true;
    }

    public function delete($iId)
    {
        //Delete phrase of category
        $aCategory = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('category_id=' . (int) $iId)
            ->execute('getSlaveRow');

        if (isset($aCategory['title']) && Core\Lib::phrase()->isPhrase($aCategory['title'])){
            Phpfox::getService('language.phrase.process')->delete($aCategory['title'], true);
        }

        $this->database()->delete($this->_sTable, 'category_id = ' . (int)$iId);
        $this->database()->delete(Phpfox::getT('directory_category_data'), 'category_id = ' . (int)$iId);
        $this->cache()->removeGroup('directory_category');
        return true;
    }

    /**
     * Add a new category for module
     *
     * @param array  $aVals
     * @param string $sName
     *
     * @return int
     */
    public function add($aVals, $sName = 'name') {
        $finalPhrase = $this->addPhrase($aVals, $sName);
        $iCategoryId = $this->database()->insert($this->_sTable, [
            'parent_id' => (!empty($aVals['parent_id']) ? (int) $aVals['parent_id'] : 0),
            'is_active' => 1,
            'title' => $finalPhrase,
            'time_stamp' => PHPFOX_TIME
        ]);

        $this->cache()->removeGroup($this->_sModule . '_category');
        return $iCategoryId;
    }

    /**
     * @param array $aVals
     * @param string $sName
     *
     * @return bool
     */
    public function update($aVals, $sName = 'name')
    {
        //Verify data
        if (!isset($aVals['edit_id'])) {
            return false;
        }

        // update parent category
        if (!empty($this->_sItemId) && !empty($this->_sTableData)) {
            $iOldParentId = db()->select('parent_id')->from($this->_sTable)->where(['category_id' => $aVals['edit_id']])->executeField();
            $aItemsInSameCategory = db()->select($this->_sItemId)->from($this->_sTableData)->where(['category_id' => $aVals['edit_id']])->executeRows();
            foreach (array_column($aItemsInSameCategory, $this->_sItemId) as $iItemId) {
                if (empty($iOldParentId) && !empty($aVals['parent_id'])) {
                    db()->insert($this->_sTableData, [
                        $this->_sItemId => $iItemId,
                        'category_id' => $aVals['parent_id']
                    ]);
                } elseif (!empty($iOldParentId) && empty($aVals['parent_id'])) {
                    db()->delete($this->_sTableData, [
                        $this->_sItemId => $iItemId,
                        'category_id' => $iOldParentId
                    ]);
                } elseif (!empty($iOldParentId) && !empty($aVals['parent_id'])) {
                    db()->update($this->_sTableData, [
                        $this->_sItemId => $iItemId,
                        'category_id' => $aVals['parent_id']
                    ], [
                        $this->_sItemId => $iItemId,
                        'category_id' => $iOldParentId
                    ]);
                }
            }
        }

        if (isset($aVals[$sName]) && Core\Lib::phrase()->isPhrase($aVals[$sName])) {
            $finalPhrase = $aVals[$sName];
            //Update phrase
            $this->updatePhrase($aVals);
        } else {
            $finalPhrase = $this->addPhrase($aVals, $sName);
        }
        $this->database()->update($this->_sTable, [
            'parent_id' => (int)$aVals['parent_id'],
            'title' => $finalPhrase,
            'time_stamp' => PHPFOX_TIME
        ], 'category_id = ' . $aVals['edit_id']);

        // Remove from cache
        $this->cache()->removeGroup($this->_sModule . '_category');

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
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

    public function updateActivity($iId, $iType)
    {
        Phpfox::isAdmin(true);
        $this->database()->update(($this->_sTable), array('is_active' => (int)($iType == '1' ? 1 : 0)), 'category_id' . ' = ' . (int)$iId);

        $this->cache()->removeGroup($this->_sModule . '_category');

    }

}

?>