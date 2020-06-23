<?php

/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Development
 * @package          Module_Contactimporter
 * @version          2.06
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>

<?php

class contactimporter_Component_Controller_Admincp_statisticsbyprovider extends Phpfox_Component
{
	public function process()
	{
		$providers = Phpfox::getService('contactimporter') -> getProviders();
		$this -> template() -> assign(array('providers' => $providers));
							
		$this -> template() -> setBreadCrumb(_p('statisticsbyprovider'), $this -> url() -> makeUrl('admincp.contactimporter.statisticsbyprovider'));
	}

}
?>