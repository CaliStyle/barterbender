<div id="yc_entry_submit_entry_content">
    <div class="yc_entry_submit_top">
        <h3 class="yc_entry_summary yc_submit_entry"> {phrase var='contest.submit_an_entry'}</h3>
        <div class="extra_info font_12">
            <p class="m_4" id='yncontest_create_new_item'>{phrase var='contest.if_you_do_not_have_any_items_link_create_here' link=$aAddEntryTemplateData.sAddNewItemLink}</p>
            <p>{phrase var='contest.or_choose_from_existing_photos_below'}</p>
        </div>
        <div class="header_bar_search">
            <form method="post" action="{$aYnContestItemSearchTool.search.action}&source={$iSourceSelected}" onbeforesubmit="$Core.Search.checkDefaultValue(this,\'{$aYnContestItemSearchTool.search.default_value}\');">
                <div>
                    <input type="hidden" name="search[submit]" value="1" />
                </div>
                <div class="header_bar_search_holder">
                    <div class="header_bar_search_default extra_info font_12" style='display:none'>{$aYnContestItemSearchTool.search.default_value}</div>
                    <input type="text" class="txt_input{if isset($aYnContestItemSearchTool.search.actual_value)} input_focus{/if} form-control" name="search[{$aYnContestItemSearchTool.search.name}]" value="{if isset($aYnContestItemSearchTool.search.actual_value)}{$aYnContestItemSearchTool.search.actual_value|clean}{else}{$aYnContestItemSearchTool.search.default_value}{/if}" />
                    <div class="header_bar_search_input"></div>
                </div>
                <div id="js_search_input_holder">
                    <div id="js_search_input_content">
                        {if isset($sModuleForInput)}
                        {module name='input.add' module=$sModuleForInput bAjaxSearch=true}
                        {/if}
                    </div>
                </div>
            </form>
        </div>
        <input type="hidden" id="ync_source_add_entry_id" value="{{$iSourceSelected}}" name="">
        {if $aAddEntryTemplateData.iItemType == 3 && ((int)Phpfox::isModule('ultimatevideo') + (int)Phpfox::isModule('videochannel') + (int)Phpfox::isModule('v')) > 1}
        <div class="form-group">
            <div class="extra_info font_12">
                <label >{_p('Select videos from')}:</label>
            </div>
            <div class="table_right">

                <select name="video_type" data-url="{$sCurrentUrl}" id="ync_select_adv_module" onchange="yncontest.addEntry.changeVideoSource(this,{$aAddEntryTemplateData.iContestId});">
                    {if Phpfox::isModule('ultimatevideo')}<option value="1" {if $iSourceSelected == 1}selected{/if}>{_p var='Ultimate Videos'}</option>{/if}
                    {if Phpfox::isModule('videochannel')}<option value="2" {if $iSourceSelected == 2}selected{/if}>{_p var='Video Channel'}</option>{/if}
                    {if Phpfox::isModule('v')}<option value="3" {if $iSourceSelected == 3}selected{/if}>{_p var='Core Videos'}</option>{/if}
                </select>
            </div>
        </div>
        {/if}
        {if $aAddEntryTemplateData.iItemType == 1 && Phpfox::isModule('ynblog')}
        <div class="form-group">
            <div class="extra_info font_12">
                <label >{_p('Select blogs from')}:</label>
            </div>
            <div class="table_right">

                <select name="blog_type" data-url="{$sCurrentUrl}" id="ync_select_adv_module" onchange="yncontest.addEntry.changeBlogSource(this,{$aAddEntryTemplateData.iContestId});">
                    <option value="1" {if $iSourceSelected != 2}selected{/if}>{_p('blog')}</option>
                    <option value="2" {if $iSourceSelected == 2}selected{/if}>{_p('ynblog')}</option>
                </select>
            </div>
        </div>
        {/if}
    </div>
    <div class="yc_entry_submit">
        {if (count($aAddEntryTemplateData.aItems))}
        <div class="wrap_list_items">
            {foreach from=$aAddEntryTemplateData.aItems name=entryItem item=aItem}
                {template file='contest.block.entry.entry-item'}
            {/foreach}
        </div>
        {template file='contest.block.pager'}

        {else}
            {phrase var='contest.you_have_no_item'}
        {/if}
        <form method='POST' action='#' id='yncontest_add_entry'>

            <input type='hidden' id='yncontest_item_id' name="val[item_id]" value='{$aAddEntryTemplateData.iChosenItemId}'/>
            <input type='hidden' id='yncontest_item_type' name="val[item_type]" value='{$aAddEntryTemplateData.iItemType}'/>
            <input type='hidden' id='yncontest_contest_id' name="val[item_contest_id]" value='{$aAddEntryTemplateData.iContestId}'/>


            <div id="core_js_messages">
                <div class="error_message" style='display:none' id='yncontest_must_select_an_item'> {phrase var='contest.please_select_an_item'}</div>
                <div class="error_message" style='display:none' id='yncontest_title_summary_required'> {phrase var='contest.title_and_description_are_required'}</div>
                <div class="error_message" style='display:none' id='yncontest_title_max_length'> {phrase var='contest.maxium_number_of_characters_for_title_is'} 255</div>
            </div>

            <div class="table form-group">
                <div class="table_left">
                    <label for="title">{required}{phrase var='contest.title'}: </label>
                </div>
                <div class="table_right">
                    <input type="text" class="form-control" name="val[title]"  id="yncontest_entry_title" size="60"
                    {if $aAddEntryTemplateData.sChosenItemTitle}
                        value = "{$aAddEntryTemplateData.sChosenItemTitle}"
                    {/if}
                    />
                </div>
                <div class="help-block">
                    {phrase var='contest.you_can_enter_maximum_number_characters', number=255}
                </div>
            </div>

            <div class="table form-group">
                <div class="table_left">
                    <label for="summary">{required}{phrase var='contest.description'}:</label>
                </div>

                <div class="table_right">
                    <textarea class="form-control" cols="56" rows="10" name="val[summary]" id="yncontest_entry_summary" style="height:70px;"></textarea>
                </div>
            </div>

            <div class="table_clear">
            <button type="button" name="val[preview]" class="button btn btn-info btn-sm" onclick="yncontest.addEntry.previewEntry('{phrase var='contest.entry_preview' phpfox_squote=true}'); return false;">{phrase var='contest.preview'}</button>
            <button type="button" name="val[submit]" class="button btn btn-primary btn-sm" id='yncontest_submit_add_entry_button' onclick="yncontest.addEntry.submitAddEntry(); return false;">{phrase var='contest.submit'}</button>
            </div>
        </form>
    </div>
</div>
{literal}
<script type="text/javascript">
	$Behavior.yncontestInitialzeItemEntryOnclick = function() {
		yncontest.addEntry.initializeClickOnEntryItem();
		yncontest.addEntry.addAjaxForCreateNewItem({/literal}{$aContest.contest_id}, {$aContest.type}{literal});

	}
</script>
{/literal}
{if $aAddEntryTemplateData.iChosenItemId != 0}
	<script type="text/javascript">
		$Behavior.yncontestSetChosenItem = function() {l}
			yncontest.addEntry.setChosenItem({$aAddEntryTemplateData.iChosenItemId});
		{r}
	</script>
{/if}

