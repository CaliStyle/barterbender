<?php
/**
 *
 *
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_GettingStarted
 * @version          2.01
 */

defined('PHPFOX') or exit('NO DICE!');

class Gettingstarted_component_controller_admincp_managecategory extends Phpfox_Component{
    public function process()
    {

        $iLimit = 10;
        $iPage = $this->request()->get("page");
        if(!$iPage)
        {
            $iPage = 1;
        }
        $iCnt = Phpfox::getService("gettingstarted")->getCountCategory();
        $aCategories = Phpfox::getService('gettingstarted')->getCategory($iPage,$iLimit,$iCnt);
        foreach($aCategories as $iKey => $value)
        {
        	$aCategories[$iKey]['description'] = _p($aCategories[$iKey]['description']);
        }
        Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $iCnt));
        $this->template()
            ->assign(array(
                        'aCategories' => $aCategories,
                        'corepath' => phpfox::getParam("core.path"),
                    )
            )
            ->setHeader('cache', array(
                    'quick_edit.js' => 'static_script'
            )
        );
    }
}
?>
