<?php
/**
 * [Yns_ContactImporter]
 * 
 * @copyright        [Younetco]

 * @package         Phpfox
 * @version         1.0
 */
 
defined('PHPFOX') or exit('NO DICE!'); 
?>
 { if isset($errors) and  count($errors)== 0}
     <h1>{_p var='invite_friends'}</h1>
            <div>{_p var='send_invitation_to_your_friends_successfully_1' contactimporter_link=$contactimporter_link homepage=$homepage}</div>
{else}
    <div>{_p var='there_were_errors_when_you_send_the_invitation'}</div>
    {foreach from=$errors item=er}
        <div style="color:red;font-weight:700">{$er}</div>
    {/foreach}
{/if}

   
