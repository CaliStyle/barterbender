<?php
/**
 * @copyright		[YOUNETCO]
 * @author  		NghiDV
 * @package  		Module_Suggestion
 * @version 		$Id: ajax.class.php 1 2011-11-25 15:29:17Z YOUNETCO $
 */
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="_block_h1">
	<h1>{if $bIsFriends}
		{phrase var='suggestion.friend_suggestion'}
		{else}
		{phrase var='suggestion.my_suggestions'}
		{/if}
		</h1>
</div>

<div class="yn_suggestion_list_item">
	<form action="" method="post" name="frmIncoming" id="frmIncoming">
		<input type="hidden" name="iUserId" id="iUserId" value="<?= Phpfox::getUserId(); ?>" />
		<div id="sKey" style="display:none;">{$sKey}</div>
		{if count($aRows)>0}
			{foreach from=$aRows key=iKey item=aItem}
				{if count($aItem)>0}
				<h3>{$aItem[0].header_name}</h3>
				<div class="suggest_row_parent">
					<div id="{$iKey}" >
						{foreach from=$aItem item=aRow}
								<div id="ynsuggestion_item_{$aRow.suggestion_id}" class="ynsuggestion_item clearfix">
									<span class="ajaxLoader hide"
										style="position: absolute; right:120px;">
									<img src="{$sFullUrl}theme/frontend/default/style/default/image/ajax/add.gif" /></span>

									<div class="suggestion_image">
										{$aRow.avatar}
									</div>

									<div class="suggestion_info">
										<div class="user_tooltip_info_user" itemprop="name">
											{$aRow.info}
										</div>

										<div class="suggestion_description">
											{$aRow.create}
										</div>

										<div class="user_browse_description">
											{$aRow.suggest}
										</div>

										<div class="user_browse_description">
											{$aRow.message}
										</div>
									</div>

									<div class="suggestion_action">

										{if (isset($aRow.accept)) }
										<button type="button" class="button btn btn-primary btn-sm" onclick="doProcess(this, 1, {$aRow.friend_user_id}, {$aRow.friend_friend_user_id}, 'process_{$aRow.suggestion_id}','{$aRow.module_id}', '{$aRow.url}'); return false;">{$aRow.accept}</button>
										{/if}

										{if (isset($aRow.ignore)) }
										<button type="button" class="button btn btn-default btn-sm" onclick="doProcess(this, 2, {$aRow.friend_user_id}, {$aRow.friend_friend_user_id}, 'process_{$aRow.suggestion_id}','{$aRow.module_id}','{$aRow.url}'); return false;">{$aRow.ignore}</button>
										{/if}

										{if (isset($aRow.delete)) }
										<button type="button" class="button btn btn-danger btn-sm" onclick="doProcessDelete(this, {$aRow.suggestion_id}); return false;">{$aRow.delete}</button>
										{/if}
									</div>

								</div>

						{/foreach}
					</div>

                    {if $aRow1.count > $iLimit}
                    <div id='suggestion_view_more_{$iKey}' class="clearfix">
                        <a class='ynsug_view_more no_ajax_link'
                           onClick="$('#suggestion_view_more_{$iKey}').hide(); $('#view_more_loader').show();	$.ajaxCall('suggestion.loadObjectViewMore','type={$iKey}&iPage_{$iKey}='+$('#iPage_{$iKey}').val());">{phrase
                            var='suggestion.view_more'}
                        </a>
                    </div>
                    {/if}
                    {for $i=0; $i < $iModuleActive; $i++}
                        {if $aItem[0].module_id == $moduleId[$i]}
                            {if $bAll && $aRowAll[$i] > $iLimit}
                            <div id='suggestion_view_more_{$iKey}' class="clearfix">
                                <a class='ynsug_view_more no_ajax_link'
                                   onClick="$('#suggestion_view_more_{$iKey}').hide(); $('#view_more_loader').show();	$.ajaxCall('suggestion.loadObjectViewMore','type={$iKey}&iPage_{$iKey}='+$('#iPage_{$iKey}').val());">{phrase
                                    var='suggestion.view_more'}
                                </a>
                            </div>
                            {/if}
                        {/if}
                    {/for}
                    <input type="hidden" id="iPage_{$iKey}" value="1">

                </div>
				{else}
					{if ($sView != 'my' && $sView != 'friends') }
						<div class="message">{phrase var='suggestion.no_new_suggestion_at_this_time'}</div>
					{/if}
				{/if}
			{/foreach}
		{else}
			<div class="message">{phrase var='suggestion.no_new_suggestion_at_this_time'}</div>
		{/if}
	</form>
</div>
