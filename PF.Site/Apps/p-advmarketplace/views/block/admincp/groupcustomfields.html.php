<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<h3>{_p var=$sGroupName}</h3>
	<input type="hidden" class="customfield-owner" value="{$sKeyVar}" />
	{foreach from=$aCustomFields key=iKey item=aField}
		{module name="advancedmarketplace.admincp.customfieldcell" aCellCustomFields=$aField sKeyVarCell=$sKeyVar}
	{/foreach}
	<div id="yn_jh_customfieldrow_anchor">
		<button class="button yn_jh_saveall" style=";" href="#">{phrase var="advancedmarketplace.save_changes"}</button>
		&nbsp;&nbsp;&nbsp;&nbsp;
		<a href="#" title="{phrase var='advancedmarketplace.add_custom_field'}" class="yn_jh_addcustomfield" ref="{$sKeyVar}">+ {phrase var='advancedmarketplace.add_custom_field'}...</a>
		<img class="ajxloader" src="{param var='core.path_actual'}PF.Site/Apps/p-advmarketplace/assets/image/default/ajxloader.gif" />
	</div>
{literal}
<script language="javascript" type="text/javascript">
	$(".yn_jh_customfield").each(function(/* index */){
		setCustomFieldInterfaceActions($(this));
	});
	$(".anoption").each(function(/* index */){
		processCustomFieldOptionSample($(this), $(this).find("ghs_cid").val());
	});
</script>
{/literal}