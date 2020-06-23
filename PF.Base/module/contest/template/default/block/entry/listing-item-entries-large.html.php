<div class="yc large_item ycs_item_list image_hover_holder">
    {if isset($showaction) && $showaction==true && (!isset($is_hidden_action) || $is_hidden_action!=1)}
        <div class="moderation_row" style="position: absolute;top: 0;">
            <label class="item-checkbox">
                <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aEntry.entry_id}" id="check{$aEntry.entry_id}" />
                <i class="ico ico-square-o"></i>
            </label>
        </div>
    {/if}
	<div class="large_item_image ele_relative">
        {if $aEntry.image_path}
        <div class="contest_thumb" onclick="window.location.href='{permalink module='contest' id=$aEntry.contest_id title=$aEntry.contest_name}entry_{$aEntry.entry_id}/'" style="background-image:url('{if $aEntry.type == 2}
                {img return_url=true server_id=$aEntry.server_id path='core.url_pic' file=$aEntry.image_path suffix='_500'}
            {elseif $aEntry.type == 3}
                {img return_url=true server_id=$aEntry.server_id path='core.url_pic' file=$aEntry.image_path suffix='_480'}
            {/if}')"></div>
        {else}
        <div class="contest_thumb" onclick="window.location.href='{permalink module='contest' id=$aEntry.contest_id title=$aEntry.contest_name}entry_{$aEntry.entry_id}/'" style="background-image:url('{$sUrlNoImagePhoto}')"></div>
        {/if}
            {if $aEntry.status_entry==0}
            <span class="small_pending draft">{phrase var='contest.pending'}</span>
            {elseif $aEntry.status_entry==2}
            <span class="small_pending denied">{phrase var='contest.denied'}</span>
            {elseif $aEntry.status_entry==3}
            <span class="small_pending draft">{phrase var='contest.draft'}</span>
            {/if}


            {if isset($sView) && $sView == 'winning'}
            <div class="entries_win">
              {$aEntry.rank}
            </div>
            {/if}

            {if isset($sView) && $sView=='pending_entries' && $aEntry.have_action_on_entry}
            <a href="#" class="image_hover_menu_link">{phrase var='contest.link'}</a>
            <div class="image_hover_menu">
              <ul>
                {template file='contest.block.entry.action-link'}
              </ul>
            </div>
            {/if}
        </div>
        	<ul class="large_item_action">
        		<li class="ycvotes">
                 <p>Votes</p>
        			<strong>{$aEntry.total_vote}</strong>
        		</li>
        		<li class="ycviews">
                 <p>Views</p>
        			<strong>{$aEntry.total_view}</strong>
        		</li>
        	</ul>
           <div class="large_item_info large_hover" onclick="window.location.href='{permalink module='contest' id=$aEntry.contest_id title=$aEntry.contest_name}entry_{$aEntry.entry_id}/'">
                 <a class="small_title" href="{permalink module='contest' id=$aEntry.contest_id title=$aEntry.contest_name}entry_{$aEntry.entry_id}/" title="{$aEntry.title}">
                    {$aEntry.title|clean}
                 </a>
                 <div class="extra_info">
                    {if (isset($bInHomepage) && $bInHomepage) || (isset($bIsEntryIndex) && $bIsEntryIndex)}
                        <p>{phrase var='contest.in'} <a href="{permalink module='contest' id=$aEntry.contest_id title=$aEntry.contest_name}" title="{$aEntry.contest_name}">{$aEntry.contest_name|clean}</a></p>
                        {/if}
                    <p>{phrase var='contest.by'} {$aEntry|user}</p>
                        {if isset($sView) && $sView == 'winning'}
                    <p title="{$aEntry.award|clean}">{$aEntry.award|clean|shorten:25:'...'|split:25}</p>
                        {/if}
                 </div>
           </div>
</div>