<?php

namespace Apps\YNC_Core\Block;

use Phpfox_Component;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class IndexController
 * @package Apps\YNC_Core\Controller
 */
class ModeView extends Phpfox_Component
{
    public function process()
    {
        $aSupportedViewModes = $this->getParam('aSupportedViewModes');
        $sModeViewId = $this->getParam('sModeViewId');
        $sModeViewDefault = $this->getParam('sModeViewDefault');

        if (!empty($aSupportedViewModes)) {
            $aSupportedViewModeKeys = array_keys($aSupportedViewModes);
            if (!$sModeViewDefault) {
                $sModeViewDefault = $aSupportedViewModeKeys[0];
            }
            if (count($aSupportedViewModes) > 1) {
                if (isset($_COOKIE['p-mode-view-cookie-' . $sModeViewId])) {
                    $sModeViewCookie = $_COOKIE['p-mode-view-cookie-' . $sModeViewId];
                    if (in_array($sModeViewCookie, $aSupportedViewModeKeys)) {
                        $sModeViewDefault = $sModeViewCookie;
                    }
                }
            }
        }

        $this->template()->assign(array(
            'aViewModes' => $aSupportedViewModes,
            'sModeViewDefault' => $sModeViewDefault,
            'sModeViewId' => $sModeViewId
        ));
    }
}
