<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright        [YouNet_COPYRIGHT]
 * @author        YouNet Company
 * @package        Module_GettingStarted
 * @version        3.02p5
 */

defined('PHPFOX') or exit('NO DICE!');

class Gettingstarted_Component_Controller_Admincp_Managearticlecategory extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $aLanguages = PhpFox::getService('language')->getAll();

        if ($this->request()->get('lang')) {
            $lang_id = $this->request()->get('lang');

        } else {
            $lang_id = $aLanguages[0]['language_id'];
        }

        $sCategories = Phpfox::getService('gettingstarted.articlecategory')->getCatForManage($lang_id);

        if ($aOrder = $this->request()->getArray('order')) {
            if (Phpfox::getService('gettingstarted.articlecategory')->updateOrder($aOrder)) {
                $this->url()->send('current', null, _p('gettingstarted.category_order_successfully_updated'));
            }
        }

        if ($iDelete = $this->request()->getInt('delete')) {
            if (Phpfox::getService('gettingstarted.articlecategory')->delete($iDelete)) {
                $this->url()->send('current', null, _p('gettingstarted.category_successfully_deleted'));
            }
        }

        $this->template()->setTitle(_p('gettingstarted.manage_categories'))
            ->setBreadcrumb(_p('gettingstarted.manage_categories'), $this->url()->makeUrl('admincp.gettingstarted.addarticlecategory'))
            ->setPhrase(array(
                    'gettingstarted.are_you_sure_this_will_delete_all_categories_that_belong_to_this_category_and_cannot_be_undone'
                )
            )
            ->setHeader(array(
                    'jquery/ui.js' => 'static_script',
                    'admin.js' => 'module_gettingstarted',
                    '<script type="text/javascript">$Behavior.initParam = function() { $Core.gettingstarted.param(\'' . $this->url()->makeUrl('admincp.gettingstarted') . '\', \'' . $lang_id . '\'); }</script>'
                )
            )
            ->assign(array(
                    'sCategories' => $sCategories,
                    'aLanguages' => $aLanguages,
                    'lang_id' => $lang_id,
                    'sUrlManageCategory' => $this->url()->makeUrl('admincp.gettingstarted.managearticlecategory'),
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('gettingstarted.component_controller_admincp_managearticlecategory_clean')) ? eval($sPlugin) : false);
    }
}

?>