<div class="yndirectory-filter-form">
	<form id="js_ynd_filterinbusiness_form" 
		data-ajax-action="{$ajax_action}"  
		data-result-div-id="{$result_div_id}" 
		data-custom-event="{$custom_event}"
		data-is-prevent-submit="{$is_prevent_submit}"
	>		
		<div class="yndirectory-filter-form-left">
			<div class="table_left">
			</div>
			<div class="yndirectory-filter-search-member input-group">
				<input class="form-control" type="text" name="val[keyword]" placeholder="{$sPlaceholderKeyword}" />
				<div class="input-group-addon">
                    <button type="submit" class="btn" aria-hidden="true">
                        <span class="ico ico-search-o"></span>
                    </button>
                </div>
			</div>
		</div>

		<div class="yndirectory-filter-form-right">
			{if $hidden_type == 'followers' || $hidden_type == 'members' }
				<div class=""> 
					<div class="table_left">
						{_p var='sort'}:
					</div>
					<div class="">
						<select class="form-control" name="val[filterinbusiness_sort]" id="">
							<option value="newest" >{_p var='newest'}</option>
							<option value="oldest" >{_p var='oldest'}</option>
							<option value="a_z" >{_p var='a_z'}</option>
							<option value="z_a" >{_p var='z_a'}</option>
						</select>	
					</div>
				</div>
			{else}
				<div class=""> 
					<div class="table_left">
						{_p var='sort'}:
					</div>
					<div class="">
						<select class="form-control" name="val[filterinbusiness_sort]" id="">
							<option value="latest" >{_p var='latest'}</option>
							<option value="most_viewed" >{_p var='most_viewed'}</option>
							<option value="most_liked" >{_p var='most_liked'}</option>
							<option value="most_discussed" >{_p var='most_discussed'}</option>
						</select>	
					</div>
				</div>
			{/if}


			<div class=""> 
				<div class="table_left">
					{_p var='show'}:
				</div>
				<div class="">
					<select class="form-control" name="val[filterinbusiness_show]" id="">
						<option value="5" >{_p var='per_page' total=5}</option>
						<option value="10" > {_p var='per_page' total=10}</option>
						<option value="15" >{_p var='per_page' total=15}</option>
					</select>	
				</div>
			</div>

			<div class=""> 
				<div class="table_left">
					{_p var='when'}:
				</div>
				<div class="">
					<select class="form-control" name="val[filterinbusiness_when]" id="">
						<option value="all_time" >{_p var='all_time'}</option>
						<option value="this_month" >{_p var='this_month'}</option>
						<option value="this_week" >{_p var='this_week'}</option>
						<option value="today" >{_p var='today'}</option>
					</select>	
				</div>
			</div>			
		</div>
		
		<div>
			<input type="hidden" name="val[hidden_type]" value="{$hidden_type}"/>			
			<input type="hidden" name="val[hidden_businessid]" value="{$hidden_businessid}"/>			
			<input type="hidden" name="val[hidden_select]" value="{$hidden_select}"/>			
		</div>

	</form>
	<div class="clear"></div>	

	<script type="text/javascript">
		$Behavior.yndInitFilterInBusinessForm = function() {l} 
			$("#js_ynd_filterinbusiness_form").ajaxForm();
		{r}
	</script>

</div>