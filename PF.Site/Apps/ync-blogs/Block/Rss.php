<?php

namespace Apps\YNC_Blogs\Block;

use Phpfox_Component;
use Phpfox;

class Rss extends Phpfox_Component
{
    public function process()
    {
        if(!Phpfox::getParam('ynblog.yn_advblog_on_off_rss')){
            return false;
        }

        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        $bIsProfile = $this->getParam('bIsProfile');

        if ($this->request()->get(($bIsProfile === true ? 'req3' : 'req2')) == 'category') {
            if ($aBlogCategory = Phpfox::getService('ynblog.category')->getCategory($this->request()->getInt(($bIsProfile === true ? 'req4' : 'req3')))) {
                if ($aBlogCategory['parent_id'] > 0 && $aBlogParentCategory = Phpfox::getService('ynblog.category')->getCategory($aBlogCategory['parent_id'])) {
                    if ($aBlogParentCategory['parent_id'] && $aBlogGrandParentCategory = Phpfox::getService('ynblog.category')->getCategory($aBlogParentCategory['parent_id'])) {
                        $url = $this->url()->permalink('ynblog.rss.category', $aBlogGrandParentCategory['category_id'], $aBlogGrandParentCategory['name']);
                    }else{
                        $url = $this->url()->permalink('ynblog.rss.category', $aBlogParentCategory['category_id'], $aBlogParentCategory['name']);
                    }
                }else{
                    $url = $this->url()->permalink('ynblog.rss.category', $aBlogCategory['category_id'], $aBlogCategory['name']);
                }
            }
        }else{
            $url = $this->url()->makeUrl('ynblog.rss');
        }

        $this->template()->assign(
            array(
                'url' => $url,
            )
        );

        return 'block';
    }
}
