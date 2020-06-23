<div class="yns adv-search-block" id ="jobposting_adv_search_company" {if !$bIsAdvSearch }style="display:none; margin-bottom: 10px;"{else}style="display:block; margin-bottom: 10px;"{/if}>
	<form method="post" action="{$sFormUrl}">
		<input type="hidden" id="flag_advancedsearch_company" {if !$bIsAdvSearch }value="0"{else}value="1"{/if} name="search[flag_advancedsearch]"/>
		
		<div class="form-group">
			<label>{phrase var='company_name'}</label>:
			<div class="table_right">
				<input type="text" name="search[name]" class="form-control" value="{if isset($aForms.name)}{$aForms.name}{/if}" id="name" size="22" maxlength="200" />
			</div>
		</div>
		<div class="form-group">
			<label>{phrase var='location'}</label>:
			<div class="table_right">
				<input type="text" name="search[location]" class="form-control" value="{if isset($aForms.location)}{$aForms.location}{/if}" id="location" size="22" maxlength="200" />
			</div>
		</div>
		<div class="form-group">
			<label>{phrase var='industry'}</label>:
			<div class="table_right">
				{$aIndustryBlock}
			</div>
		</div>
		<div class="p_top_8">
			<input type="submit" id="filter_submit" name="search[submit]" value="{phrase var='search'}" class="btn btn-sm  btn-success" />
		</div>		
	</form>
</div>