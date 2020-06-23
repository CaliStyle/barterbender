<?php 
/**
 * [PHPFOX_HEADER]
 * @copyright      YouNet Company
 * @author         LyTK
 * @package        Module_Coupon
 * @version        3.01
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div id="js_menu_drop_down" style="display:none;">
	<div class="link_menu dropContent" style="display:block;">
		<ul>
			<li><a href="#" onclick="return $Core.coupon.action(this, 'edit');">{phrase var='edit'}</a></li>
			<li><a href="#" onclick="return $Core.coupon.action(this, 'delete');">{phrase var='delete'}</a></li>
		</ul>
	</div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
	        {phrase var='faqs'}
        </div>
    </div>

    <form method="post" action="{url link='admincp.coupon.faq'}">
        <div class="panel-body">
            <div class="form-group">
                <div class="sortable dont-unbind-children">
                    {$sFaqs}
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" value="{phrase var='update_order'}" class="btn btn-primary" />
        </div>
    </form>
</div>
{literal}
<script>
    $Behavior.init_yncoupon_faqs = function () {
        yncoupon_faqs.init_sort();
        yncoupon_faqs.init_dropdown();
    }
</script>
{/literal}