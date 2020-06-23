{literal}
<style>
	#page_coupon_admincp_customfield_index #site_content > *:not(.table_header){
		background: #FFF;
		padding: 10px;
	}
	.table_clear{
		margin-bottom: 0px;
	}
</style>
{/literal}

<div id="js_menu_drop_down" style="display:none;">
	<div class="link_menu dropContent" style="display:block;">
		<ul>
			<li>
				<a href="#" onclick="return $Core.coupon.action(this, 'edit');">{phrase var='edit'}</a>
			</li>
			<li>
				<a href="#" onclick="return $Core.coupon.action(this, 'delete');">{phrase var='delete'}</a>
			</li>
		</ul>
	</div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
	        {phrase var='manage_custom_field'}
        </div>
    </div>

    <div class="js_mp_parent_holder" id="js_mp_holder">
        <a href="#"
           onclick="tb_show('{phrase var='add_field_question'}', $.ajaxBox('coupon.AdminAddCustomFieldBackEnd', 'height=300&width=300&action=add')); return false;"
           style="margin-left: 15px">{phrase var='add_field_question'}</a>
    </div>

    {if (($aFields|count) >0)}
	    <form method="post" action="{url link='admincp.coupon.customfield'}">
            <div class="panel-body">
                <div class="form-group">
                    <div class="sortable">
                        <ul class="ui-sortable dont-unbind-children">
                            {foreach from=$aFields key=iKey item=iField}
                                <li><img src="{$sCorePath}theme/adminpanel/default/style/default/image/misc/draggable.png" alt="">
                                    <input type="hidden" name="order[{$iField.field_id}]" value="{$iField.ordering}" class="js_mp_order">
                                    <a href="#?id={$iField.field_id}" class="js_drop_down">{phrase var=$iField.phrase_var_name}</a>
                                </li>
                            {/foreach}
                        </ul>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <input type="submit" value="{phrase var='update_order'}" class="btn btn-primary" />
            </div>
	    </form>
    {/if}
</div>

{literal}
<script>
    $Behavior.init_yncoupon_custom_fields = function () {
        yncoupon_customfield.init_sort();
        yncoupon_customfield.init_dropdown();
    }
</script>
{/literal}
