<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * @copyright		YouNetCo Company
 * @author  		MinhNTK
 */
class FoxFavorite_Component_Block_Favorite_Page extends Phpfox_Component
{

    public function process()
    {        
        $sModule = 'pages';
        $iItemId = (defined('PHPFOX_IS_USER_PROFILE')) ? phpfox::getLib('request')->get('req1') : phpfox::getLib('request')->getInt('req2');
        $aPage = phpfox::getService('pages')->getForView($iItemId);
        $iViewId = phpfox::getUserBy('view_id');
        if (!Phpfox::getService('foxfavorite')->isAvailModule($sModule) || empty($aPage) || $iViewId != 0)
        {
            return false;
        }

        $bIsAlreadyFavorite = phpfox::getService('foxfavorite')->isAlreadyFavorite($sModule, $aPage['page_id']);
        $favor_img = phpfox::getParam('core.path') . 'module/foxfavorite/static/image/favorite.png';
        $unfavor_img = phpfox::getParam('core.path') . 'module/foxfavorite/static/image/unfavorite.png';
        $html ='<div id="yn_section_menu"  style="display:none;">';

        $html .='<a  class="favor btn btn-default btn-round" href="#" onclick="$(\'#js_favorite_link_unlike_'.$iItemId.'\').show(); $(\'#js_favorite_link_like_'.$iItemId.'\').attr(\'style\', \'display: none !important\'); $.ajaxCall(\'foxfavorite.addFavorite\', \'type='.$sModule.'&amp;id='.$iItemId.'\', \'GET\'); return false;"  id="js_favorite_link_like_'. $iItemId.'" title="'._p('foxfavorite.add_to_your_favorites').'"';
        if ($bIsAlreadyFavorite) {
            $html .= ' style="display:none !important;">';
        }
        else{
            $html .= '>';
        }
        $html .= _p('foxfavorite.favorite').'</a>';

        $html .= '<a class="unfavor btn btn-default btn-round" title="'._p('foxfavorite.remove_from_your_favorite').'" href="#" onclick="$(\'#js_favorite_link_like_'.$iItemId.'\').show(); $(\'#js_favorite_link_unlike_'.$iItemId.'\').attr(\'style\', \'display: none !important\'); $.ajaxCall(\'foxfavorite.deleteFavorite\', \'type='. $sModule.'&amp;id='.$iItemId.'\', \'GET\'); return false;" id="js_favorite_link_unlike_'. $iItemId.'"';
        if (!$bIsAlreadyFavorite) {
            $html .= ' style="display:none !important;">';
        }
        else{
            $html .='>';
        }
        $html.= _p('foxfavorite.unfavorite').'</a></div>';
        $this->template()->assign(array(
            'favor_img' => $favor_img,
            'unfavor_img' => $unfavor_img,
            'bIsAlreadyFavorite' => $bIsAlreadyFavorite,
            'sModule' => $sModule,
            'iItemId' => $aPage['page_id'],
            'sHtml' => $html
        ));

        return 'block';         
    }

}

?>