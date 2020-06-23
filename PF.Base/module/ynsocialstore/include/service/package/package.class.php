<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/4/16
 * Time: 10:07 AM
 */
class Ynsocialstore_Service_Package_Package extends Phpfox_Service
{
    public function getById($package_id)
    {
        $aRow = $this->database()->select('pkg.*')
            ->from(Phpfox::getT("ynstore_store_package"), 'pkg')
            ->where('pkg.package_id = '.(int)$package_id)
            ->execute('getSlaveRow');

        return $aRow;
    }

    public function getPackageByStoreId($iStoreId)
    {
        $aRow = $this->database()->select('pkg.*')
            ->from(Phpfox::getT("ynstore_store_package"), 'pkg')
            ->join(Phpfox::getT('ynstore_store'), 'st', 'st.package_id = pkg.package_id')
            ->where('st.store_id = '.(int)$iStoreId)
            ->execute('getSlaveRow');

        return $aRow;
    }

    public function getItemCount()
    {
        $oQuery = $this -> database()
            -> select('count(*)')
            -> from(Phpfox::getT("ynstore_store_package"),'pk');

        return $oQuery->execute('getSlaveField');
    }

    public function getPackages($iPage = 0, $iLimit = 0, $iCount = 0)
    {
        $oSelect = $this -> database()
            -> select('*')
            -> from(Phpfox::getT("ynstore_store_package"), 'pk');

        $oSelect->limit($iPage, $iLimit, $iCount);

        $aPackages = $oSelect->execute('getSlaveRows');

        return $aPackages;
    }
}