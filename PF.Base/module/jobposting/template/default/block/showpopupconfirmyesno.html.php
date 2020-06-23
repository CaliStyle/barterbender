
<div>
	{$phare}
</div>
<br />
<div class="job-posting-popup-bottom-btn-group">
	<input id="js_ynsa_faq_confirmbtn" type="button" onclick="js_box_remove(this); $.ajaxCall('{$function}','{$value}'); " value="{phrase var='yes'}" class="button btn btn-primary" />
	<input id="js_ynsa_faq_confirmbtn" type="button" onclick="return js_box_remove(this);" value="{phrase var='no'}"  class="button btn btn-default" />
</div>