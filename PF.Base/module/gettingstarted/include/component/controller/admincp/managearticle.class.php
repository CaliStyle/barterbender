<?php

/**
 *
 *
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_GettingStarted
 * @version          2.01
 */
defined('PHPFOX') or exit('NO DICE!');

class Gettingstarted_component_controller_admincp_managearticle extends Phpfox_Component
{
    public function process()
    {
        //delete articles by article_ids
        if ($aDeleteIds = $this->request()->getArray('id')) {
            if (Phpfox::getService('gettingstarted.articlecategory')->deleteMultipleArticle($aDeleteIds)) {
                $this->url()->send('admincp.gettingstarted.managearticle', null, _p('gettingstarted.knowledge_base_articles_successfully_deleted'));
            }
        }

        $aLanguages = PhpFox::getService('language')->getAll();
        $aTypes_Lang = array();
        foreach ($aLanguages as $key => $value) {
            $aTypes_Lang[$value['language_id']] = $value['title'];
        }

        if ($this->request()->get('req4') == '' && $this->request()->get('search-id') == '') {
            unset($_SESSION['aTypes']);
        }
        $aTypes = array();
        if (isset($_SESSION['aTypes'])) {
            $aTypes = $_SESSION['aTypes'];
        } else {
            $aTypes = Phpfox::getService('gettingstarted.articlecategory')->getCategoryForSearchArticle($aLanguages[0]['language_id']);
        }

        //get languages for search filter option
        $aTypes_featured = array(
            '0' => _p('gettingstarted.any'),
            '1' => _p('gettingstarted.unfeatured'),
            '2' => _p('gettingstarted.featured'),
        );

        //define a filter for search
        $aFilters = array(
            'title' => array(
                'type' => 'input:text',
                'search' => "[VALUE]"
            ),
            'type' => array(
                'type' => 'select',
                'options' => $aTypes,
                'default' => '1',
                'search' => "type_[VALUE]"
            ),
            'language' => array(
                'type' => 'select',
                'options' => $aTypes_Lang,
                'default' => '1',
                'search' => "language_[VALUE]"
            ),
            'featured' => array(
                'type' => 'select',
                'options' => $aTypes_featured,
                'default' => '-1',
                'search' => "featured_[VALUE]"
            )
        );
        //set and build a search
        $oSearch = Phpfox::getLib('search')->set(array(
                'type' => '',
                'filters' => $aFilters,
                'search' => 'search',
                'name' => 'title'
            )
        );

        $bIsSearch = false;
        $sSearch = $this->request()->get('search-id');

        if ($sSearch != '') {
            $bIsSearch = true;
        }
        //get search conditions
        $arrSearch = $oSearch->getConditions();
        $title_search = "";
        $category_search = 0;
        $language_id = $aLanguages[0]['language_id'];
        $iFeatured_value = -1;
        if (count($arrSearch) > 6) {
            $title_search = $arrSearch[0];
            $arrtemp_search = explode("type_", $arrSearch[1]);

            $category_search = $arrtemp_search[1];

            $arrtemp_search = explode("language_", $arrSearch[3]);
            $language_id = $arrtemp_search[1];

            $this->setParam('sLanguage_id', $language_id);


            $arrtemp_search = explode("featured_", $arrSearch[5]);
            $iFeatured_value = $arrtemp_search[1] - 1;
        } //get search conditions value when title search isn't existed
        else if (count($arrSearch) == 6) {
            $arrtemp_search = explode("type_", $arrSearch[0]);
            if ($arrtemp_search[1] != '')
                $category_search = $arrtemp_search[1];

            $arrtemp_search = explode("language_", $arrSearch[2]);
            if ($arrtemp_search[1] != '') {
                $language_id = $arrtemp_search[1];

                $this->setParam('sLanguage_id', $language_id);
            }

            $arrtemp_search = explode("featured_", $arrSearch[4]);
            if ($arrtemp_search[1] != '')
                $iFeatured_value = $arrtemp_search[1] - 1;
        } else {
            $this->setParam('sLanguage_id', $aLanguages[0]['language_id']);
        }
        //define limit article on page for Pgaer
        $iLimit = 5;
        $iPage = $this->request()->get("page");
        if (!$iPage) {
            $iPage = 1;
        }
        $sSortBy = 'article.time_stamp desc';
        //get total search result articles for Pager
        $iCnt = Phpfox::getService("gettingstarted.articlecategory")->getCountArticleForManage($iFeatured_value, $language_id, $title_search, $category_search, $sSortBy);

        $aCategories = Phpfox::getService('gettingstarted.articlecategory')->getArticleForManage($iFeatured_value, $language_id, $title_search, $category_search, $sSortBy, $iPage, $iLimit, $iCnt);

        foreach($aCategories as $iKey => $aCategory)
        {
            $aCategories[$iKey]['description_parsed'] = Phpfox::getLib('parse.bbcode')->parse($aCategory['description_parsed']);
        }

        for ($i = 0; $i < count($aCategories); $i++) {
            $aCategories[$i]['title'] = PHPFOX::getService('gettingstarted.search')->highlight($title_search, $aCategories[$i]['title']);
        }

        Phpfox::getService('gettingstarted.articlecategory')->getExtra($aCategories);
        Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $iCnt));


        $this->setParam('language_id', $language_id);
        $this->template()
            ->assign(array(
                    'aCategories' => $aCategories,
                    'corepath' => phpfox::getParam("core.path"),
                    'bIsSearch' => $bIsSearch,
                    'iPage' => $iPage,
                    'language_hidden' => $language_id
                )
            )
            ->setHeader('cache', array(
                    'quick_edit.js' => 'static_script'
                )
            );
        $this->template()->setBreadCrumb(_p('gettingstarted.manage_articles'), $this->url()->makeUrl('admincp.gettingstarted.managearticle'));
    }

}

?>
