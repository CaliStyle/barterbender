<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/7/16
 * Time: 3:58 PM
 */
?>
<div class="sub_section_menu header_display">
    <ul class="action">
        {foreach from=$aProfileMenu key=iKey item=aItemMenu}
        <li class="{if $aItemMenu.sMenu == $sDetailPage}active{/if} {$aItemMenu.sClass}"  >
            <a href="{url link=$aItemMenu.sLink}">
                {$aItemMenu.sPhrase|convert}
            </a>
        </li>
        {/foreach}
    </ul>
</div>

