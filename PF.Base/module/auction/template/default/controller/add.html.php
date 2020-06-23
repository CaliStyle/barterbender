<div id="ynauction_add" class="main_break">
    <div class="ynauction-hiddenblock">
        <input type="hidden" value="add" id="ynauction_pagename" name="ynauction_pagename">
    </div>
    {if isset($invoice_id) && (int)$invoice_id > 0}
    <div>
        <h3>{phrase var='payment_methods'}</h3>
        {module name='api.gateway.form'}
    </div>
    {else}
    <div>
        {$sCreateJs}
        <form enctype="multipart/form-data" id="ynauction_add_auction_form" action="{$sFormUrl}" class="ynauction-add-edit-form" method="post" onsubmit="{$sGetJsForm}">
            <div class="ynauction-hiddenblock">
                <input type="hidden" value="{$iDefaultFeatureFee}" id="ynauction_defaultfeaturefee">
                <input type="hidden" value="{$iDefaultPublishFee}" id="ynauction_defaultpublishfee">
                <input type="hidden" value="{$iRatioBuyItNowPrice}" id="ynauction_ratio_buyitnow_price">
            </div>
            <div>
                <div id="js_custom_privacy_input_holder">
                    {if $bIsEdit && empty($sModule)}
                    {module name='privacy.build' privacy_item_id=$aForms.auction_id privacy_module_id='auction'}
                    {/if}
                </div>

                <input type="hidden" name="val[attachment]" class="js_attachment" value="{value type='input' id='attachment'}" />
                <input type="hidden" name="val[selected_categories]" id="js_selected_categories" value="{value type='input' id='selected_categories'}" />
                {if Phpfox::getParam('core.force_https_secure_pages')}
                <div><input id="force_https_secure_pages" type="hidden" name="force_https_secure_pages" value="https" /></div>
                {else}
                <div><input id="force_https_secure_pages" type="hidden" name="force_https_secure_pages" value="http" /></div>
                {/if}
                {if !empty($sModule)}
                <div><input type="hidden" name="module" value="{$sModule|htmlspecialchars}" /></div>
                {/if}
                {if !empty($iItem)}
                <div><input type="hidden" name="item" value="{$iItem|htmlspecialchars}" /></div>
                {/if}
                {if $bIsEdit}
                <div><input type="hidden" name="val[auction_id]" value="{$aForms.auction_id}" /></div>
                <div><input type="hidden" name="val[auction_status]" value="{$aForms.auction_status}" /></div>
                {/if}

                <div id="js_auction_block_main" class="js_auction_block page_section_menu_holder">

                    <h3>{phrase var='general_info'}</h3>
                    <div class="table form-group">
                        <div class="table_left">
                            <label for="name">{required}{phrase var='product_title'}: </label>
                        </div>
                        <div class="table_right">
                            <input class="form-control" type="text" name="val[name]" value="{value type='input' id='name'}" id="name" size="60" />
                        </div>
                    </div>

                    <div id="ynauction_categorylist" class="table form-group">
                        <div class="table_left">
                            <label for="category">{required}{phrase var='category'}:</label>
                        </div>
                        <div class="table_right">
                            {$sCategories}
                        </div>
                    </div>
                    <div class="table form-group">
                        <div id="ynauction_customfield_category">
                        </div>
                    </div>

                    {if isset($aUOMs) && count($aUOMs)}
                    <div class="table form-group">
                        <div class="table_left">
                            <label for="uom">{required}{phrase var='uom'}: </label>
                        </div>
                        <div class="table_right">
                            <select name="val[uom]" id="ynauction_uom" class="form-control">
                                {foreach from=$aUOMs item=uom}
                                <option value="{$uom.uom_id}" {if isset($aForms.uom) && $aForms.uom == $uom.uom_id }selected{/if}>{$uom.title}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    {/if}
                    <div class="table form-group">
                        <div class="table_left">
                            <label for="quantity">{required}{phrase var='quantity'}: </label>
                        </div>
                        <div class="table_right">
                            <input class="form-control" type="text" name="val[quantity]" id="ynauction_quantity" value="{if isset($aForms.quantity) && isset($aForms.quantity)}{$aForms.quantity}{/if}" size="40" />
                        </div>
                    </div>

                    <div class="table form-group">
                        <div class="table_left">
                            <label for="description">{phrase var='description'}</label>
                        </div>
                        <div class="table_right">
                            {editor id='description'}
                        </div>
                    </div>

                    <div class="table form-group">
                        <div class="table_left">
                            <label for="shipping">{required}{phrase var='shipping_payment'}</label>
                        </div>
                        <div class="table_right">
                            {editor id='shipping'}
                        </div>
                    </div>

                    <h3>{phrase var='price'}</h3>

                    <div class="table form-group">
                        <div class="table_left">
                            <label for="reserve_price">{required}{phrase var='reserve_price'} ({$aCurrentCurrencies.0.currency_id}): </label>
                        </div>
                        <div class="table_right">
                            <input class="form-control" type="text" name="val[reserve_price]" id="ynauction_reserve_price" value="{if isset($aForms.reserve_price) && isset($aForms.reserve_price)}{$aForms.reserve_price}{/if}" size="40" />
                            <div class="extra_info">
                                <input type="checkbox" id ="auction_add_checkbok" name="val[hide_reserve_price]" {if isset($aForms) && isset($aForms.is_hide_reserve_price) && $aForms.is_hide_reserve_price }checked{/if}  > {phrase var='hide'}
                            </div>
                        </div>
                    </div>

                    <div class="table form-group">
                        <div class="table_left">
                            <label for="buynow_price">{required}{phrase var='buy_now_price'} ({$aCurrentCurrencies.0.currency_id}): </label>
                        </div>
                        <div class="table_right">
                            <input class="form-control" type="text" name="val[buynow_price]" id="ynauction_buynow_price" value="{if isset($aForms.buynow_price) && isset($aForms.buynow_price)}{$aForms.buynow_price}{/if}" size="40" />
                        </div>
                    </div>


                    <div class="table form-group-follow">
                        {module name='core.upload-form' type='auction_logo' current_photo=''}
                    </div>

                    <h3>{phrase var='availability'}</h3>

                    <div class="table form-group">
                        <div class="table_left">
                            {required}{phrase var='start_date'}:
                        </div>
                        <div class="table_right">
                            <div class="ync_start_time" style="position: relative;">
                                {select_date prefix='start_time_' id='_start_time' start_year='current_year' end_year='+10' field_separator=' / ' field_order='MDY' default_all=true add_time=true time_separator='auction.time_separator'}
                            </div>
                        </div>
                    </div>

                    <div class="table form-group">
                        <div class="table_left">
                            {required}{phrase var='end_date'}:
                        </div>
                        <div class="table_right">
                            <div class="ync_end_time" style="position: relative;">
                                {select_date prefix='end_time_' id='_end_time' start_year='current_year' end_year='+10' field_separator=' / ' field_order='MDY' default_all=true add_time=true time_separator='auction.time_separator'}
                            </div>
                        </div>
                    </div>

                    <div id="ynauction_customfield" class="table form-group">
                        <h3>{phrase var='addition_information'}</h3>
                        <div id="ynauction_customfield_user">
                            {if isset($aForms) && isset($aForms.all_customfield_user) && count($aForms.all_customfield_user)}
                            {foreach from=$aForms.all_customfield_user key=keyall_customfield_user item=itemall_customfield_user}
                            <div class="table_right">
                                <div class="ynauction-customfield-user form-group">
                                    <div class="table_left">
                                        <label>{phrase var='title'}: </label>
                                    </div>
                                    <div class="table_right">
                                        <input class="form-control" type="text" name="val[customfield_user_title][]" size="60" value="{$itemall_customfield_user.usercustomfield_title}" />
                                        <div class="extra_info">
                                            {if $keyall_customfield_user == 0}
                                            <a id="ynauction_add" href="javascript:void(0)" onclick="ynauction.appendPredefined(this,'customfield_user'); return false;">
                                                {img theme='misc/add.png' class='v_middle'}
                                            </a>
                                            <a id="ynauction_delete" style="display: none;" href="javascript:void(0)" onclick="ynauction.removePredefined(this,'customfield_user'); return false;">
                                                <img src="{$corepath}module/auction/static/image/delete.png" class="v_middle"/>
                                            </a>
                                            {else}
                                            <a id="ynauction_delete" href="javascript:void(0)" onclick="ynauction.removePredefined(this,'customfield_user'); return false;">
                                                <img src="{$corepath}module/auction/static/image/delete.png" class="v_middle"/>
                                            </a>
                                            {/if}
                                        </div>
                                    </div>
                                    <div class="table_left">
                                        <label>{phrase var='content'}: </label>
                                    </div>
                                    <div class="table_right">
                                        <input class="form-control" type="text" name="val[customfield_user_content][]" size="60" value="{$itemall_customfield_user.usercustomfield_content}" />
                                    </div>
                                </div>
                            </div>
                            {/foreach}
                            {else}
                            <div class="ynauction-customfield-user">
                                <div class="table_left">
                                    <label>{phrase var='title'}: </label>
                                </div>
                                <div class="table_right">
                                    <input class="form-control" type="text" name="val[customfield_user_title][]" size="60" />
                                    <div class="extra_info">
                                        <a id="ynauction_add" href="javascript:void(0)" onclick="ynauction.appendPredefined(this,'customfield_user'); return false;">
                                            {img theme='misc/add.png' class='v_middle'}
                                        </a>
                                        <a id="ynauction_delete" style="display: none;" href="javascript:void(0)" onclick="ynauction.removePredefined(this,'customfield_user'); return false;">
                                            <img src="{$corepath}module/auction/static/image/delete.png" class="v_middle"/>
                                        </a>
                                    </div>
                                </div>
                                <div class="table_left">
                                    <label>{phrase var='content'}: </label>
                                </div>
                                <div class="table_right">
                                    <input class="form-control" type="text" name="val[customfield_user_content][]" size="60" />
                                </div>
                            </div>
                            {/if}
                        </div>
                    </div>

                    {if empty($sModule) && Phpfox::isModule('privacy')}
                    <h3>{phrase var='privacy'}</h3>
                    <div class="table form-group-follow">
                        <div class="table_left">
                            <label for="view_privacy">{phrase var='view_privacy'}:</label>
                        </div>
                        <div class="table_right">
                            {module name='privacy.form' privacy_name='privacy' privacy_info='auction.control_who_can_see_this_auction' privacy_no_custom=true}
                        </div>
                    </div>
                    <div class="table form-group-follow">
                        <div class="table_left">
                            <label for="photo_privacy">{phrase var='photo_privacy'}:</label>
                        </div>
                        <div class="table_right">
                            {module name='privacy.form' privacy_name='privacy_photo' privacy_info='auction.control_who_can_see_photos_of_this_auction' privacy_no_custom=true}
                        </div>
                    </div>
                    <div class="table form-group-follow">
                        <div class="table_left">
                            <label for="video_privacy">{phrase var='video_privacy'}:</label>
                        </div>
                        <div class="table_right">
                            {module name='privacy.form' privacy_name='privacy_video' privacy_info='auction.control_who_can_see_videos_of_this_auction' privacy_no_custom=true}
                        </div>
                    </div>
                    {/if}
                    <div class="table form-group-follow">
                        <div class="table_left"></div>
                        <div class="table_right">
                            <input type="checkbox" name="val[is_receive_notification]" {if isset($aForms) && isset($aForms.receive_notification_someone_bid) && $aForms.receive_notification_someone_bid }checked{/if} > {phrase var='receive_notification_when_anyone_bid_this_auction'}
                        </div>
                    </div>
                    <div class="table form-group-follow">
                        <div class="table_right">
                            <label>{phrase var='publishing_fee_new'}: {$iDefaultPublishFee} {$aCurrentCurrencies.0.currency_id}</label>
                        </div>
                    </div>
                    {if Phpfox::getUserParam('auction.can_feature_auction')}
                    <div class="table form-group-follow">
                        <div class="table_left">
                            <label>{phrase var='feature'}:</label>
                        </div>
                        <div class="table_right">
                            {phrase var='feature_this_auction_for'} <input id="ynauction_feature_number_days" type="text" name="val[feature_number_days]" value="0" size="10"> {phrase var='day_s_with'} <input id="ynauction_feature_fee_total" type="text" value="0" size="10" readonly /> {$aCurrentCurrencies.0.currency_id}
                            <div class="extra_info">({phrase var='fee_to_feature_auction_feature_fee_currency_id_for_1_day' feature_fee=$iDefaultFeatureFee currency_id=$aCurrentCurrencies.0.currency_id})</div>
                        </div>
                    </div>
                    {/if}
                    <div class="table form-group-follow">
                        <div class="table_left"></div>
                        <div class="table_right">
                            <label>{phrase var='total_fee_new'}: <span id="ynauction_text_defaultpublishfee">{$iDefaultPublishFee}</span> {$aCurrentCurrencies.0.currency_id}</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table_clear">
                <button id="ynauction_submit" type="submit" class="btn btn-sm btn-primary" name="val[draft]" onclick="$Core.reloadPage();">{phrase var='save'}</button>
                <button id="ynauction_submit" type="submit" class="btn btn-sm btn-primary" name="val[publish]" onclick="$Core.reloadPage();">{phrase var='publish'}</button>
                <button id="ynauction_submit" onclick="location.href='{$sBackUrl}';" type="button" class="btn btn-sm btn-default" name="val[cancel]">{phrase var='cancel'}</button>
            </div>
        </form>
    </div>
    {if Phpfox::getParam('core.display_required')}
    <div class="table_clear">
        {required} {phrase var='core.required_fields'}
    </div>
    {/if}
    {/if}
</div>

{if !isset($invoice_id)}
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?v=3.exp&libraries=places"></script>
{/if}

{if PHPFOX_IS_AJAX}
{literal}
<script type="text/javascript">
    $Behavior.globalInit();
    ynauction.initAdd();
</script>
{/literal}
{/if}


