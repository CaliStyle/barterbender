<?php
namespace Apps\YNC_Comment\Controller\Admin;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;


class AddStickerSetController extends Phpfox_Component
{
    public function process()
    {
        $bIsEdit = false;
        if ($iId = $this->request()->getInt('id')) {
            $bIsEdit = true;
            $aStickerSet = Phpfox::getService('ynccomment.stickers')->getForEdit($iId);
            if (!$aStickerSet) {
                $this->url()->send('admincp.app',['id' => 'YNC_Comment']);
            }
            $aStickers = Phpfox::getService('ynccomment.stickers')->getStickersBySet($iId);
            $aStickerSet['params'] = [
                'id' => $aStickerSet['set_id']
            ];
            $this->template()->assign([
                'aForms' => $aStickerSet,
                'aStickers' => $aStickers,
                'iEditId' => $iId,
            ]);
        }

        if ($aVals = $this->request()->getArray('val')) {

        }
        $sTitle = $bIsEdit ? (!empty($aStickerSet['view_only']) ? _p('preview_sticker_set') : _p('edit_sticker_set')) : _p('add_sticker_set');
        $this->template()->setTitle($sTitle)
            ->setHeader([
                'css/backend.css' => 'app_ync-comment',
                'jscript/admin.js' => 'app_ync-comment'
            ])
            ->setPhrase([
                'error',
                'notice',
                'sticker_set_updated_successfully',
                'sticker_set_added_successfully',
                'please_remove_all_error_files_first'
            ])
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("Advanced Comment"), $this->url()->makeUrl('admincp.app', ['id' => 'YNC_Comment']))
            ->setBreadCrumb($sTitle)
            ->assign([
                'bIsEdit' => $bIsEdit,
                'sTitle' => $sTitle
            ]);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ynccomment.component_controller_admincp_add_sticker_set_clean')) ? eval($sPlugin) : false);
    }
}