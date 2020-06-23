<?php

if (Phpfox::isModule('foxfavorite') && Phpfox::isUser() && (Phpfox::getUserBy('view_id') == 0))
{
    $foxfavoriteModule = (defined('PHPFOX_IS_USER_PROFILE')) ? 'profile' : Phpfox::getLib('module')->getModuleName();

    $iItemId = Phpfox::getService('foxfavorite')->getItemId($foxfavoriteModule);
    $bIsViewItem = Phpfox::getService('foxfavorite')->isViewItem($foxfavoriteModule);
    $bIsAvailModule = Phpfox::getService('foxfavorite')->isAvailModule($foxfavoriteModule);
    $bIsFunctionedModule = Phpfox::getService('foxfavorite')->isFunctionedModule($foxfavoriteModule);
    $bIsAlreadyFavorite = Phpfox::getService('foxfavorite')->isAlreadyFavorite($foxfavoriteModule, $iItemId);

    if ($bIsViewItem && $bIsAvailModule   && !$bIsFunctionedModule && !Phpfox::isAdminPanel() && !defined('PHPFOX_IS_USER_PROFILE'))
    {
        $html ='<div id=\"yn_section_menu\"  style=\"display:none;\">';

        $html .='<a  class=\"favor\" href=\"#\" onclick=\"$(\'#js_favorite_link_unlike_'.$iItemId.'\').show(); $(\'#js_favorite_link_like_'.$iItemId.'\').hide(); $.ajaxCall(\'foxfavorite.addFavorite\', \'type='.$foxfavoriteModule.'&amp;id='.$iItemId.'\', \'GET\'); return false;\"  id=\"js_favorite_link_like_'. $iItemId.'\" title=\"'._p('foxfavorite.add_to_your_favorites').'\"';
        if ($bIsAlreadyFavorite) {
            $html .= ' style=\"display:none;\">';
        }
        else{
            $html .= '>';
        }
        $html .= _p('foxfavorite.favorite').'</a>';

        $html .= '<a class=\"unfavor\" title=\"'._p('foxfavorite.remove_from_your_favorite').'\" href=\"#\" onclick=\"$(\'#js_favorite_link_like_'.$iItemId.'\').show(); $(\'#js_favorite_link_unlike_'.$iItemId.'\').hide(); $.ajaxCall(\'foxfavorite.deleteFavorite\', \'type='. $foxfavoriteModule.'&amp;id='.$iItemId.'\', \'GET\'); return false;\" id=\"js_favorite_link_unlike_'. $iItemId.'\"';
        if (!$bIsAlreadyFavorite) {
            $html .= ' style=\"display:none;\">';
        }
        else{
            $html .='>';
        }
        $html.= _p('foxfavorite.unfavorite').'</a></div>';
        $this->template()->setHeader('<script type="text/javascript">

        	var gan_nut_unfavorite = true;
            $Behavior.onCreateFavoriteButton = function() {
                    if(!$(\'body\').find(\'#yn_section_menu\').length) { $(\'body\').append("'.$html.'"); }
            		if ($(\'#page_pages_view\').length && !$(\'#ynfavorite\').length){

            		if($(\'#section_menu\').size() == 0 && $(\'#yn_section_menu\').html() != null)
	                {
	                    $bt_favor = \'<div id="ynfavorite"   class="yn_sectionmenu">\' + $(\'#yn_section_menu\').first().html() + \'</div>\';
	                    $(\'.profile-actions .profile-action-block:first\').prepend($bt_favor); //cosmic theme
	                    $(\'#section_menu\').show();
	                }
	                else
	                {
	                    $bt_favor = $(\'.yn_page_favorite\').first();
	                    $bt_unfavor = $(\'.unfavor\');
	                    if (gan_nut_unfavorite){
	                    	$(\'#ynfavorite\').prepend($bt_unfavor);
	                    }
	                }
	                $Behavior.inlinePopup();

                    }

    	  			$(\'#yn_section_menu\').html(\'\');
            };
        </script>');
    }
}
?>