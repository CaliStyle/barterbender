<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\YouNet_UltimateVideos\Controller\Admin;

use Phpfox;
use Phpfox_Component;

defined('PHPFOX') or exit('NO DICE!');

class UltilitiesController extends Phpfox_Component
{
    public function process()
    {
        $isError = false;
        $ffmpegPath = setting('ynuv_ffmpeg_path');
        $version = $format = "";
        if (function_exists('exec')) {
            $output = null;
            $return = null;
            if (!empty($ffmpegPath)) {
                exec($ffmpegPath . ' -version', $output, $return);
            }
        }
        exec($ffmpegPath . ' -version', $output, $return);
        if (!empty($ffmpegPath) && $return == 0) {
            $version = shell_exec(escapeshellcmd($ffmpegPath) . ' -version 2>&1');
            $command = "$ffmpegPath -formats 2>&1";
            $format = shell_exec(escapeshellcmd($ffmpegPath) . ' -formats 2>&1')
                . shell_exec(escapeshellcmd($ffmpegPath) . ' -codecs 2>&1');
        } else {
            $isError = true;
        }
        if ($return != 0) {
            $isError = true;
        }
        $this->template()->setTitle(_p('video_ultilities'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("ultimate_videos"), Phpfox::getLib('url')->makeUrl('admincp.app', ['id' => 'YouNet_UltimateVideos']))
            ->setBreadcrumb(_p('video_ultilities'), $this->url()->makeUrl('admincp.ultimatevideo.ultilities'))
            ->assign(array(
                'isError' => $isError,
                'sVersion' => $version,
                'sFormat' => $format,
            ));
    }
}