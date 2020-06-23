<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Service_Uom_Process extends Core_Service_Systems_Category_Process {

    public function __construct()
    {
        parent::__construct();
        $this->_sTable = Phpfox::getT('ecommerce_uom');
        $this->_sModule = 'ecommerce';
        $this->_sCategoryName = 'uom';
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

        if (isset($aVals[$sName]) && Core\Lib::phrase()->isPhrase($aVals[$sName])) {
            $finalPhrase = $aVals[$sName];
            //Update phrase
            $this->updatePhrase($aVals);
        } else {
            $finalPhrase = $this->addPhrase($aVals, $sName);
        }
        $this->database()->update($this->_sTable, [
            'title' => $finalPhrase,
            'is_active' => 1,
        ], 'uom_id = ' . $aVals['edit_id']);


        // Remove from cache
        $this->cache()->removeGroup($this->_sModule . '_uom');

        return true;
    }

    /**
     * Add a new uom for module
     *
     * @param array  $aVals
     * @param string $sName
     *
     * @return int
     */
    public function add($aVals, $sName = 'name') {
        $finalPhrase = $this->addPhrase($aVals, $sName);
        $iUOMId = $this->database()->insert($this->_sTable, [
            'title' => $finalPhrase,
            'time_stamp' => PHPFOX_TIME,
            'ordering' => 0,
            'is_active' => 1,
        ]);

        $this->cache()->removeGroup($this->_sModule . '_uom');
        return $iUOMId;
    }

    public function delete($iId)
    {
        $aUom = Phpfox::getService('ecommerce.uom')->getForEdit($iId);

        //delete products related UOM, need show confirm do you want to delete UOM
        $aProducts = $this->database()->select('product.product_id')->from(Phpfox::getT('ecommerce_product'),
                'product')->where('product.product_id = ' . (int)$iId)->execute('getRows');

        if (count($aProducts)) {
            foreach ($aProducts as $aProduct) {
                Phpfox::getService('ecommerce.process')->delete($aProduct['product_id']);
            }
        }

        //Delete phrase of uom
        if (isset($aUom['title']) && Core\Lib::phrase()->isPhrase($aUom['title'])){
            Phpfox::getService('language.phrase.process')->delete($aUom['title'], true);
        }

        $this->database()->delete($this->_sTable, 'uom_id = ' . (int)$iId);
        $this->database()->delete(Phpfox::getT('ecommerce_uom'), 'uom_id = ' . (int)$iId);

        $this->cache()->removeGroup($this->_sModule . '_uom');
        return true;
    }

    public function updateUomActivity($iId, $iType)
    {
        Phpfox::isAdmin(true);
        $this->database()->update(($this->_sTable), array('is_active' => (int)($iType == '1' ? 1 : 0)),
            'uom_id' . ' = ' . (int)$iId);

        $this->cache()->removeGroup($this->_sModule . '_uom');
    }
}
