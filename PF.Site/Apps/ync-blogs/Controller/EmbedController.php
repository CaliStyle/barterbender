<?php

namespace Apps\YNC_Blogs\Controller;
defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;

class EmbedController extends Phpfox_Component
{
    public function process()
    {
        $iBlogId = $this->request()->getInt('req3');

        if (!$iBlogId) {
            exit(_p('invalid_param'));
        }

        $aBlog = Phpfox::getService('ynblog.blog')->getBlog($iBlogId);

        if (!$aBlog) {
            exit(_p('unable_to_view_this_item_due_to_privacy_settings'));
        }

        $aStaticFiles = $this->template()->getHeader(true);

        foreach ($aStaticFiles as $key => $sFile) {
            if (!preg_match('/jquery(.*).js/i', $sFile)) {
                unset($aStaticFiles[$key]);
            }
        }

        $this->template()->setTitle(Phpfox::getLib('locale')->convert($aBlog['title']));

        $this->template()->assign(array(
            'aItem' => $aBlog,
            'sCorePath' => Phpfox::getParam('core.path_actual') . 'PF.Base/',
            'appPath' => Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/ync-blogs/',
            'aFiles' => $aStaticFiles,
        ))
            ->setHeader('cache', array(
                'jquery/plugin/jquery.highlightFade.js' => 'static_script',
            )
        );
        Phpfox::getLib('module')->getControllerTemplate();
        die;
    }
}
