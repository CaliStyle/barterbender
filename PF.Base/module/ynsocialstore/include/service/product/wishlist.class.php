<?php

/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/13/16
 * Time: 21:42
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Service_Product_Wishlist extends Phpfox_Service
{
    /**
     * Favourite constructor.
     */
    public function __construct()
    {
        $this->_sTable =  Phpfox::getT('ecommerce_product_ynstore_wishlist');
    }

    /**
     * @param $iUserId
     * @param $iProductId
     *
     * @return int | null
     */
    public function findId($iUserId, $iProductId)
    {
        return (int) $this->database()->select('wishlist_id')
            ->from($this->_sTable)
            ->where(strtr('user_id=:user and product_id=:product',[
                ':user'=> intval($iUserId),
                ':product'=> intval($iProductId),
            ]))
            ->execute('getSlaveField');
    }

    /**
     * @param $iUserId
     * @param $iProductId
     * @return bool
     */
    public function add($iUserId, $iProductId)
    {
        $id = $this->findId($iUserId, $iProductId);

        if(!$id){
            $this->database()->insert($this->_sTable, [
                'user_id'=>intval($iUserId),
                'product_id'=> intval($iProductId),
                'time_stamp'=> time(),
            ]);
            $aProduct = Phpfox::getService('ynsocialstore.product')->getProductById($iProductId);
            Phpfox::getService("notification.process")->add("ynsocialstore_favoriteproduct",$iProductId, $aProduct['user_id'] , Phpfox::getUserId());
        }

        return true;
    }

    /**
     * @param int $iUserId
     * @param int $iProductId
     * @return bool
     */
    public function delete($iUserId, $iProductId)
    {
        $id = $this->findId($iUserId, $iProductId);

        if($id){
            $this->database()->delete($this->_sTable,'wishlist_id='. intval($id));
        }

        return true;
    }


    /**
     * @param int $iUserId
     * @param int $iProductId
     * @return bool
     */
    public function isWishlist($iUserId, $iProductId)
    {
        return $this->findId($iUserId, $iProductId) !=0;
    }

    public function deleteAllWishlist()
    {
        $this->database()->delete($this->_sTable, 'user_id = ' . (int)Phpfox::getUserId());
        return true;
    }

    public function getAllWishlistByProductId($iProductId)
    {
        $aRows = $this->database()->select('sf.wishlist_id,u.email,u.full_name,u.user_id')
                    ->from($this->_sTable,'sf')
                    ->join(Phpfox::getT('user'),'u','u.user_id = sf.user_id')
                    ->where('sf.product_id ='.$iProductId)
                    ->execute('getRows');
        if(!$aRows)
            return false;
        return $aRows;
    }
}