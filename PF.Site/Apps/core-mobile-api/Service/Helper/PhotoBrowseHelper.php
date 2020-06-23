<?php
/**
 * @author  phpFox LLC
 * @license phpfox.com
 */

namespace Apps\Core_MobileApi\Service\Helper;

use Apps\Core_Photos\Service\Browse as Browse;
use Phpfox;

class PhotoBrowseHelper extends Browse
{
    public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false)
    {
        parent::getQueryJoins($bIsCount, $bNoQueryFriend);
        if ($this->request()->get('tag') && $this->request()->get('req2') != 'tag') {
            db()->innerJoin(Phpfox::getT('tag'), 'tag', 'tag.item_id = photo.photo_id AND tag.category_id = \'photo\'');
        }
        if ($this->request()->get('feed_id')) {
            db()->leftJoin(Phpfox::getT('photo_feed'), 'pfeed', 'photo.photo_id = pfeed.photo_id');
        }
    }
}