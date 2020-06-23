<?php

/**

 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Store_Menuedit extends Phpfox_Component
{
	public function process()
	{
        $sTabView = $this->request()->get('req3');
        $id = $this->request()->get('id');
        if(!$id)
        {
            return false;
        }
        $aStore = Phpfox::getService('ynsocialstore')->getQuickStoreById($id);
        if(!$aStore)
        {
            return false;
        }
        if(!Phpfox::getService('ynsocialstore.permission')->canEditStore(false,$aStore['user_id']))
        {
            return false;
        }
        $this->template()->assign(array(
                'sTabView'  => $sTabView,
                'iStoreId'  => $id,
            )
        );

        return 'block';	
	}
}