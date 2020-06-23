<?php
/**
 * @copyright		[FOXEXPERT_COPYRIGHT]
 * @author  		Belan Ivan
 * @package  		App_AdvancedFooter
 */
namespace Apps\Advancedfooter\Controller\Admin;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class DeleteMenuController extends Phpfox_Component
{
    public function process()
    {
        $iDeleteId = $this->request()->getInt('delete');
        if ($iDeleteId && $aCategory = Phpfox::getService('advancedfooter.menu')->getCategory($iDeleteId)) {
            $this->template()->assign([
                    'iDeleteId' => $iDeleteId,
                ]
            );
            if (($aVals = $this->request()->getArray('val'))) {
                if (Phpfox::getService('advancedfooter.menu')->deleteCategory($iDeleteId, $aVals)) {
                    $this->url()->send('admincp.advancedfooter.index', false,
                        _p('Successfully deleted menu'));
                }
            }
        } else {
            Phpfox_Error::display(_p('category_not_found'));
        }

        $this->template()->setTitle(_p('Delete Menu'))
            ->setBreadCrumb(_p('Delete menu'));
    }
}
