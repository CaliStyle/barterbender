<?php

defined('PHPFOX') or exit('NO DICE!');

class Ynsocialstore_Component_Block_Store_Neweststore extends Phpfox_Component
{
	public function process(){

        $hideBlock = $this->getParam('hideBlock', false);
        if($hideBlock) {
            return false;
        }

		$iLimit = Phpfox::getParam('ynsocialstore.max_item_block_new_stores',6);
		$aNewStore = Phpfox::getService('ynsocialstore')->getRecentStore($iLimit);
		if(!count($aNewStore))
			return false;
        foreach ($aNewStore as $key => $itemStore) {
            $aNewStore[$key]['is_favorite'] = Phpfox::getService('ynsocialstore.favourite')->isFavorite(Phpfox::getUserId(),$itemStore['store_id']);
            $aNewStore[$key]['is_following'] = Phpfox::getService('ynsocialstore.following')->isFollowing(Phpfox::getUserId(),$itemStore['store_id']);
        }
		$this->template()->assign(array(
				'sHeader' => _p('new_stores'),
				'aItems' => $aNewStore,
				'sCorePath' => Phpfox::getParam('core.path_actual').'PF.Base/',
				'bIsNoModerate' => true,
		));
		return 'block';
	}
}