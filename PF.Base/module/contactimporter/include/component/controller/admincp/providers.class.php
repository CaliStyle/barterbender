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

class contactimporter_Component_Controller_Admincp_providers extends Phpfox_Component
{

    public function process()
    {
        $providers = Phpfox::getService('contactimporter')->getProviders();
        $this->template()
                ->setHeader('cache', array(
                    'quick_edit.js' => 'static_script',
                    'jquery.tablednd_0_5.js' => 'module_contactimporter',
                    'orderprovider.js' => 'module_contactimporter',
                    'contactimporter.js' => 'module_contactimporter',
                        )
        );
        $core_url = Phpfox::getParam('core.path');
        $this->template()->assign(array('core_url' => $core_url));
        $this->template()->assign(array('providers' => $providers));
        $this->template()
            ->setBreadCrumb(_p('Apps'),$this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_contactimporter'),$this->url()->makeUrl('admincp.app',['id' => '__module_contactimporter']))
            ->setBreadCrumb(_p('providers'), $this->url()->makeUrl('admincp.contactimporter.providers'));
    }

}
?>