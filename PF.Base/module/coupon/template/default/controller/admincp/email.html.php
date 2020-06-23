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
{$sCreateJs}
<form method="post" id="frmEmailTemplate" action="{url link='admincp.coupon.email'}" name="js_form">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='email_templates'}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="">{required}{phrase var='email_templates_types'}:</label>
                <select class="form-control" name="val[type_id]" id="type_id" onchange="$.ajaxCall('coupon.fillEmailTemplate', 'type_id=' + $(this).val());">
                    <option value="">{phrase var='select'}:</option>
                    <option value="{$aTypes.createcouponsuccessful_owner}">{phrase var='createcouponsuccessful_owner'}</option>
                    <option value="{$aTypes.couponapproved_owner}">{phrase var='couponpublished_owner'}</option>
                    <option value="{$aTypes.couponfeatured_owner}">{phrase var='couponfeatured_owner'}</option>
                    <option value="{$aTypes.startrunningcoupon_owner}">{phrase var='startrunningcoupon_owner'}</option>
                    <option value="{$aTypes.couponclaimed_owner}">{phrase var='couponclaimed_owner'}</option>
                    <option value="{$aTypes.couponclaimed_claimer}">{phrase var='couponclaimed_claimer'}</option>
                    <option value="{$aTypes.couponclosed_owner}">{phrase var='couponclosed_owner'}</option>
                </select>
            </div>

            <div class="form-group">
                <label for="">{phrase var='subject'}:</label>
                <input class="form-control" type="text" name="val[email_subject]" value="{value type='input' id='email_subject'}" id="email_subject" size="40" maxlength="150" />
            </div>

            <div class="form-group">
                <div id="lbl_html_text">
                    <label for="">{phrase var='content'}:</label>
                </div>
                {editor id='email_template' rows='15'}
            </div>

            <div class="extra_info table">
                    {module name='coupon.keywordplaceholder'}
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" value="{phrase var='save_now'}" class="btn btn-primary" />
        </div>
    </div>
</form>

<script type="text/javascript">
$Behavior.emailCoupon = function(){l}
	$('#type_id option').each(function() {l} 
		if($(this).val() == {$iCurrentTypeId})
		{l}
			$(this).attr('selected', 'selected');
		{r}
	{r});
{r}	
</script>