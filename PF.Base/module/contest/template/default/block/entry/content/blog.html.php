{if !empty($imagePath) || $isAdvBlog}
<div class="yncontest_preview_ynblog">
    <div class="ynadvblog_avatar">
        <a href="" class="ynadvblog_cover_inner item_image full" style="background-image: url(
                {if !empty($imagePath)}
                    {$imagePath}
                {else}
                    {$defaultAdvBlogPhoto}
                {/if}
            )">
        </a>
    </div>
</div>
{/if}

<div class="item_view_content">
    {$aBlogEntry.blog_content_parsed}
</div>

{if isset($aBlogEntry) && $aBlogEntry.total_attachment}
	{if $bIsPreview}
		 {module name='contest.entry.content.attachment.list' sType=blog iItemId=$aBlogEntry.blog_id}
	{else}
	    {module name='contest.entry.content.attachment.list' sType=contest_entry_blog iItemId=$aBlogEntry.entry_id}
	{/if}
{/if}

