<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 20/01/2017
 * Time: 14:22
 */
defined('PHPFOX') or exit('NO DICE!');
?>

{if !count($aItems)}
    {if !PHPFOX_IS_AJAX}
        <div class="extra_info">
            {_p var='No bloggers found'}
        </div>
    {/if}
{else}
    {if !PHPFOX_IS_AJAX}
    <ul class="ynadvblog_my_following_bloggers">
        {/if}
        {foreach from=$aItems item=aCurrentAuthor}
            {assign var=aLatestPost value=$aCurrentAuthor.aLatestPost}
            <li class="ynadvblog_my_following_bloggers_inner" id="js_ynblog_my_following_blogger_item_{$aCurrentAuthor.user_id}">
                {template file='ynblog.block.author'}
            </li>
        {/foreach}
        {pager}

        {if !PHPFOX_IS_AJAX}
    </ul>
    {/if}
{/if}
