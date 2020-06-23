<?php

namespace Apps\YNC_Blogs\Block;

use Phpfox_Component;
use Phpfox;

class Author extends Phpfox_Component
{
    public function process()
    {
        $bIsInDetail = $this->getParam('blog_id', 0);

        if ($bIsInDetail) {
            $aBlog = $this->getParam('aBlog');
            $iUserId = (int)$aBlog['user_id'];
            $sHeader = _p('about_blogger');
        } else {
            $iUserId = Phpfox::getUserId();
            $sHeader = _p('statistic');
        }

        if (!$iUserId) return false;

        $aCurrentAuthor = Phpfox::getService('ynblog.blog')->getCurrentAuthor($iUserId);

        if (empty($aCurrentAuthor) || (!$this->request()->getInt('req2', false) && $this->request()->get('view') != 'my')) {
            return false;
        }

        if (!empty($aCurrentAuthor['cover_photo'])) {
            $aCurrentAuthor['aCoverPhoto'] = Phpfox::getService('photo')->getCoverPhoto($aCurrentAuthor['cover_photo']);;
        } else {
            $aCurrentAuthor['aCoverPhoto'] = array();
        }

        $aCurrentAuthor['canFollow'] = Phpfox::getUserId() && $aCurrentAuthor['user_id'] != Phpfox::getUserId() && user('yn_advblog_follow');
        $this->template()
            ->assign([
                'sHeader' => $sHeader,
                'sCustomClassName' => 'p-block',
                'aCurrentAuthor' => $aCurrentAuthor,
                'bIsInDetail' => $bIsInDetail,
                'aLatestPost' => ($bIsInDetail ? Phpfox::getService('ynblog.blog')->getRecentPosts('latest_post' . $aCurrentAuthor['user_id'], 1, 'ab.time_stamp DESC', 'AND u.user_id = ' . $aCurrentAuthor['user_id']) : []),
                'sCoverDefaultUrl' => flavor()->active->default_photo('user_cover_default', true),
            ]);

        return 'block';
    }
}

