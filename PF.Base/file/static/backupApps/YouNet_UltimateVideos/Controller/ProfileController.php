<?php
/**
 * Created by PhpStorm.
 * User: namnv
 * Date: 8/19/16
 * Time: 4:52 PM
 */

namespace Apps\YouNet_UltimateVideos\Controller;

use Phpfox;
use Phpfox_Component;

class ProfileController extends Phpfox_Component
{
    public function process()
    {
        $this->setParam('bIsProfile', true);

        if ($this->request()->get('req3') == 'playlist') {

            Phpfox::getComponent('ultimatevideo.playlist', ['bNoTemplate' => true], 'controller');
        } else {
            Phpfox::getComponent('ultimatevideo.index', ['bNoTemplate' => true], 'controller');
        }
    }
}