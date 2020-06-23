<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Videochannel_Component_Block_Mostdiscussed extends Phpfox_Component
{
    public function process()
    {
        $iId = $this->request()->getInt('req3');
        $aParentModule = $this->getParam('aParentModule');
        $aMostDiscussed = Phpfox::getService('videochannel')->getMostDiscussedVideos(5,
            isset($aParentModule['module_id']) ? $aParentModule['module_id'] : null,
            isset($aParentModule['item_id']) ? $aParentModule['item_id'] : null);
        $sView = $this->request()->get('view');
        if (count($aMostDiscussed) == 0 || defined('PHPFOX_IS_USER_PROFILE') || $sView == 'channels'
            || $sView == 'all_channels') {
            return false;
        }

        $sLink = isset($aParentModule['module_id']) ? ($aParentModule['module_id'] . '/' . $aParentModule['item_id'] . '/videochannel') : 'videochannel';

        $this->template()->assign(array(
                'sHeader' => _p('videochannel.most_discussed'),
                'aMostDiscussed' => $aMostDiscussed,
                'bViewMore' => (count($aMostDiscussed) == 5) ? true : false,
                'sLink' => isset($aParentModule['module_id']) ? ($aParentModule['module_id'] . '/' . $aParentModule['item_id'] . '/videochannel') : 'videochannel'
            )
        );

        if (count($aMostDiscussed) >= 5) {
            $this->template()->assign(array(
                'aFooter' => array(
                    _p('view_more') => $this->url()->makeUrl($sLink) . '?sort=most-talked'
                ),
            ));
        }

        return 'block';
    }
}

?>
