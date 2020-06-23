
{if count($aCustomFields) > 0}
	{foreach from=$aCustomFields key=sKey item=aCustomField}
		<label class="ync-customfield__label" for="title">{phrase var=$sKey}</label>
		{if count($aCustomField) > 0}
		{foreach from=$aCustomField key=iKey item=aField}
			{if $aField.view_id}
				<div class="form-group">
					{module name="advancedmarketplace.frontend.edit.".($aField.view_id) aField=$aField cfInfors = $cfInfors}
				</div>
			{/if}
		{/foreach}
		{/if}
	{/foreach}
{else}{/if}
{literal}
<script language="javascript" type="text/javascript">
	$Behavior.valid_addListing = function() {
		$("#js_advancedmarketplace_form").submit(function(evt) {
            if($('#js_mp_block_detail:visible').length) {
                isValid = 0;
                $(".validstp").each(function (index) {
                    var $this = $(this);
                    var $id = $this.attr("rel");
                    var _value_a = '';
                    if ($this.parent().parent().find('[name*="customfield"][type=text]:visible').size()) {
                        _value_a = $this.parent().parent().find('[name*="customfield"][type=text]:visible').first().val() != "";
                    } else if ($this.parent().parent().find('select[name*="customfield"]:visible').size()) {
                        cobj = $this.parent().parent().find('select[name*="customfield"]:visible').first();
                        _value_a = (cobj.children("option[value='" + cobj.val() + "']")).text() != "";
                    } else {
                        _value_a = false;
                    }

                    var _value_b = $this.parent().parent().find('[name*="customfield"]:checked').size();
                    if ((_value_a == '') && (_value_b <= 0)) {
                        setTimeout(function () {
                            if ($("div[ref=" + $id + "]").size() <= 0) {
                                $("#js_advancedmarketplace_form_msg").show().find(".error_message").html();
                                $("#js_advancedmarketplace_form_msg").prepend($("<div class=\"error_message\" ref=\"" + $id + "\">").html("\"" + $this.html() + "\" {/literal}{phrase var='advancedmarketplace.can_not_be_empty'}{literal}"));
                            }
                        }, 1);

                        isValid++;
                    }
                });

                return (isValid == 0);
            }
		});
	}
</script>
{/literal}