<?php

/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/13/16
 * Time: 21:51
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Service_Following extends Phpfox_Service
{
    public function __construct()
    {
        $this->_sTable =  Phpfox::getT('ynstore_store_following');
    }

    /**
     * @param $iUserId
     * @param $iStoreId
     *
     * @return int | null
     */
    public function findId($iUserId, $iStoreId)
    {
        return (int) $this->database()->select('follow_id')
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
            Phpfox::getService("notification.process")->add("ynsocialstore_followstore",$iStoreId, $aItem['user_id'] , Phpfox::getUserId());
            $this->database()->updateCounter('ynstore_store', 'total_follow', 'store_id', $iStoreId);
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
            $this->database()->delete($this->_sTable,'follow_id='. intval($id));

            $this->database()->updateCounter('ynstore_store', 'total_follow', 'store_id', $iStoreId, true);
        }

        return true;
    }


    /**
     * @param int $iUserId
     * @param int $iStoreId
     * @return bool
     */
    public function isFollowing($iUserId, $iStoreId)
    {
        return $this->findId($iUserId, $iStoreId) !=0;
    }

    public function deleteAllFollow()
    {
        $this->database()->delete($this->_sTable, 'user_id = ' . (int)Phpfox::getUserId());
        return true;
    }

    public function getAllFollowingByStoreId($iStoreId)
    {
        $aRows = $this->database()->select('sf.follow_id,u.email, u.full_name,u.user_id')
            ->from($this->_sTable,'sf')
            ->join(Phpfox::getT('user'),'u','u.user_id = sf.user_id')
            ->where('sf.store_id ='.$iStoreId)
            ->execute('getRows');
        if(!$aRows)
            return false;
        return $aRows;
    }
}