<?php
namespace Apps\YNC_Comment\Controller\Admin;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;


class ManageStickersController extends Phpfox_Component
{
    public function process()
    {
        if ($iId = $this->request()->getInt('delete')) {
            if (Phpfox::getService('ynccomment.stickers.process')->deleteStickerSet($iId)) {
                $this->url()->send('admincp.app', ['id' => 'YNC_Comment'], _p('sticker_set_deleted_successfully'));
            }
        }
        if ($iId = $this->request()->getInt('default')) {
            if (Phpfox::getService('ynccomment.stickers.process')->setDefaultSet($iId)) {
                $this->url()->send('admincp.app', ['id' => 'YNC_Comment'],
                    _p('sticker_set_marked_as_default_successfully'));
            } else {
                $this->url()->send('admincp.app', ['id' => 'YNC_Comment'], _p('oops_something_went_wrong'));
            }
        }
        if ($iId = $this->request()->getInt('un_default')) {
            if (Phpfox::getService('ynccomment.stickers.process')->setDefaultSet($iId, true)) {
                $this->url()->send('admincp.app', ['id' => 'YNC_Comment'],
                    _p('sticker_set_removed_default_successfully'));
            } else {
                $this->url()->send('admincp.app', ['id' => 'YNC_Comment'],
                    _p('failed_need_at_least_one_default_sticker_set'));
            }
        }
        if ($aIds = $this->request()->getArray('ids')) {
            foreach ($aIds as $iId) {
                Phpfox::getService('ynccomment.stickers.process')->deleteStickerSet($iId);
            }
            $this->url()->send('admincp.app', ['id' => 'YNC_Comment'], _p('sticker_set_s_deleted_successfully'));
        }
        $this->template()
            ->setTitle(_p('manage_stickers'))
            ->setBreadCrumb(_p('manage_stickers'))
            ->setHeader([
                'jscript/admin.js' => 'app_ync-comment'
            ])
            ->assign([
                'aStickerSets' => Phpfox::getService('ynccomment.stickers')->getForAdmin(),
            ]);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ynccomment.component_controller_admincp_manage_stickers_clean')) ? eval($sPlugin) : false);
    }
}