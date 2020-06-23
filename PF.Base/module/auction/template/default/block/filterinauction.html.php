<div class="ynauction-filter-form">
	<form id="js_ynauction_filterinauction_form" 
		data-ajax-action="{$ajax_action}"  
		data-result-div-id="{$result_div_id}" 
		data-custom-event="{$custom_event}"
		data-is-prevent-submit="{$is_prevent_submit}"
	>
        <div class="ynauction-filter-form-left">
            <div class="table_left"></div>
            <div class="table_right">
                <input type="text" class="txt_input" name="val[keyword]" placeholder="{$sPlaceholderKeyword}" />
            </div>
        </div>
        <div class="ynauction-filter-form-right">
            {if $hidden_type == 'followers' || $hidden_type == 'members' }
            <div>
                <div class="table_left">
                    {_p var='sort'}:
                </div>
                <div class="table_right">
                    <select class=" " name="val[filterinauction_sort]" id="">
                        <option value="newest" >{_p var='newest'}</option>
                        <option value="oldest" >{_p var='oldest'}</option>
                        <option value="a_z" >{_p var='a_z'}</option>
                        <option value="z_a" >{_p var='z_a'}</option>
                    </select>
                </div>
            </div>
            {else}
            <div>
                <div class="table_left">
                    {_p var='sort'}:
                </div>
                <div class="table_right">
                    <select class=" " name="val[filterinauction_sort]" id="">
                        <option value="latest" >{_p var='latest'}</option>
                        <option value="most_viewed" >{_p var='most_viewed'}</option>
                        <option value="most_liked" >{_p var='most_liked'}</option>
                        <option value="most_discussed" >{_p var='most_discussed'}</option>
                    </select>
                </div>
            </div>
            {/if}

            <div>
                <div class="table_left">
                    {_p var='show'}:
                </div>
                <div class="table_right">
                    <select class=" " name="val[filterinauction_show]" id="">
                        <option value="5" >{_p var='per_page' total=5}</option>
                        <option value="10" >{_p var='per_page' total=10}</option>
                        <option value="15" >{_p var='per_page' total=15}</option>
                    </select>
                </div>
            </div>

            <div>
                <div class="table_left">
                    {_p var='when'}:
                </div>
                <div class="table_right">
                    <select class=" " name="val[filterinauction_when]" id="">
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
            <input type="hidden" name="val[hidden_productid]" value="{$hidden_productid}"/>
            <input type="hidden" name="val[hidden_select]" value="{$hidden_select}"/>
        </div>
	</form>
	<div class="clear"></div>	

	<script type="text/javascript">
		$Behavior.yndInitFilterInAuctionForm = function() {l} 
			$("#js_ynauction_filterinauction_form").ajaxForm();
		{r}
	</script>

</div>