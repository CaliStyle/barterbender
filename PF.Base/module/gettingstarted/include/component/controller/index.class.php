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

class Gettingstarted_Component_Controller_Index extends Phpfox_Component
{

    public function process()
    {
        $iPage = 0;
        $bIsCategory = false;
        $temp = $this->request()->get('req2');
        $flag = 1;
        $this->search()->browse()->setPagingMode(Phpfox::getParam('gettingstarted.gettingstarted_paging_mode', 'loadmore'));
        if ($temp == null) {
            $flag = 0;
            $this->template()->setBreadCrumb(_p('gettingstarted.knowledge_base'), $this->url()->makeUrl('gettingstarted'));

            if (defined('PHPFOX_IS_AJAX_CONTROLLER')) {
                $bIsProfile = true;
                $aUser = Phpfox::getService('user')->get($this->request()->get('profile_id'));
                $this->setParam('aUser', $aUser);
            } else {
                $bIsProfile = $this->getParam('bIsProfile');
                if ($bIsProfile === true) {
                    $aUser = $this->getParam('aUser');
                }
            }

            $bIsSearch = false;
            $settings = phpfox::getService('gettingstarted.settings')->getSettings(0);
            $sView = $this->request()->get('view');

            if (isset($settings['active_knowledge_base'])) {
                if ($settings['active_knowledge_base'] == false) {
                    Phpfox::getLib('database')->update(phpfox::getT('menu'), array('is_active' => 0), 'm_connection="' . 'main' . '" and module_id="' . "gettingstarted" . '"');
                    return Phpfox::getLib('module')->setController('error.404');
                } else {
                    Phpfox::getLib('database')->update(phpfox::getT('menu'), array('is_active' => 1), 'm_connection="' . 'main' . '" and module_id="' . "gettingstarted" . '"');
                }
            }

            $iLimit = $this->request()->get('show');

            if ($iLimit == '') {
                $iLimit = 5;
            }
            $iPage = $this->request()->get("page");

            if (!$iPage) {
                $iPage = 1;
            }


            $all_articlecategories = Phpfox::getService("gettingstarted.articlecategory")->getArticleCategory();
            $aTypes = array();
            $aTypes[0] = "Any";
            $aPages = array(5, 10, 15);
            $aDisplays = array();
            foreach ($aPages as $iPageCnt) {
                $aDisplays[$iPageCnt] = _p('core.per_page', array('total' => $iPageCnt));
            }

            $aSorts = array(
                'gettingstarted_article.time_stamp' => '',
                'gettingstarted_article.total_view' => ''
            );
            $aFilters = array(
                'title' => array(
                    'type' => 'input:text',
                    'search' => "[VALUE]"
                ),
                'display' => array(
                    'type' => 'select',
                    'options' => $aDisplays,
                    'default' => '15'
                ),
                'sort' => array(
                    'type' => 'select',
                    'options' => $aSorts,
                    'default' => 'gettingstarted_article.time_stamp'
                ),
            );
            $oSearch = Phpfox::getLib('search')->set(array(
                    'type' => 'gettingstarted_article',
                    'field' => 'gettingstarted_article.article_id', //by HT
                    'filters' => $aFilters,
                    'search_tool' => array(
                        'table_alias' => 'gettingstarted_article',
                        'search' => array(
                            'action' => ($bIsProfile === true ? $this->url()->makeUrl($aUser['user_name'], array('gettingstarted', 'view' => $this->request()->get('gettingstarted'))) : $this->url()->makeUrl('gettingstarted', array('view' => $this->request()->get('view')))),
                            'default_value' => _p('gettingstarted.search_articles_dot'),
                            'name' => 'title',
                            'field' => 'gettingstarted_article.title'
                        ),
                        'sort' => array(
                            'latest' => array('gettingstarted_article.time_stamp', _p('latest'))
                        ),
                        'show' => array(15, 10, 5)
                    )
                )
            );

            $arrSearch = $oSearch->getConditions();
            $arrSort = $oSearch->getSort();

            //by HT -------------------------------------------
            //---------------------------------------------

            foreach ($all_articlecategories as $iKey => $articlecategory) {
                $aTypes[$articlecategory['article_category_id']] = $articlecategory['article_category_name'];
            }

            if ($this->request()->get(($bIsProfile === true ? 'req3' : 'req2')) == 'categories') {
                $bIsSearch = true;
                if ($aFeedBackCategory = Phpfox::getService('gettingstarted')->getArticleCategoryforEdit($this->request()->getInt(($bIsProfile === true ? 'req4' : 'req3')))) {
                    $this->template()->setBreadCrumb(_p('gettingstarted.category'));

                    $this->template()->setTitle(Phpfox::getLib('locale')->convert($aFeedBackCategory['article_category_name']));
                    $this->template()->setBreadCrumb(Phpfox::getLib('locale')->convert($aFeedBackCategory['article_category_name']), $this->url()->makeUrl('current'), true);
                    $this->search()->setFormUrl($this->url()->permalink(array('gettingstarted.categories', 'view' => $this->request()->get('view')), $aFeedBackCategory['article_category_id'], $aFeedBackCategory['article_category_name']));
                }
                $iPage = $this->search()->getPage();
            }

            $sSearch = $this->request()->get('search-id');
            $arrSearch = $oSearch->getConditions();

            if (($sSearch != '') || (!empty($arrSearch))) {
                $bIsSearch = true;
            }
            if ($this->request()->get('makesearch') && $this->request()->get('makesearch') == 1) {
                $bIsSearch = true;
            }

            if ($bIsSearch == false) {
                $categoryCnt = Phpfox::getService("gettingstarted.articlecategory")->getCount() + 1;
                $articlecategories = Phpfox::getService("gettingstarted.articlecategory")->get($iPage, $categoryCnt, $categoryCnt);
                $iLimit = 5;
                $aSettings = phpfox::getService("gettingstarted.settings")->getSettings(0);
                if (!isset($aSettings['number_of_article_category'])) {
                    $iLimit = 5;
                } else {
                    $iLimit = $aSettings['number_of_article_category'];
                }
                // paging
                $iLimit = $this->request()->getInt('show') ? $this->request()->getInt('show') : $iLimit;

                $iCnt = 0;

                foreach ($articlecategories as $iKey => $articlecategory) {
                    $article = Phpfox::getService("gettingstarted.articlecategory")->getArticleByCategoryId($articlecategory['article_category_id'], $iLimit);
                    phpfox::getService('gettingstarted.articlecategory')->getExtra($article);
                    $pagination = 0;
                    $dsArticle = Phpfox::getService("gettingstarted.articlecategory")->getdsArticleByCategoryId($articlecategory['article_category_id']);
                    if (count($dsArticle) > $iLimit) {
                        $pagination = 1;
                    }
                    $articlecategories[$iKey]['pagination'] = $pagination;
                    $articlecategories[$iKey]['article'] = $article;

                    $iCnt += count($dsArticle);
                }
                Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $iCnt));
                $this->template()->assign(array('articlecategories' => $articlecategories));
            } else {

                $arrSearch = $oSearch->getConditions();

                $title_search = '';
                if (!empty($arrSearch)) {
                    $title_search = $arrSearch[0];
                }
                $category_search = $this->request()->getInt(($bIsProfile === true ? 'req4' : 'req3'));

                $categoryCnt = Phpfox::getService("gettingstarted.articlecategory")->getCount() + 1;
                $articlecategories = Phpfox::getService("gettingstarted.articlecategory")->get($iPage, $categoryCnt, $categoryCnt);

                $iLimit = 5;
                $aSettings = phpfox::getService("gettingstarted.settings")->getSettings(0);
                if (!isset($aSettings['number_of_article_category'])) {
                    $iLimit = 5;
                } else {
                    $iLimit = $aSettings['number_of_article_category'];
                }
                // paging
                $iLimit = $this->request()->getInt('show') ? $this->request()->getInt('show') : $iLimit;


                $iCnt = 0;
                foreach ($articlecategories as $iKey => $articlecategory) {
                    $article = Phpfox::getService("gettingstarted.articlecategory")->searchArticleByCategoryId($title_search, $articlecategory['article_category_id'], $iLimit, null, $arrSearch);
                    phpfox::getService('gettingstarted.articlecategory')->getExtra($article);
                    $pagination = 0;
                    $dsArticle = Phpfox::getService("gettingstarted.articlecategory")->searchdsArticleByCategoryId($title_search, $articlecategory['article_category_id'], $arrSearch);

                    if (count($dsArticle) > $iLimit) {
                        $pagination = 1;
                    }
                    $articlecategories[$iKey]['pagination'] = $pagination;
                    $articlecategories[$iKey]['article'] = $article;

                    $iCnt += count($dsArticle);
                }

                Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $iCnt));
                $search_id = $this->request()->get('search-id');
                if (strpos($title_search, 'time_stamp') !== false) {
                    $title_search = _p('gettingstarted.your_search_filter');
                }
                $this->template()->assign(array(
                    'articlecategories' => $articlecategories,
                    'iCnt' => $iCnt,
                    'search_id' => $search_id,
                    'title_search' => $title_search,
                    'iPage' => $iPage
                ));
            }

            $this->template()->assign(array(
                'bIsSearch' => $bIsSearch,
            ))
                ->setHeader(array(
                    'gettingstarted.js' => 'module_gettingstarted',
                    'pager.css' => 'style_css',
                ));
        } else {
            // START Category page
            $bIsCategory = true;
            $this->template()->setBreadCrumb(_p('gettingstarted.knowledge_base'), $this->url()->makeUrl('gettingstarted'));

            if (defined('PHPFOX_IS_AJAX_CONTROLLER')) {
                $bIsProfile = true;
                $aUser = Phpfox::getService('user')->get($this->request()->get('profile_id'));
                $this->setParam('aUser', $aUser);
            } else {
                $bIsProfile = $this->getParam('bIsProfile');
                if ($bIsProfile === true) {
                    $aUser = $this->getParam('aUser');
                }
            }

            $bIsSearch = false;
            $settings = phpfox::getService('gettingstarted.settings')->getSettings(0);
            $sView = $this->request()->get('view');
            if (isset($settings['active_knowledge_base'])) {
                if ($settings['active_knowledge_base'] == false) {
                    Phpfox::getLib('database')->update(phpfox::getT('menu'), array('is_active' => 0), 'm_connection="' . 'main' . '" and module_id="' . "gettingstarted" . '"');
                    Phpfox::getLib("cache")->remove('menu', 'substr');
                    return Phpfox::getLib('module')->setController('error.404');
                } else {
                    Phpfox::getLib('database')->update(phpfox::getT('menu'), array('is_active' => 1),
                        'm_connection="' . 'main' . '" and module_id="' . "gettingstarted" . '"');
                    Phpfox::getLib("cache")->remove('menu', 'substr');
                }
            }


            $iLimit = $this->request()->get('show');
            if ($iLimit == '') {
                $iLimit = 15;
            }
            // paging
            $iLimit = $this->request()->getInt('show') ? $this->request()->getInt('show') : $iLimit;

            $iPage = $this->request()->get("page");
            if (!$iPage) {
                $iPage = 1;
            }
            $all_articlecategories = Phpfox::getService("gettingstarted.articlecategory")->getArticleCategory();

            $aTypes = array();
            $aTypes[0] = "Any";
            $aPages = array(15, 10, 5);

            $aDisplays = array();
            foreach ($aPages as $iPageCnt) {
                $aDisplays[$iPageCnt] = _p('core.per_page', array('total' => $iPageCnt));
            }

            $aSorts = array(
                'gettingstarted_article.time_stamp' => '',
                'gettingstarted_article.total_view' => ''
            );
            $aFilters = array(
                'title' => array(
                    'type' => 'input:text',
                    'search' => "[VALUE]"
                ),
                'display' => array(
                    'type' => 'select',
                    'options' => $aDisplays,
                    'default' => '15'
                ),
                'sort' => array(
                    'type' => 'select',
                    'options' => $aSorts,
                    'default' => 'gettingstarted_article.time_stamp'
                ),
            );

            $oSearch = Phpfox::getLib('search')->set(array(
                    'type' => 'gettingstarted_article',
                    'filters' => $aFilters,
                    'field' => 'gettingstarted_article.article_id',
                    'search_tool' => array(
                        'table_alias' => 'a',
                        'search' => array(
                            'action' => ($bIsProfile === true ? $this->url()->makeUrl($aUser['user_name'], array('gettingstarted', 'view' => $this->request()->get('gettingstarted'))) : $this->url()->makeUrl('gettingstarted', array('view' => $this->request()->get('view')))),
                            'default_value' => _p('gettingstarted.search_articles_dot'),
                            'name' => 'title',
                            'field' => 'gettingstarted_article.title'
                        ),
                        'sort' => array(
                            'latest' => array('gettingstarted_article.time_stamp', 'Latest')
                        ),
                        'show' => $aPages
                    )
                )
            );


            $sSearch_name = $this->search()->get('title');
            foreach ($all_articlecategories as $iKey => $articlecategory) {
                $aTypes[$articlecategory['article_category_id']] = $articlecategory['article_category_name'];
            }

            $articlecategory = array();
            if ($this->request()->get(($bIsProfile == true ? 'req3' : 'req2')) == 'categories') {
                if ($aFeedBackCategory = Phpfox::getService('gettingstarted')->getArticleCategoryforEdit($this->request()->getInt($bIsProfile == true ? 'req4' : 'req3'))) {
                    $this->template()->setTitle(Phpfox::getLib('locale')->convert($aFeedBackCategory['article_category_name']));
                    $this->template()->setBreadCrumb(Phpfox::getLib('locale')->convert($aFeedBackCategory['article_category_name']), $this->url()->makeUrl('current'), true);
                    $this->search()->setFormUrl($this->url()->permalink(array('gettingstarted.categories', 'view' => $this->request()->get('view')), $aFeedBackCategory['article_category_id'], $aFeedBackCategory['article_category_name']));

                    $articlecategory['article_category_id'] = $this->request()->getInt($bIsProfile == true ? 'req4' : 'req3');

                } else {
                    $articlecategory['article_category_id'] = -1;

                    $this->url()->send('gettingstarted', null, _p('gettingstarted.category_does_not_exist'));
                }
            }

            $sSearch = $this->request()->get('search-id');
            $arrSearch = $oSearch->getConditions();
            if (($sSearch != '') || (!empty($arrSearch))) {
                $bIsSearch = true;
            }
            if ($this->request()->get('makesearch') && $this->request()->get('makesearch') == 1) {
                $bIsSearch = true;
            }
            if ($bIsSearch == false) {
                $articlecategories = array();
                $articlecategories[0] = Phpfox::getService('gettingstarted.articlecategory')->getArticleCategoryforEdit($articlecategory['article_category_id']);
                $iLimit = 10;
                $aSettings = phpfox::getService("gettingstarted.settings")->getSettings(0);
                if (!isset($aSettings['number_of_article_category'])) {
                    $iLimit = 10;
                } else {
                    $iLimit = $this->request()->getInt('show') ? $this->request()->getInt('show') : 15;
                }
                // paging
                $iLimit = $this->request()->getInt('show') ? $this->request()->getInt('show') : $iLimit;

                $pagination = 0;
                $dsArticle = Phpfox::getService("gettingstarted.articlecategory")->getdsArticleByCategoryId($articlecategory['article_category_id']);
                if (count($dsArticle) > $iLimit) {
                    $pagination = 1;
                }
                $iCnt = count($dsArticle);

                $iPage = $this->request()->getInt('page') ? $this->request()->getInt('page') : 1;
                Phpfox::getLib('pager')->set(array('page' => $iPage,
                    'size' => $iLimit,
                    'count' => $iCnt,
                    'paging_mode' => $this->search()->browse()->getPagingMode()
                    ));

                $article = Phpfox::getService("gettingstarted.articlecategory")->getArticleByCategoryId($articlecategory['article_category_id'], $iLimit, $iPage);

                $articletemp = Phpfox::getService("gettingstarted.articlecategory")->getArticleByCategoryId($articlecategory['article_category_id'], $iLimit, $iPage + 1);

                if (count($articletemp) > 0) {

                } else {
                    $flag = false;
                }


                phpfox::getService('gettingstarted.articlecategory')->getExtra($article);

                $articlecategories[0]['pagination'] = $pagination;
                $articlecategories[0]['article'] = $article;

                $this->template()->assign(array('articlecategories' => $articlecategories));

            } else {
                $arrSearch = $oSearch->getConditions();
                $title_search = '';
                if (!empty($arrSearch)) {
                    $title_search = $arrSearch[0];
                }
                // build WHEN

                $articlecategories = array();
                $articlecategories[0] = Phpfox::getService('gettingstarted.articlecategory')->getArticleCategoryforEdit($articlecategory['article_category_id']);

                $iLimit = 10;
                $aSettings = phpfox::getService("gettingstarted.settings")->getSettings(0);
                if (!isset($aSettings['number_of_article_category'])) {

                    $iLimit = 5;

                } else {
                    $iLimit = $aSettings['number_of_article_category'];
                }

                // paging
                $iLimit = $this->request()->getInt('show') ? $this->request()->getInt('show') : $iLimit;


                $pagination = 0;
                $dsArticle = Phpfox::getService("gettingstarted.articlecategory")
                    ->searchdsArticleByCategoryId($title_search, $articlecategory['article_category_id'], $arrSearch);

                if (count($dsArticle) > $iLimit) {
                    $pagination = 1;
                }
                $iCnt = count($dsArticle);

                $iPage = $this->request()->getInt('page') ? $this->request()->getInt('page') : 1;
                Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $iCnt, 'paging_mode' => $this->search()->browse()->getPagingMode()));
                $article = Phpfox::getService("gettingstarted.articlecategory")->searchArticleByCategoryId($title_search, $articlecategory['article_category_id'], $iLimit, $iPage, $arrSearch);
                phpfox::getService('gettingstarted.articlecategory')->getExtra($article);

                $articlecategories[0]['pagination'] = $pagination;
                $articlecategories[0]['article'] = $article;

                $this->template()->assign(array(
                    'articlecategories' => $articlecategories,
                    'iCnt' => $iCnt,
                    'title_search' => $title_search,

                ));
            }

            $this->template()->assign(array(
                'bIsSearch' => $bIsSearch,
            ))
                ->setHeader(array(
                    'gettingstarted.js' => 'module_gettingstarted',
                    'pager.css' => 'style_css',
                ));

            // END Category page
        }
        $current_page = $this->request()->getInt("page");
        $this->template()->assign(array(
            'current_page' => $current_page,
            'flag' => $flag,
            'bIsCategory' => $bIsCategory,
            'iPage' => $iPage
        ));
    }

    public function showArticlebyCategory()
    {

    }
}

?>
