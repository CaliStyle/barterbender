<?php
namespace Apps\YNC_Feed\Block\Share;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox_Component;
use Phpfox;

class Preview extends Phpfox_Component
{
    public function process()
    {
        $parentFeedId = $this->getParam('parent_feed_id');
        $parentModuleId = $this->getParam('parent_module_id');

        $tempContent = ob_get_contents();
        ob_clean();
        $content = Phpfox::getService('ynfeed')->getPreviewContent('block','ynfeed.mini', [
            'parent_feed_id' => $parentFeedId,
            'parent_module_id' => $parentModuleId
        ]);
        echo $tempContent;
        if(!empty($content)) {
            $content = preg_replace_callback('/<form.*?>/',function($match) {
                return '';
            }, $content);
            $content = preg_replace_callback('/<\/form>/',function($match) {
                return '';
            }, $content);
            $this->template()->assign('content', $content);
            return 'block';
        }
        return false;
    }
}
