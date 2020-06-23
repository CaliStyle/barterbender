<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author        Raymond Benc
 * @package        Phpfox_Component
 * @version        $Id: profile.class.php 1245 2009-11-02 16:10:29Z Raymond_Benc $
 */
class FoxFavorite_Component_Controller_Profile extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $bIsProfile = false;
        if (defined('PHPFOX_IS_USER_PROFILE') && ($sLegacyTitle = $this->request()->get('req3')) && !empty($sLegacyTitle)) {
            Phpfox::getService('core')->getLegacyItem(array(
                    'field' => array('favorite_id', 'title'),
                    'table' => 'foxfavorite',
                    'redirect' => 'foxfavorite',
                    'title' => $sLegacyTitle
                )
            );
        }

        //var_dump($_SERVER);
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            $bIsProfile = true;
        }
        $aUser = $this->getParam('aUser');

        if (!Phpfox::getService('user.privacy')->hasAccess($aUser['user_id'], 'favorite.view_favorite')) {
            return Phpfox_Error::display('<div class="extra_info">' . _p('favorite.full_name_has_closed_their_favorites_section', array('user_link' => $this->url()->makeUrl($aUser['user_name']), 'full_name' => Phpfox::getLib('parse.output')->clean($aUser['full_name']))) . '</div>');
        }

        $aSearchFields = array(
            'type' => 'foxfavorite',
            'field' => 'f.favorite_id',
            'search_tool' => array(
                'table_alias' => 'f',
                'search' => array(
                    'action' => ($bIsProfile === true ? $this->url()->makeUrl($aUser['user_name'], array('foxfavorite', 'view' => $this->request()->get('view'))) : $this->url()->makeUrl('foxfavorite', array('view' => $this->request()->get('view')))),
                    'default_value' => _p('foxfavorite.search_favorites'),
                    'name' => 'search',
                    'field' => array('f.title')
                ),
                'sort' => array(
                    'latest' => array('favorite.time_stamp', _p('foxfavorite.latest'))
                ),
                'show' => array(15, 20, 30)
            )
        );
        $aSearchFields['search_tool']['no_filters'] = [_p('sort')];
        $this->search()->set($aSearchFields);
        $aCond = '';
        $sView = $this->request()->get('view');
        if (isset($sView) && $sView) {
            $aCond .= ' AND f.type_id ="' . $sView . '"';
        }
        $aCondition = $this->search()->getConditions();
        $aCond .= (isset($aCondition[0])) ? $aCondition[0] : '';
        $iPage = $this->search()->getPage();
        $iLimit = $this->search()->getDisplay();
        if (isset($sView) && $sView) {
            $iCnt = phpfox::getService('foxfavorite')->getCount($sView, $aUser['user_id'], $aCond);
            list($iOwnerUserId, $aFavorites) = Phpfox::getService('foxfavorite')->getSearchFavorite($aUser['user_id'], $aCond, 'fs.title ASC, f.time_stamp DESC', $iPage, $iLimit, $iCnt);
            Phpfox::getLib('pager')->set(array('page' => $this->search()->getPage(), 'size' => $this->search()->getDisplay(), 'count' => $iCnt));
        } else {
            list($iOwnerUserId, $aFavorites) = Phpfox::getService('foxfavorite')->get($aUser, $aCond, 'fs.title ASC, f.time_stamp DESC');

        }

        $this->template()->setHeader('cache', array(
                'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                'quick_edit.js' => 'static_script',
                'comment.css' => 'style_css',
                'pager.css' => 'style_css',
                'feed.js' => 'module_feed'
            )
        )
            ->setTitle(_p('full_name_s_favorites', array('full_name' => $aUser['full_name'])))
            ->setBreadcrumb(_p('full_name_s_favorites', array('full_name' => $aUser['full_name'])), $this->url()->makeUrl($aUser['user_name'], 'foxfavorite'))
            ->assign(array(
                    'iPage' => $iPage,
                    'aFavorites' => $aFavorites,
                    'iFavoriteUserId' => $iOwnerUserId,
                    'sView' => (isset($sView)) ? $sView : false
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('foxfavorite.component_controller_profile_clean')) ? eval($sPlugin) : false);
    }
}

?>