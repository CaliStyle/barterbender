<?php

namespace Apps\YNC_Blogs\Controller;

use Phpfox;
use Phpfox_Error;
use Phpfox_Component;
use Apps\YNC_Blogs\Libraries\Wordpress as Wordpress;
use Apps\YNC_Blogs\Libraries\Tumblr as Tumblr;
use Apps\YNC_Blogs\Libraries\Blogger as Blogger;

class ImportController extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true) && user('yn_advblog_import', null, null, true);

        $aFilterMenu = Phpfox::getService('ynblog.helper')->buildFilterMenu();
        $this->template()->buildSectionMenu('ynblog', $aFilterMenu);

        $this->template()->setTitle(_p('import_blog'));
        $this->template()
            ->setBreadCrumb(_p('Blogs'), $this->url()->makeUrl('ynblog'))
            ->setBreadCrumb(_p('import_blog'), '');

        // Check if do not have any categories
        $sCategoriesGet = Phpfox::getService('ynblog.category')->get();

        if (!$sCategoriesGet) {
            Phpfox_Error::display(_p('there_are_no_categories'));
        }
        if ($aVals = $this->request()->getArray('val')) {
            $aCategories = array_filter($aVals['category']);
            //Check empty category
            if (empty($aCategories)) {
                Phpfox_Error::set(_p('provide_a_category_this_item_will_belong_to'));
            }
            if (isset($aVals['import_type']) && in_array(intval($aVals['import_type']), [1, 2])) {
                $sFile = Phpfox::getLib('file')->load('file', array("xml"), null);
            }
            if (Phpfox_Error::isPassed()) {
                switch ($aVals['import_type']) {
                    // Wordpress
                    case '1':
                        if (!empty($sFile)) {
                            $oWordPress = new Wordpress($sFile['tmp_name']);
                            $aPosts = $oWordPress->getPosts();
                        }
                        break;
                    // Blogger
                    case '2':
                        if (!empty($sFile)) {
                            $oBlogger = new Blogger($sFile['tmp_name']);
                            $aPosts = $oBlogger->getPosts();
                        }
                        break;
                    // Tumblr
                    case '3':
                        if (!empty($aVals['txt_tumblr_username'])) {
                            $oTumblr = new Tumblr($aVals['txt_tumblr_username']);
                            $aPosts = $oTumblr->getPosts();
                        }
                        break;
                }

                if (!empty($aPosts)) {
                    $bSuccess = Phpfox::getService('ynblog.process')->importBlogs($aPosts, $aCategories);
                    if ($bSuccess) {
                        $sMsg = _p('blog_s_successfully_imported');
                    } else {
                        $sMsg = _p('import_error_or_no_entry_was_gotten');
                    }
                } else {
                    $sMsg = _p('import_error_or_no_entry_was_gotten');
                }

                Phpfox::addMessage($sMsg);
                $this->url()->send('ynblog.import');
            }
        }

        $this->template()->assign(array(
                'sType' => $this->request()->get('sType'),
                'bIsInDetail' => true,
                'aCategories' => $sCategoriesGet,
                'aCurrentAuthor' => Phpfox::getService('ynblog.blog')->getCurrentAuthor(Phpfox::getUserId()),
                'aLatestPost' => Phpfox::getService('ynblog.blog')->getRecentPosts('latest_post', 1, null, 'AND u.user_id = ' . Phpfox::getUserId()),
            )
        );
    }
}
