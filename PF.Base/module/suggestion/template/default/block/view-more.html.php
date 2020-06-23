{if count($aRows)>0}   
    {foreach from=$aRows key=iKey item=aItem}
    	  {if count($aItem)>0}    
	    	{foreach from=$aItem item=aRow}	    
			    <div id="ynsuggestion_item_{$aRow.suggestion_id}" class="ynsuggestion_item clearfix" >			    	
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
			   		{if (isset($aRow.accept)) }
			        	<button type="button" class="button btn btn-primary btn-sm"   onclick="doProcess(this, 1, {$aRow.friend_user_id}, {$aRow.friend_friend_user_id}, 'process_{$aRow.suggestion_id}','{$aRow.module_id}', '{$aRow.url}'); return false;">{$aRow.accept}</button>
			       	{/if}
		        	{if (isset($aRow.ignore)) }
			        	<button type="button" class="button btn btn-default btn-sm"   onclick="doProcess(this, 2, {$aRow.friend_user_id}, {$aRow.friend_friend_user_id}, 'process_{$aRow.suggestion_id}','{$aRow.module_id}','{$aRow.url}'); return false;">{$aRow.ignore}</button>
			        {/if}
			       	{if (isset($aRow.delete)) }
			        	<button type="button" class="button btn btn-danger btn-sm"  onclick="doProcessDelete(this, {$aRow.suggestion_id}); return false;">{$aRow.delete}</button>
			       	{/if}


                    {if (isset($aRow.reminder))}
                        <button type="button" class="button btn btn-success btn-sm" onclick="doReminder({$aRow.item_id},'{$aRow.module_id}','{$aRow.url}','{$aRow.title}',{$aRow.friend_user_id}); return false;">{$aRow.reminder}</button>
                    {/if}

                    {if (isset($aRow.delete_reminder))}
                        <button type="button" class="button btn btn-danger btn-sm"  onclick="deleteReminder(this, {$aRow.reminder_id}); return false;">{$aRow.delete_reminder}s</button>
                    {/if}
                            
			       	</div>
			
			    </div>
	    	{/foreach}
	    {/if}
    {/foreach}       
{else}
	 {if $iPage < 1}
	{phrase var='suggestion.no_new_suggestion_at_this_time'}
	{/if}	 
{/if}
<script type="text/javascript" >suggestion_viewmorephoto();</script>
