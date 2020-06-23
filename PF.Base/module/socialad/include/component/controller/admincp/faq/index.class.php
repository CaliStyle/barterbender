<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 * [PHPFOX_HEADER]
 * @copyright      YouNet Company
 * @author         LyTK
 * @package        Module_Socialad
 * @version        3.01
 */
class Socialad_Component_Controller_Admincp_FAQ_Index extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        if ($aOrder = $this->request()->getArray('order')) {
            if (Phpfox::getService('socialad.faq.process')->updateOrder($aOrder)) {
                $this->url()->send('admincp.socialad.faq', null, _p('faq_order_success_updated'));
            }
        }

        if ($iDelete = $this->request()->getInt('delete')) {
            if (Phpfox::getService('socialad.faq.process')->delete($iDelete)) {
                $this->url()->send('admincp.socialad.faq', null, _p('faq_success_deleted'));
            }
        }

        $this->template()->setTitle(_p('admincp_manage_faqs'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("module_socialad"), $this->url()->makeUrl('admincp.app') . '?id=__module_socialad')
            ->setBreadcrumb(_p('admincp_manage_faqs'), $this->url()->makeUrl('admincp.socialad.faq'))
            ->setHeader(array(
                    'jquery/ui.js' => 'static_script',
                    'faq.js' => 'module_socialad',
                    '<script type="text/javascript">$Behavior.yncFaqIndexInit = function() { $Core.socialad.url(\'' . $this->url()->makeUrl('admincp.socialad.faq') . '\'); } </script>'
                )
            )
            ->assign(array(
                    'sFaqs' => Phpfox::getService('socialad.faq')->display('admincp')->get()
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('socialad.component_controller_admincp_faq_index_clean')) ? eval($sPlugin) : false);
    }

}

?>