<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author           Raymond_Benc
 * @package          Phpfox
 * @version          $Id: template-logo.html.php 7042 2014-01-14 12:42:41Z Fern $
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="site-logo">
    <a href="{url link=''}" class="site-logo-link">
        <span class="site-logo-icon"><i{if isset($logo)} style="background-image:url({$logo})"{/if}></i></span>
        {if (isset($site_name))}
        <span class="site-logo-name" style="display:none;">{$site_name}</span>
        {/if}
    </a>
</div>
{if !Phpfox::isUser() && Phpfox::getParam('user.allow_user_registration') && !Phpfox::getParam('user.invite_only_community')}
    <a href="{url link='user.register'}" class="ync-fbclone-sign-up {if Phpfox::canOpenPopup('user.register')}popup{else}no_ajax{/if}">
        <button class="btn btn-success btn-sm">{_p var='sign_up_button'}</button>
    </a>
{/if}