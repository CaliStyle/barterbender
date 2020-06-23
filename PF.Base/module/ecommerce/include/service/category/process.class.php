<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Service_Category_Process extends Core_Service_Systems_Category_Process {

    private $_iSize;
    private $_iStringLengthCategoryName;
    public function __construct()
    {
        parent::__construct();
        $this->_sTable = Phpfox::getT('ecommerce_category');
        $this->_iSize = 16;
        $this->_sModule = 'ecommerce';
        $this->_iStringLengthCategoryName = 40;
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
        $this->_processImage($iCategoryId);

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

        if ($aVals['edit_id'] == $aVals['parent_id']) {
            return Phpfox_Error::set(_p('parent_category_and_child_is_the_same'));
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

        $this->_processImage($aVals['edit_id'], true);

        // Remove from cache
        $this->cache()->removeGroup($this->_sModule . '_category');

        return true;
    }

    public function updateOrder($aVals)
    {
        foreach ($aVals as $iId => $iOrder)
        {
            $this->database()->update($this->_sTable, array('ordering' => $iOrder), 'category_id = ' . (int) $iId);
        }

        $this->cache()->remove('ecommerce_category', 'substr');

        return true;
    }

	public function deleteCategoriesData($iProductId)
	{
		$this->database()->delete(Phpfox::getT('ecommerce_category_data'), 'product_id = ' . (int) $iProductId);
	}
	
    public function delete($iId)
    {
        $aCategory = Phpfox::getService('ecommerce.category')->getForEdit($iId);

        //delete business related category, need show confirm do you want to delete category
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

        if ($aCategory && !empty($aCategory['image_path']) && file_exists(Phpfox::getParam('core.dir_pic') . sprintf($aCategory['image_path'], '_' . $this->_iSize)))
        {
            Phpfox::getLib('file')->unlink(Phpfox::getParam('core.dir_pic') . sprintf($aCategory['image_path'], '_' . $this->_iSize));
        }

        //Delete phrase of category
        if (isset($aCategory['title']) && Phpfox::isPhrase($aCategory['title'])){
            Phpfox::getService('language.phrase.process')->delete($aCategory['title'], true);
        }

        $this->database()->delete($this->_sTable, 'category_id = ' . (int) $iId);
        $this->database()->delete(Phpfox::getT('ecommerce_category_data'), 'category_id = ' . (int) $iId);
        $this->cache()->remove('ecommerce_category', 'substr');
        return true;
    }

    public function updateCountAuctionsForCategory($sCategoryText)
    {
        $aCategoryInfo = array();
        
        if ($sCategoryText != '')
        {
            $aCategoryInfo = $this->database()
                    ->select('ec.category_id, COUNT(ecd.product_id) as total_auction')
                    ->from(Phpfox::getT('ecommerce_category'), 'ec')
                    ->leftjoin(Phpfox::getT('ecommerce_category_data'), 'ecd', 'ec.category_id = ecd.category_id')
                    ->leftjoin(Phpfox::getT('ecommerce_product'), 'ep', 'ep.product_id = ecd.product_id')
                    ->where('ec.category_id IN (' . $sCategoryText . ') AND ep.product_status IN ("running", "approved")')
                    ->group('ec.category_id')
                    ->execute('getSlaveRows');

            $this->database()->update(Phpfox::getT('ecommerce_category'), array('used' => 0), 'category_id IN (' . $sCategoryText . ')');
        }

        if (count($aCategoryInfo))
        {
            foreach ($aCategoryInfo as $key => $category)
            {
                $this->database()->update(Phpfox::getT('ecommerce_category'), array('used' => (int) $category['total_auction']), ' category_id = ' . (int) $category['category_id']);
            }
        }

        $this->cache()->remove('auction_category', 'substr');

        return $aCategoryInfo;
    }

    public function updateActivity($iId, $iType, $iSub)
    {
        Phpfox::isAdmin(true);
        $this->database()->update(($this->_sTable), array('is_active' => (int) ($iType == '1' ? 1 : 0)), 'category_id' . ' = ' . (int) $iId);

        $this->cache()->remove('ecommerce_category', 'substr');
    }

    private function _processImage($iId, $bIsUpdate = false)
    {
        if (isset($_FILES['image']['name']) && ($_FILES['image']['name'] != '')) {
            $aImage = Phpfox::getLib('file')->load('image', array(
                'jpg',
                'gif',
                'png'
            ),
                (Phpfox::getUserParam('ecommerce.max_size_for_icons') === 0 ? null : (Phpfox::getUserParam('ecommerce.max_size_for_icons') / 1024)));

            if ($aImage !== false) {
                if ($bIsUpdate) {
                    $aCategory = Phpfox::getService('ecommerce.category')->getForEdit($iId);

                    if ($aCategory && !empty($aCategory['image_path']) && file_exists(Phpfox::getParam('core.dir_pic') . sprintf($aCategory['image_path'],
                                '_' . $this->_iSize))) {
                        Phpfox::getLib('file')->unlink(Phpfox::getParam('core.dir_pic') . sprintf($aCategory['image_path'],
                                '_' . $this->_iSize));
                    }
                }

                $oImage = Phpfox::getLib('image');

                $sFileName = Phpfox::getLib('file')->upload('image',
                    Phpfox::getParam('core.dir_pic') . 'ynecommerce' . DIRECTORY_SEPARATOR, $iId);

                $aSql = array();
                $aSql['image_path'] = 'ynecommerce/' . $sFileName;
                $aSql['server_id'] = Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID');


                $oImage->createThumbnail(Phpfox::getParam('core.dir_pic') . 'ynecommerce' . DIRECTORY_SEPARATOR . sprintf($sFileName,
                        ''),
                    Phpfox::getParam('core.dir_pic') . 'ynecommerce' . DIRECTORY_SEPARATOR . sprintf($sFileName,
                        '_' . $this->_iSize), $this->_iSize, $this->_iSize);
                Phpfox::getLib('file')->unlink(Phpfox::getParam('core.dir_pic') . 'ynecommerce' . DIRECTORY_SEPARATOR . sprintf($sFileName,
                        ''));

                $this->database()->update($this->_sTable, $aSql, 'category_id = ' . $iId);
            }
        }
    }
}
