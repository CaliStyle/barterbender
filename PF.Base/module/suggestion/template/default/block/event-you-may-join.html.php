<div class="yn_suggestion_list_item">
<br><h3>{phrase var='suggestion.event_you_may_join'}</h3><br>
<form action="" method="post" name="frmIncoming" id="frmIncoming">
	
    <input type="hidden" name="iUserId" id="iUserId" value="<?= Phpfox::getUserId(); ?>" />
    <div id="sKey" style="display:none;">{$sKey}</div>
    {if count($aRows)>0}   
	    {foreach from=$aRows key=iKey item=aItem}
	    	  {if count($aItem)>0}
	    
		    	<div style="overflow: hidden;">
		    	<div id="{$iKey}" >
		    	{foreach from=$aItem item=aRow}
		    
				    <div id="ynsuggestion_item_{$aRow.suggestion_id}" class="ynsuggestion_item">
				    	
				    	<span class="ajaxLoader hide" style="position: absolute; right:120px;"><img src="{$sFullUrl}theme/frontend/default/style/default/image/ajax/add.gif" /></span>

				       	<div class="suggestion_image">    		
				    		{$aRow.avatar}
				    	</div> 
				    	<div class="suggestion_info">    		
				    		<div class="user_tooltip_info_user" itemprop="name">    			
				    			{$aRow.info}
				    		</div>
				    		<div class="suggestion_description">
				    			{$aRow.create}
				    		</div>
				    		<div class="user_browse_description">
				    			{$aRow.suggest}
				    		</div>
				    		<div class="user_browse_description">
				    			{$aRow.message}
				    		</div>
				    	</div>   
				    	
				    	<div class="suggestion_action">
					        {if (isset($aRow.ignore)) }
					        	<button type="button" class="button btn btn-default btn-sm" style="margin-left: 10px; float: right;" onclick="doProcess(this, 2, {$aRow.friend_user_id}, {$aRow.friend_friend_user_id}, 'process_{$aRow.suggestion_id}','{$aRow.module_id}','{$aRow.url}'); return false;">{$aRow.ignore}</button>
					        {/if}
					        
					        {if (isset($aRow.accept)) }
					        	<button type="button" class="button btn btn-primary btn-sm" style="margin-left: 10px; float: right;" onclick="doProcess(this, 1, {$aRow.friend_user_id}, {$aRow.friend_friend_user_id}, 'process_{$aRow.suggestion_id}','{$aRow.module_id}', '{$aRow.url}'); return false;">{$aRow.accept}</button>
					       	{/if}
					       	
					       	{if (isset($aRow.delete)) }
					        	<button type="button" class="button btn btn-danger btn-sm" style="margin-left: 10px; float: right;" value="" onclick="doProcessDelete(this, {$aRow.suggestion_id}); return false;">{$aRow.delete}</button>
					       	{/if}
					    </div>
						
				    </div>
		    	{/foreach}
		    	</div>
		    	<div id='suggestion_view_more_{$iKey}' class="t_center">
					<a  class='ynsug_view_more no_ajax_link' style='cursor:pointer;'
					onClick="$('#suggestion_view_more_{$iKey}').hide(); $('#view_more_loader').show();	$.ajaxCall('suggestion.loadObjectViewMore','type={$iKey}&iPage_{$iKey}='+$('#iPage_{$iKey}').val());">{phrase var='suggestion.view_more'}
					</a>
				</div>
				<input type="hidden" id="iPage_{$iKey}" value="1">
				</div>
			{else}
				{if ($sView != 'my' && $sView != 'friends') }
    			<div class="message">{phrase var='suggestion.no_new_suggestion_at_this_time'}</div>
    			{/if}
		    {/if}
	    {/foreach}
    {/if}

</form>
</div>
