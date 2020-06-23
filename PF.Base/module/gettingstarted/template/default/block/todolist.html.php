<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
{literal}
<!--[if ie]>
<style type="text/css">
	.donot_show_me_message
	{
		float: left;
		margin-top: -16px;
	}
</style>
<![endif]-->
{/literal}
<div id="todolist">
	<input type="hidden" id="ordering" value="{$FirstTodoList.ordering}"/>
	<div class="title_todolist" id="title_todolist">
    	{$FirstTodoList.title}
	</div>
	<div id="description_todolist" class="description_content item_view_content">
    	{$FirstTodoList.description_parsed|parse}
    	<div style="clear:both"></div>
	</div>
	<div class="border_todolist">

    	<div id ="command_button_gts">
    			<span id="pretodolist" >

    			{if $showbuttonPre == 1}
    				<a href="javascript:void(0);" onclick="javascript:viewPreTodoList();return false;" ><button type="button" class="button btn btn-default btn-sm"><i class="ico ico-angle-left"></i></button></a>
    			{/if}
    			</span>
    			<span id="nexttodolist" >

            		{if $showbuttonNext == 1}
            			<a href="javascript:void(0);" onclick="javascript:viewNextTodoList(); return false;" ><button type="button" class="button btn btn-default btn-sm"><i class="ico ico-angle-right"></i></button></a>
            		{/if}
            	</span>
    			<span id="donetodolist">
    				{if $showbuttonDone == 1}
    					<a href="javascript:void(0);" onclick="javascript:doneTodoList(); return false;"><button type="button" class="button btn btn-primary btn-sm" onclick="tb_remove();">{phrase var='gettingstarted.done'}</button></a>
    				{/if}
    			</span>


    	</div>
	</div>
</div>
<div id="donot_show_me">
	<span class="check_box_show_message"><input id="checkbox_todolist" type="checkbox" value="" name="autohidden" onclick="validate_checkbox_todolist()"/></span>
	<label class="donot_show_me_message" for="checkbox_todolist">{phrase var='gettingstarted.do_not_show_it_again'}.</label></div>
{literal}
<script type="text/javascript">

	var append_do_not_show_me = document.getElementById('donot_show_me');
	$('#todolist').append(append_do_not_show_me);

    function validate_checkbox_todolist()
    {
        var checkbox=document.getElementById('checkbox_todolist');
        var checkbox_check = 1;
        if(checkbox.checked==true)
        {
            checkbox_check=0;
        }
        $.ajaxCall('gettingstarted.updateCheckboxTodolist','active='+checkbox_check);
    }

</script>
{/literal}