
<div class="p-block yndirectory-blog-list">
	{if count($aBlogs) < 1}
		<div class="help-block">
			{phrase var='no_item_s_found'}.
		</div>
	{/if}
    {if $sType == 'ynblog'}
    <div class="mb-2">
        <div class="p-listing-container p-advblog-listing-container col-4 casual-col-3 p-mode-view" data-mode-view="list">
        {foreach from=$aBlogs name=blog item=aItem}
            {template file='ynblog.block.entry'}
        {/foreach}
        </div>
    </div>
    {else}
        <div class="yndirectory-content-row">
        {foreach from=$aBlogs name=blog item=aItem}
            <div class="yndirectory-blog-item">
                <div class="yndirectory-content-row-image">
                    {img user=$aItem suffix='_50_square' max_width=50 max_height=50}
                </div>
                <div class="yndirectory-content-row-info">
                    <div class="yndirectory-item-title"><a href="{permalink module='blog' id=$aItem.blog_id title=$aItem.title}" id="js_blog_edit_inner_title{$aItem.blog_id}" class="link ajax_link" itemprop="url">{$aItem.title|clean|shorten:55:'...'|split:20}</a></div>
                    <div class="yndirectory-item-info">{phrase var='blog.by_full_name' full_name=$aItem|user:'':'':50:'':'author'}</div>
                    <div class="yndirectory-blog-extra">
                        <div>
                            <div>
                                {if isset($bBlogView)}
                                    {$aItem.text|parse|highlight:'search'|split:55}
                                {else}
                                    <div class="extra_info">
                                        {$aItem.text|strip_tags|highlight:'search'|split:55|shorten:$iShorten'...'}
                                    </div>
                            {/if}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        {/foreach}
        </div>
    {/if}

	<div class="clear"></div>
	{module name='directory.paging'}	
</div>

{if PHPFOX_IS_AJAX}
{literal}
<script type="text/javascript">
	$Core.loadInit();
</script>
{/literal}
{/if}

