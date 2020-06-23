<?php

defined('PHPFOX') or exit('NO DICE!');


class FoxFavorite_Component_Controller_Admincp_Page extends Phpfox_Component 
{
	public function process()
	{
		$sRequest = $this->request()->get('isclone');
		if($sRequest == 1)
		{
			phpfox::getService('foxfavorite.process')->migrateFavoriteData();
		}
	
		$sUrl = $this->url()->makeUrl('admincp.foxfavorite.page.isclone_1');
		$this->template()->assign(array(
			'sUrl'=>$sUrl
		));
					
		$this->template()
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_foxfavorite'), $this->url()->makeUrl('admincp.app').'?id=__module_foxfavorite')
            ->setBreadcrumb(_p('foxfavorite.page_migration'), $this->url()->makeUrl('admincp.foxfavorite.page'));
	}
}
?>