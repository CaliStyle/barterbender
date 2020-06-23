{module name='socialad.sub-menu'}

{if !empty($sMessage)}<div class="message">{$sMessage}</div>{/if}
{if $aSaPackages}
<div class="ynsaChoosePackage ">
{foreach from=$aSaPackages item=aSaPackage}
	<div class="ynsaLFloat ynsaWideDiv">
		{template file='socialad.block.package.entry'}
	</div>
{/foreach}
</div>
{else}

<div class="extra_info"> {phrase var='currently_there_is_no_package_to_select'}</div>
{/if}
