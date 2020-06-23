{if isset($bSpecialMenu) && $bSpecialMenu == true}
    {template file='ultimatevideo.block.specialmenu'}
{/if}
{if !count($aItems)}
    <div class="extra_info">
        {phrase var='ultimatevideo.no_video_founds'}
    </div>
{else}
    {if !PHPFOX_IS_AJAX}
        <div class="ultimatevideo-grid clearfix">
    {/if}
    {foreach from=$aItems name=video item=aItem}
        {template file='ultimatevideo.block.entry'}
    {/foreach}
    {pager}

    {if !PHPFOX_IS_AJAX && (Phpfox::getUserParam('blog.can_approve_blogs') || Phpfox::getUserParam('blog.delete_user_blog'))}
        {moderation}
    {/if}
    {if !PHPFOX_IS_AJAX}
        </div>
    {/if}
{/if}