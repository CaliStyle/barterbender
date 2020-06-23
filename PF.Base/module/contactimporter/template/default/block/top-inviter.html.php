<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author          younetco
 * @package          Module_Contactimporter
 * @version         
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<ul class="action">
{foreach from=$topinviter item=inviter}
    {if $inviter.user_name}
        <li><a href="{url link=$inviter.user_name}">{$inviter.full_name|convert|clean} ({$inviter.number_invitation})</a></li>
    {/if}
{/foreach}
</ul>