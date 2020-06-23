<?php
namespace Apps\P_AdvEvent\Service\Category;

use Phpfox;
use Phpfox_Service;
use Phpfox_Error;
use Phpfox_Plugin;

class Process extends \Core_Service_Systems_Category_Process
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('fevent_category');
        $this->_sTableData = Phpfox::getT('fevent_category_data');
        parent::__construct();
    }

    public function update($aVals, $sName = 'name')
    {
        $aCategory = db()->select('*')
            ->from($this->_sTable)
            ->where('category_id =' . intval($aVals['edit_id']))
            ->execute('getRow');
        //Verify data
        if (!isset($aVals['parent_id'])) {
            $aVals['parent_id'] = 0;
        }
        if (!isset($aVals['edit_id'])) {
            return false;
        }
        if ($aVals['edit_id'] == $aVals['parent_id']) {
            return Phpfox_Error::set(_p('fevent.parent_category_and_child_is_the_same'));
        }

        if (isset($aVals[$sName]) && \Core\Lib::phrase()->isPhrase($aVals[$sName])) {
            $finalPhrase = $aVals[$sName];
            //Update phrase
            $this->updatePhrase($aVals);
        } else {
            $finalPhrase = $this->addPhrase($aVals, $sName);
        }
        $this->database()->update($this->_sTable, [
            'parent_id' => (int)$aVals['parent_id'],
            'name' => $finalPhrase,
            'time_stamp' => PHPFOX_TIME
        ], 'category_id = ' . $aVals['edit_id']
        );
        //update category data when change parent category
        if (($aVals['parent_id'] != $aCategory['parent_id'])) {
            $aEvents = db()->select('d.event_id')
                ->from($this->_sTableData, 'd')
                ->where("d.category_id = " . $aVals['edit_id'])
                ->execute('getSlaveRows');
            if ($aCategory['parent_id'] > 0) {
                if ($aVals['parent_id'] == 0) {
                    foreach ($aEvents as $aEvent) {
                        db()->delete($this->_sTableData,
                            ['event_id' => $aEvent['event_id'], 'category_id' => $aCategory['parent_id']]);
                    }
                } else {
                    foreach ($aEvents as $aEvent) {
                        db()->update($this->_sTableData, ['category_id' => $aVals['parent_id']],
                            ['event_id' => $aEvent['event_id'], 'category_id' => $aCategory['parent_id']]);
                    }
                }
            } else {
                foreach ($aEvents as $aEvent) {
                    db()->insert($this->_sTableData,
                        ['event_id' => $aEvent['event_id'], 'category_id' => $aVals['parent_id']]);
                }
            }
        }
        // Remove from cache
        $this->cache()->remove();
        return true;
    }

    public function getParentIds($iId)
    {
        $aIds = array();
        $aCategories = $this->database()->select('*')->from($this->_sTable)->execute('getSlaveRows');
        $aCurr = $this->database()->select('*')->from($this->_sTable)->where('category_id = '.(int)$iId)->execute('getSlaveRow');

        if(count($aCategories) && is_array($aCurr))
        {
            while($aCurr['parent_id'] != 0)
            {
                foreach($aCategories as $aCategory)
                {
                    if($aCategory['category_id'] == $aCurr['parent_id'])
                    {
                        $aIds[] = $aCategory['category_id'];
                        $aCurr = $aCategory;
                        continue;
                    }
                }
            }
        }

        return $aIds;
    }

    public function getDirectCategoryIdOfEvent($iEventId)
    {
        $aDatas = $this->database()->select('category_id')->from(Phpfox::getT('fevent_category_data'))->where('event_id = '.(int)$iEventId)->execute('getRows');

        if($cnt = count($aDatas))
        {
            foreach($aDatas as $aData)
            {
                if(!$this->_haveChild($aData, $aDatas))
                {
                    return $aData['category_id'];
                }
            }
        }

        return 0;
    }

    protected function _haveChild($aCheck, $aDatas)
    {
        foreach($aDatas as $aData)
        {
            if($this->database()->select('*')->from($this->_sTable)->where('category_id='.(int)$aData['category_id'].' AND parent_id='.(int)$aCheck['category_id'])->execute('getRow'))
            {
                return true;
            }
        }
        return false;
    }

    public function delete($iId)
    {
        $aCategory = Phpfox::getService('fevent.category')->getForEdit($iId);

        $aSubCategories = $this->database()->select('category_id')
            ->from($this->_sTable)
            ->where('parent_id = ' . (int) $iId)
            ->execute('getRows');

        if (!empty($aSubCategories))
        {
            $aSubCategoryIds = array();

            foreach ($aSubCategories as $aItem)
            {
                $aSubCategoryIds[] = array_shift($aItem);
            }

            $sSubCategories = implode(',', $aSubCategoryIds);

            $this->database()->update($this->_sTable, array('parent_id' => $aCategory['parent_id']), 'category_id IN ('.$sSubCategories.')');
        }
        if (isset($aCategory['name']) && Phpfox::isPhrase($aCategory['name'])){
            Phpfox::getService('language.phrase.process')->delete($aCategory['name'], true);
        }
        $this->database()->delete($this->_sTable, 'category_id = ' . (int) $iId);
        $this->database()->delete(Phpfox::getT('fevent_category_data'), 'category_id = ' . (int) $iId);
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

    public function updateActivity($iId, $iType, $iSub)
    {
        Phpfox::isAdmin(true);
        $this->database()->update(($this->_sTable), array('is_active' => (int) ($iType == '1' ? 1 : 0)), 'category_id' . ' = ' . (int) $iId);

        $this->cache()->remove();
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
        if ($sPlugin = Phpfox_Plugin::get('fevent.service_category_process__call'))
        {
            return eval($sPlugin);
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}