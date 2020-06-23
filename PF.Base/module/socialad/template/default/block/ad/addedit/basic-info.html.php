<div class="ynsaSection ynsaTopSection">
	<div class="ynsaSectionHeading ynsaClearFix">
		<div class="ynsaLFloat">
			<div class="ynsaHeadingText">
			</div>
		</div>
		<div class="ynsaRFloat">
			<!-- put help link here -->
		</div>
	</div>
		{if $bNoCreateBtn = true} {/if}
		{template file='socialad.block.package.entry'}
</div>
<div class="ynsaSection_RightContent">
	<div class="ynsaSection">
		<div class="ynsaSectionHeading ynsaClearFix">
			<div class="ynsaLFloat">
				<div class="ynsaHeadingText">
					{phrase var='what_do_you_want_to_advertise'}
				</div>
			</div>
			<div class="ynsaRFloat">
				<!-- put help link here -->
			</div>
		</div>

		<div class="ynsaSectionContent ynsaFixOveflowSectionContent">
			<div class="ynsaLeftInnerContent fix_width">
				<div class="table form-group"> <!-- select ITEM to advertise -->
					<div class="table_left">
						{phrase var='item_type'}:
					</div>
					<div class="table_right">
						<ul class="checklist_grp">
							{foreach from=$aSaItemTypes item=aItemType}
							<li>
								<div class="radio-inline">
									<label>
										<input type="radio" class='itemType jsItemTypeRadio' name="val[ad_item_type]" data-name='{$aItemType.name}' value="{$aItemType.id}"
											{if $iDefaultItemTypeId == $aItemType.id}
												checked="checked"
											{/if}

										>
										{$aItemType.phrase}
									</label>
								</div>
							</li>
							{/foreach}
						</ul>
					</div>
					<div class="jsItemNormalList" id="js_ynsa_ad_select_item">
					{module name='socialad.ad.addedit.select-item'}
					</div>
					<div class="ynsaRightInnerContent ynsaSelectItemHolder url">
						<!-- input external URL -->
						<div class="form-group" id="js_ynsa_ad_external_url"
							{if isset($aForms) && !$aForms.ad_external_url}
								style="display:none"
							{/if}
						>
							<div class="table_left">
								<label for="title">{phrase var='url'}</label>
							</div>
							<div class="table_right">
								<input class="form-control" type="text" name="val[ad_external_url]" value="{value type='input' id='ad_external_url'}" id="ad_external_url" size="37" />
							</div>
						</div> <!-- end external URL -->
					</div> <!-- float right end -->
				</div>
			</div> <!-- end selecting item type -->

			<div class="ynsaExtraSectionInfo width_fix">
				<div class="ynsaExtraSectionInfoTitle">
					{phrase var='getting_started'}
				</div>
				<div class="ynsaExtraSectionInfoContent">
					{phrase var='getting_started_intro'}
				</div>
			</div>
		</div> <! -- section content end -->
	</div> <!-- end select item section-->

	<div class="ynsaSection">
		<div class="ynsaSectionHeading ynsaClearFix">
			<div class="ynsaLFloat">
				<div class="ynsaHeadingText">
					{phrase var='what_type_of_ad_do_you_want_to_create'}
				</div>
			</div>
			<div class="ynsaRFloat">
				<!-- put help link here -->
			</div>
		</div>

		<div class="ynsaSectionContent type_ads">
			{foreach from=$aSaAdTypes item=aType}
					<div class="ynsaSelectBigDiv {if $iDefaultAdTypeId == $aType.id} ynsaOn{/if}" >
						<div class="ynsaImage ads_{$aType.id}"> </div>
						<div class="ynsaPhrase ynsaClearFix" > <span> {$aType.phrase} </span> </div>
						<div class="ynsaDescription ynsaClearFix" > <span> {$aType.description}</span> </div>
						<div class="ynsaActionHolder" >
							<input {if $iDefaultAdTypeId == $aType.id} checked="checked" {/if} class="jsYnsaSelectAdType" data-name="{$aType.name}" type="radio" name="val[ad_type]" value="{$aType.id}" />
						</div>
					</div>
			{/foreach}
		</div> <!-- end select item type section content -->
	</div> <!-- end select item type section-->


	<div class="ynsaSection">

		<div class="ynsaSectionHeading ynsaClearFix">
			<div class="ynsaLFloat">
				<div class="ynsaHeadingText">
					{phrase var='upload_image'}
				</div>
			</div>
			<div class="ynsaRFloat">
				<!-- put help link here -->
			</div>
		</div>

		<div class="ynsaSectionContent ynsaUploadImageDiv" id="containerUploadImageParent">
	        <div id="containerUploadImage" class="ynsaLeftInnerContent" style="width:35%;">
				&nbsp;
			</div>
			<div class="ynsaExtraSectionInfo ynsaExtraSectionExtraWide width_fix">
				<div class="ynsaExtraSectionInfoTitle">
					{phrase var='choose_proper_size'}
				</div>
				<div class="ynsaExtraSectionInfoContent">
					<ul>
						<li>{phrase var='recommended_image_size_for_html_ad'}:  {$aImageSizes.html.width} {phrase var='pixels'} x {$aImageSizes.html.height} {phrase var='pixels'}</li>
						<li>{phrase var='recommended_image_size_for_feed_ad'}:  {$aImageSizes.feed.width} {phrase var='pixels'} x {$aImageSizes.feed.height} {phrase var='pixels'}</li>
						<li> {phrase var='supported_extensions_gif_jpg_and_png'}</li>
					</ul>
				</div>
			</div>
		</div>
	</div> <!-- image -->


	<div class="ynsaSection" >

		<div class="ynsaSectionHeading ynsaClearFix">
			<div class="ynsaLFloat">
				<div class="ynsaHeadingText">
					{phrase var='preview_and_edit'}
				</div>
			</div>
			<div class="ynsaRFloat">
				<!-- put help link here -->
			</div>
		</div>

		<div class="ynsaSectionContent" id="ynsa_preview_and_edit_section_content">
			<div class="ynsaLeftInnerContent ynsaTitleTextSide">
				<div class="form-group">
					<label for="title">{phrase var='title'}</label>	
					<input class="form-control" data-limit-char="{$iSaTitleLimitCharacter}" type="text" name="val[ad_title]" value="{if !isset($aForms)}{phrase var='example_ad_title'}{else} {$aForms.ad_title} {/if} " id="js_ynsa_ad_title" />
					<div class="extra_info js_limit_info">
					</div>
				</div>

				<div class=" form-group" id="js_ynsa_text_holder">
						<label for="title">{phrase var='text'}</label>
							<textarea class="form-control" data-limit-char="{$iSaTextLimitCharacter}" name="val[ad_text]" cols="27" rows="8" id="js_ynsa_ad_text" >{if !isset($aForms)}{phrase var='example_ad_text'}{else} {$aForms.ad_text} {/if} </textarea>
					<div class="extra_info js_limit_info">
					</div>
				</div> <!-- end text -->
				<div class="table form-group" id="js_ynsa_select_block">
						{phrase var='block_sa'}:
						<select class="form-control" name="val[placement_block_id]" id="js_ynsa_placement_block_id">
							{foreach from=$aSaBlocks item=iBlock}
								<option value="{$iBlock}"
									{if isset($aForms) && $aForms.placement_block_id == $iBlock}
										selected="selected"
									{/if}

								>{phrase var='block_sa'} {$iBlock}</option>
							{/foreach}
						</select>
						<div class="clear"></div>
					<div class="extra_info width_limit"> {phrase var='ad_banner_preview_warning'}</div>
				</div> <!-- end block -->
			</div>

			<div class="ynsaRightInnerContent ynsaPreviewSide">
					{module name='socialad.ad.preview.preview' iAdTypeId=$iDefaultAdType}
			</div>
		</div>
	</div> <!-- end edit and preview section-->
</div>

