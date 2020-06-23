<?php

namespace Apps\YNC_Blogs\Service;

use Phpfox;
use Phpfox_Service;

class Helper extends Phpfox_Service
{
    /**
     * @return array
     */
    public function buildFilterMenu()
    {
        $aFilterMenu = array(
            _p('all_blogs') => '',
        );

        if (Phpfox::isUser()) {
            $iMyTotal = Phpfox::getService('ynblog.blog')->getMyTotal();
            $iMyTotal = ($iMyTotal >= 100) ? '99+' : $iMyTotal;
            $aFilterMenu[_p('my_blogs') . ($iMyTotal ? '<span class="pending count-item">' . $iMyTotal . '</span>' : '')] = 'my';
        }

        if (!Phpfox::getParam('core.friends_only_community') && Phpfox::isModule('friend')) {
            $aFilterMenu[_p('friends_blogs')] = 'friend';
        }

        if (Phpfox::isUser()) {
            $iFavoriteTotal = Phpfox::getService('ynblog.blog')->getFavoriteTotal();
            $iFavoriteTotal = ($iFavoriteTotal >= 100) ? '99+' : $iFavoriteTotal;
            $aFilterMenu[_p('my_favorites') . ($iFavoriteTotal ? '<span class="favorite count-item" id="total_favorite_blog" ' . (!$iFavoriteTotal ? 'style="display: none"' : "") . '>' . $iFavoriteTotal . '</span>' : '')] = 'favorite';

            $iFollowingTotal = Phpfox::getService('ynblog.blog')->getFollowingTotal();
            $iFollowingTotal = ($iFollowingTotal >= 100) ? '99+' : $iFollowingTotal;
            $aFilterMenu[_p('my_following_bloggers') . ($iFollowingTotal ? '<span class="following count-item" id="total_follow_blogger" ' . (!$iFollowingTotal ? 'style="display: none"' : "") . '>' . $iFollowingTotal . '</span>' : '')] = 'ynblog.following';

            $iSavedToTal = Phpfox::getService('ynblog.blog')->getSavedTotal();
            $iSavedToTal = ($iSavedToTal >= 100) ? '99+' : $iSavedToTal;
            $aFilterMenu[_p('saved_blogs') . '<span class="saved count-item" id="total_saved_blog" ' . (!$iSavedToTal ? 'style="display: none"' : "") . '>' . $iSavedToTal . '</span>'] = 'saved';
        }

        if (Phpfox::getUserParam('yn_advblog_approve')) {
            $iPendingTotal = Phpfox::getService('ynblog.blog')->getPendingTotal();
            $iPendingTotal = ($iPendingTotal >= 100) ? '99+' : $iPendingTotal;

            if ($iPendingTotal) {
                $aFilterMenu[_p('pending_blogs') . (Phpfox::getUserParam('yn_advblog_approve') ? '<span class="pending count-item">' . $iPendingTotal . '</span>' : 0)] = 'pending';
            }
        }

        if (Phpfox::isUser() && user('yn_advblog_import')) {
            $aFilterMenu[_p('import_blog')] = 'ynblog.import';
        }

        return $aFilterMenu;
    }

    /**
     * @param $sTime
     * @return false|int
     */
    public function time_to_timestamp_begin($sTime)
    {
        $a = strptime($sTime, '%m/%d/%Y');
        return mktime(0, 0, 0, $a['tm_mon'] + 1, $a['tm_mday'], $a['tm_year'] + 1900);
    }

    /**
     * @param $sTime
     * @return false|int
     */
    public function time_to_timestamp_end($sTime)
    {
        $a = strptime($sTime, '%m/%d/%Y');
        return mktime(23, 59, 59, $a['tm_mon'] + 1, $a['tm_mday'], $a['tm_year'] + 1900);
    }


    /**
     * @param $sImage
     * @param $iServerId
     * @param $sSuffix
     * @return string
     */
    public function getImagePath($sImage, $iServerId, $sSuffix)
    {
        if (empty($sImage)) {
            $sImage = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/ync-blogs/assets/image/blog_photo_default.png';
        } elseif (strpos($sImage, 'http') !== false) {
            // Do nothing
        } else {
            $sImage = Phpfox::getLib('image.helper')->display(
                [
                    'server_id' => $iServerId,
                    'path' => 'core.url_pic',
                    'file' => 'ynadvancedblog/' . $sImage,
                    'suffix' => $sSuffix,
                    'return_url' => true,
                ]
            );
        }

        return $sImage;
    }

    public function getRandomItems($aItem, $iLimit)
    {
        shuffle($aItem);
        return array_slice($aItem, 0, $iLimit);
    }
}