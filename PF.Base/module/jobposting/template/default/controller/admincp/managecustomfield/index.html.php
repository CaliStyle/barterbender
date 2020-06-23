<?php

defined('PHPFOX') or exit('NO DICE!');

?>
<div id="js_menu_drop_down" style="display:none;">
	<div class="link_menu dropContent" style="display:block;">
		<ul>
			<li>
				<a href="#" onclick="return $Core.jobposting.action(this, 'edit');">{phrase var='edit'}</a>
			</li>
			<li>
				<a href="#" onclick="return $Core.jobposting.action(this, 'delete');">{phrase var='delete'}</a>
			</li>
		</ul>
	</div>
</div>

<form action="{url link='admincp.jobposting.managecustomfield'}" id="jobposting_manage_fields" method="POST">
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {phrase var='manage_custom_field'}
        </div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="form-group col-md-6 col-xs-12">
                <select title="" name="objtype" class="form-control" onchange="$('.fields-sortable').remove(); $('#jobposting_manage_fields').submit()">
                    <option value="1" id="custom_field_company" {if $objtype == 1} selected {/if} >{phrase var='employer'}</option>
                    <option value="2" id="custom_field_job" {if $objtype == 2} selected {/if} >{phrase var='job'}</option>
                </select>
            </div>
            <div class="form-group col-md-6 col-xs-12">
                <button class="btn btn-primary" onclick="tb_show('', $.ajaxBox('jobposting.controllerAddFieldBackEnd', 'height=300&width=300&action=add&objtype={$objtype}')); return false;">{phrase var='add_field_question'}</button>
            </div>
        </div>
        <div class="fields-sortable">
            {if (($aFields|count) >0)}
                <label>{_p var='list_custom_fields'}</label>
                <ul class="list-group dont-unbind">
                    {foreach from=$aFields key=iKey item=iField}
                    <li class="list-group-item">
                        {img theme='misc/draggable.png' alt=''}
                        <input type="hidden" name="order[{$iField.field_id}]" value="{$iField.ordering}" class="js_mp_order">
                        <a href="#?id={$iField.field_id}&objtype={$objtype}" class="js_drop_down">{phrase var=$iField.phrase_var_name}</a>
                    </li>
                    {/foreach}
                </ul>
            {/if}
        </div>
    </div>
    {if (($aFields|count) >0)}
    <div class="panel-footer">
        <input type="submit" value="{phrase var='update_order'}" class="btn btn-primary">
    </div>
    {/if}
</div>
</form>

{literal}
<script type="text/javascript">
	$Behavior.init_ynjobposting_custom_fields = function () {
        ynjobposting_init.init_sort('fields-sortable');
        ynjobposting_init.init_dropdown();
    }
</script>
{/literal}