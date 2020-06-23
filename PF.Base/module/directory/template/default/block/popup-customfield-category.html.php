{literal}
	<style>
		.yndirectory-table{
			border-bottom: 1px solid #dbdbdb;
		}

		
	 	.yndirectory-table .yndirectory-table_left{
			width: 70%;
			padding: 10px 0px;
			float: left;
			font-weight: bold;
			box-sizing: border-box;
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
		}
		.yndirectory-table .yndirectory-table_right{
			width: 30%;
			padding: 10px 0px;
			float: left;
			box-sizing: border-box;
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
		}
	</style>
{/literal}

{if count($aGroupsInfo)}
<span style="font-weight: bold;font-size: 14px;display: block;text-align: center;margin-bottom: 10px;">
    {if Core\Lib::phrase()->isPhrase($this->_aVars['sCategory']['title'])}
        {phrase var=$sCategory.title}
    {else}
        {$sCategory.title}
    {/if}
</span>
	<div class="yndirectory-table clearfix">
	    <div class="yndirectory-table_left">
	        {phrase var='custom_field_group'}
	    </div>
	    <div class="yndirectory-table_right">
	        {phrase var='option'}
	    </div>
	</div>

	{foreach from=$aGroupsInfo item=aGroup}
	<div class="yndirectory-table clearfix">
	    <div class="yndirectory-table_left">
	        {$aGroup.phrase_var_name}
	    </div>
	    <div class="yndirectory-table_right">
	        <a href="{url link='admincp.directory.customfield.add.id_'.$aGroup.group_id}">{phrase var='edit'}</a>
	    </div>
	</div>
	{/foreach}
{else}
{phrase var='there_are_no_custom_groups_associate_with_this_category'}
{/if}
