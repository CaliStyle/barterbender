<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FoxFeedsPro
 * @version          3.01
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<?php

class Suggestion_Component_Block_Advanced_Menu extends Phpfox_Component
{
    public function process()
    {
        Phpfox::getService('suggestion')->getTotalIncomingSuggestion(Phpfox::getUserId());
        $iFriends = Phpfox::getService('suggestion')->getTotalFriendsSuggestion();
        $iMys = Phpfox::getService('suggestion')->getTotalMySuggestion();
        $countObj = Phpfox::getService('suggestion')->getAllCoutObj();

        $htmlLi_friend = '';
        $htmlLi_my = '';
        $view = $this->request()->get('view');

        $sSupportModule = Phpfox::getUserParam('suggestion.support_module');
        $aMainMenus = $this->template()->getMenu('main');
        $aMainExplore = $this->template()->getMenu('explore');
        $aMainMenus = array_merge($aMainMenus, $aMainExplore);

        if ($sSupportModule != '') {
            $sSupportModule = explode(',', $sSupportModule);
            foreach ($sSupportModule as $sModule) {
                if (Phpfox::isModule($sModule)) {
                    $active = '';
                    $view_Module = str_replace("friends", "", $view);
                    $view_Module = str_replace("my", "", $view_Module);
                    if ($view_Module == $sModule) {
                        $active = 'active';
                    }
                    $title = ($sModule == 'fevent') ? 'Advanced Event' : ucfirst($sModule);
                    foreach ($aMainMenus as $menu) {
                        if ($sModule == $menu['module']) {
                            $title = _p($sModule . '.' . $menu['var_name']);
                            break;
                        }
                    }
                    $htmlLi_friend .= '<li class="' . $active . '"><a href="%url' . $sModule . '" id="%s' . $sModule . '" class="ynsug_cat_link">' . ucfirst($title) . (($countObj['friends'][$sModule])?'<span>' . $countObj['friends'][$sModule] . '</span>':'').'</a></li>';
                    $htmlLi_my .= '<li class="' . $active . '"><a href="%url' . $sModule . '" id="%s' . $sModule . '" class="ynsug_cat_link">' . ucfirst($title) . (($countObj['my'][$sModule])?'<span>' . $countObj['my'][$sModule] . '</span>':'').'</a></li>';
                }
            }
        }
        $view = $this->request()->get('view');
        $open = '';
        $active = '';
        if(strpos($view, 'friends') !== false) {
            $active = 'active';
            $open = 'open';
        }
        if($view == 'friends')
        {
            $open = '';
        }
        $html = '<ul class="ulLevelMenu" id="suggestion_menu">';
        //add menu of all suggestion
        $temp = '';
        $url = $this->url()->makeUrl('suggestion.view_friends');
        $url = substr($url, 0, strlen($url) - 1);
        if ($htmlLi_friend != '') {
            if (strpos($view, 'my') !== false) {
                $htmlLi_friend = str_replace("class=\"active\"", "", $htmlLi_friend);
            }
            $temp = '<ul id="menu_view_friends">' . str_replace('%url', $url, str_replace('%s', 'view_friends', $htmlLi_friend)) . '</ul>';
        }
        $html .= '<li id="view_friends" class="' . $open . ' ' . $active . '"><span class="menu_option" onclick="showSub(\'menu_view_friends\');"></span><a href="' . $this->url()->makeUrl('suggestion.view_friends') . '" class="ynsug_cat_link">' . _p('suggestion.friends_suggestions') . '<span>' . $iFriends . '</span></a>' . $temp . '</li>';

        //add menu of my suggestion
        $temp = '';
        $url = $this->url()->makeUrl('suggestion.view_my');
        $url = substr($url, 0, strlen($url) - 1);
        if ($htmlLi_my != '') {
            if (strpos($view, 'friends') !== false) {
                $htmlLi_my = str_replace("class=\"active\"", "", $htmlLi_my);
            }
            $temp = '<ul id="menu_view_my">' . str_replace('%url', $url, str_replace('%s', 'view_my', $htmlLi_my)) . '</ul>';
        }

        $open = '';
        $active = '';
        if(strpos($view, 'my') !== false) {
            $active = 'active';
            $open = 'open';
        }
        if($view == 'my')
        {
            $open = '';
        }
        $html .= '<li id="view_my" class="' . $open . ' ' . $active . '"><span class="menu_option" onclick="showSub(\'menu_view_my\');"></span><a href="' . $this->url()->makeUrl('suggestion.view_my') . '" class="ynsug_cat_link">' . _p('suggestion.my_suggestions') . '<span>' . $iMys . '</span></a>' . $temp . '</li>';
        $html .= '</ul>';
        $this->template()->assign(array(
            'html' => $html,
            'core_url' => Phpfox::getParam('core.path')
        ));
        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('foxfeedspro.component_block_category_clean')) ? eval($sPlugin) : false);
    }

}

?>