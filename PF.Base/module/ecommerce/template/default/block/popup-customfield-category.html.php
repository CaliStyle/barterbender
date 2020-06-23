{literal}
	<style>
		#page_ecommerce_admincp_category_index .js_box_content {
			padding: 10px 20px 20px 20px;
		}
		.ecommerce-table{
			border-bottom: 1px solid #dbdbdb;
		}

	 	.ecommerce-table .ecommerce-table_left{
			width: 70%;
			padding: 10px 0px 10px 10px;
			float: left;
			font-weight: bold;
			box-sizing: border-box;
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
		}
		.ecommerce-table .ecommerce-table_right{
			width: 30%;
			padding: 10px 0px;
			float: left;
			box-sizing: border-box;
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
		}
		.ecommerce-table-header {
			text-transform: uppercase;
			font-weight: bold;
			background-color: #ebebeb;
		}
		.ecommerce-category-label {
			padding: 0 0 10px 10px;
		}
		.ecommerce-category-label span {
			text-transform: uppercase;
			color: #999999;
			font-weight: bold;
		}
	</style>
{/literal}

{if count($aGroupsInfo)}
<div class="ecommerce-category-label">
	{phrase var='category'}: <span>{$sCategory.title|convert}</span>
</div>

<div class="ecommerce-table ecommerce-table-header clearfix">
    <div class="ecommerce-table_left">
        {phrase var='custom_field_group'}
    </div>
    <div class="ecommerce-table_right">
        {phrase var='option'}
    </div>
</div>

	{foreach from=$aGroupsInfo item=aGroup}
	<div class="ecommerce-table clearfix">
	    <div class="ecommerce-table_left">
	        {$aGroup.phrase_var_name}
	    </div>
	    <div class="ecommerce-table_right">
	        <a href="{url link='admincp.ecommerce.customfield.add.id_'.$aGroup.group_id}">{phrase var='edit'}</a>
	    </div>
	</div>
	{/foreach}
{else}
{phrase var='there_are_no_custom_groups_associate_with_this_category'}
{/if}
