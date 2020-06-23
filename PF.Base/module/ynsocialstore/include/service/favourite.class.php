<?php

/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/13/16
 * Time: 21:42
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Service_Favourite extends Phpfox_Service
{
    /**
     * Favourite constructor.
     */
    public function __construct()
    {
        $this->_sTable =  Phpfox::getT('ynstore_store_favorite');
    }

    /**
     * @param $iUserId
     * @param $iStoreId
     *
     * @return int | null
     */
    public function findId($iUserId, $iStoreId)
    {
        return (int) $this->database()->select('favorite_id')
            ->from($this->_sTable)
            ->where(strtr('user_id=:user and store_id=:store',[
                ':user'=> intval($iUserId),
                ':store'=> intval($iStoreId),
            ]))
            ->execute('getSlaveField');
    }

    /**
     * @param $iUserId
     * @param $iStoreId
     * @return bool
     */
    public function add($iUserId, $iStoreId)
    {
        $id = $this->findId($iUserId, $iStoreId);

        if(!$id){
            $this->database()->insert($this->_sTable, [
                'user_id'=>intval($iUserId),
                'store_id'=> intval($iStoreId),
                'time_stamp'=> time(),
            ]);
            $aItem = Phpfox::getService('ynsocialstore')->getStoreById($iStoreId, true);
            Phpfox::getService("notification.process")->add("ynsocialstore_favoritestore",$iStoreId, $aItem['user_id'] , Phpfox::getUserId());
            $this->database()->updateCounter('ynstore_store', 'total_favorite', 'store_id', $iStoreId);
        }

        return true;
    }

    /**
     * @param int $iUserId
     * @param int $iStoreId
     * @return bool
     */
    public function delete($iUserId, $iStoreId)
    {
        $id = $this->findId($iUserId, $iStoreId);

        if($id){
            $this->database()->delete($this->_sTable,'favorite_id='. intval($id));

            $this->database()->updateCounter('ynstore_store', 'total_favorite', 'store_id', $iStoreId, true);
        }

        return true;
    }


    /**
     * @param int $iUserId
     * @param int $iStoreId
     * @return bool
     */
    public function isFavorite($iUserId, $iStoreId)
    {
        return $this->findId($iUserId, $iStoreId) !=0;
    }

    public function deleteAllFavorite()
    {
        $this->database()->delete($this->_sTable, 'user_id = ' . (int)Phpfox::getUserId());
        return true;
    }

    public function getAllFavoriteByStoreId($iStoreId)
    {
        $aRows = $this->database()->select('sf.favorite_id,u.email,u.full_name,u.user_id')
                    ->from($this->_sTable,'sf')
                    ->join(Phpfox::getT('user'),'u','u.user_id = sf.user_id')
                    ->where('sf.store_id ='.$iStoreId)
                    ->execute('getRows');
        if(!$aRows)
            return false;
        return $aRows;
    }
}