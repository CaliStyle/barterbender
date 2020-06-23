<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{literal}
<script type="text/javascript">
    function viewTutorial(ele)
    {
        if(ele)
        {
            if($(ele).html() == oTranslations['socialbridge.view'])
            {
                $('#'+ele.rel).slideDown();
                $(ele).html(oTranslations['socialbridge.hide']);
            }
            else
            {
                $('#'+ele.rel).slideUp();
                $(ele).html(oTranslations['socialbridge.view']);
            }

        }

    }
</script>
<style type="text/css">
    .ynsocialbridge-tip{
        margin-top: 0px;
    }
    div.tip_tutorial
    {
        padding-bottom:4px;
        border-bottom: 1px solid #CFCFCF;
    }
    div.tip_tutorial ul
    {
        counter-reset: li;
    }
    div.tip_tutorial ul li
    {
        list-style: decimal-leading-zero outside none;
        margin: 0 0 0 26px;
        padding: 4px 0;
        position: relative;
    }
</style>
<script>
    function checkSelected(f){
        var n = $(f).find("input:checked").length;
        if(n <=0)
        {
            alert("{/literal}{phrase var='socialbridge.no_selected_migrate_options'}{literal}");return false;
        }
        else
        {
            if(confirm("{/literal}{phrase var='core.are_you_sure'}{literal}"))
            {
                return true;
            }
        }
        return false;
    }
</script>
{/literal}

{foreach from=$aPublisherProviders index=iKey item=aPublisherProvider}
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">{$aPublisherProvider.title} {phrase var='socialbridge.setting'}</div>
    </div>
    <div class="panel-body">
        <div class="form-group">
            {if $aPublisherProvider.name == 'facebook' }
            <div class="form-group">
                <div class="ynsocialbridge-tip" id="tip_facebook"><a href="javascript:void(0)" onclick="viewTutorial(this);" rel="tip_facebook_tutorial">{phrase var='socialbridge.view'}</a> {phrase var='socialbridge.tutorial_how_to_get_facebook_api_key'}</div>
                <div id="tip_facebook_tutorial" class="tip_tutorial" style="display: none">
                    <ul>
                        {phrase var='socialbridge.to_get_your_facebook_api_id'}
                        <li>{phrase var='socialbridge.for_site_url_you_should_use_this_url'} <strong>{$sCoreUrl}</strong> </li>
                        <li>{phrase var='socialbridge.do_follow_facebook_steps_to_complete_create_new_application'}</li>
                        <li>{phrase var='socialbridge.go_to_the_following_url_select_your_app_and_edit'}</li>
                        <li>{_p var='add_oauth_redirect_uri' link=$redirectFacebookUrl}</li>
                    </ul>
                </div>
            </div>
            <form action="{url link='admincp.socialbridge.providers'}" method="post" enctype="multipart/form-data" id="provider_facebook">
                <div class="form-group">
                    <label for="facebook-id">{phrase var='socialbridge.application_id'}</label>
                    <input type="text" size="40" value="{if isset($aPublisherProvider.params.app_id)}{$aPublisherProvider.params.app_id}{/if}" name="facebook[app_id]" class="form-control" id="facebook-id">
                </div>
                <div class="form-group">
                    <label for="facebook-secret">{phrase var='socialbridge.facebook_secret'}</label>
                    <input type="text" size="60" value="{if isset($aPublisherProvider.params.secret)}{$aPublisherProvider.params.secret}{/if}" name="facebook[secret]" class="form-control" id="facebook-secret">
                </div>

                <div class="form-group">
                    <table>
                        <tbody>
                        <tr>
                            <td>
                                <label for="facebook-enable">{phrase var='socialbridge.enable_facebook_connect'}</label>
                                <div class="item_can_be_closed_holder">
                                    <div class="js_item_is_active"{if !$aPublisherProvider.is_active} style="display:none;"{/if}>
                                        <a href="#?call=socialbridge.activeProvider&amp;id={$aPublisherProvider.service_id}&amp;active=0" class="js_item_active_link" title="{phrase var='rss.deactivate'}"></a>
                                    </div>
                                    <div class="js_item_is_not_active"{if $aPublisherProvider.is_active} style="display:none;"{/if}>
                                        <a href="#?call=socialbridge.activeProvider&amp;id={$aPublisherProvider.service_id}&amp;active=1" class="js_item_active_link" title="{phrase var='rss.activate'}"></a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="form-group">
                    <label for="facebook-image">{phrase var='socialbridge.picture_show_on_facebook'}</label>
                    <input type="file" name="fb_pic" class="form-control" id="facebook-image" />
                    {if !empty($aPublisherProvider.params.pic)}
                    <div style="margin-bottom: 10px; margin-top: 10px">{img path='photo.url_photo' file=$aPublisherProvider.params.pic max_width=100 max_height=100}</div>
                    <label class="checkbox-inline"><input type="checkbox" name="facebook[delete_pic]" value="1" /> {phrase var='core.delete'}</label>
                    {/if}
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="{phrase var='core.submit'}"/>
                </div>
            </form>
            {/if}
            {if $aPublisherProvider.name == 'twitter' }
            <div class="form-group">
                <div class="ynsocialbridge-tip" id="tip_twitter"><a href="javascript:void(0)" onclick="viewTutorial(this);" rel="tip_twitter_tutorial">{phrase var='socialbridge.view'}</a> {phrase var='socialbridge.tutorial_how_to_get_twitter_api_key'}</div>
                <div id="tip_twitter_tutorial" class="tip_tutorial" style="display: none">
                    <ul>
                        {phrase var='socialbridge.to_get_your_twitter_api'}
                        <li>
                            <div class="p_4">{phrase var='socialbridge.for_website_you_should_use_this_url'} <strong>{$sCoreUrl}</strong></div>
                            <div class="p_4">{phrase var='socialbridge.for_callback_url_must_use'} <strong style="color:red;">{$sCallBackUrl}</strong></div>

                        </li>
                        {phrase var='socialbridge.do_follow_twitter_steps_to_complete_create_new_application'}
                    </ul>
                </div>
            </div>
            <form action="{url link='admincp.socialbridge.providers'}" method="post" id="provider_twitter">
                <div class="form-group">
                    <label for="twitter-key">{phrase var='socialbridge.consumer_key'}</label>
                    <input type="text" size="40" value="{if isset($aPublisherProvider.params.consumer_key)}{$aPublisherProvider.params.consumer_key}{/if}" name="twitter[consumer_key]" class="form-control" id="twitter-key">
                </div>
                <div class="form-group">
                    <label for="twitter-secret">{phrase var='socialbridge.consumer_secret'}</label>
                    <input type="text" size="60" value="{if isset($aPublisherProvider.params.consumer_secret)}{$aPublisherProvider.params.consumer_secret}{/if}" name="twitter[consumer_secret]" class="form-control" id="twitter-secret">
                </div>

                <div class="form-group">
                    <table>
                        <tbody>
                        <tr>
                            <td>
                                <label for="twitter-enable">
                                    {phrase var='socialbridge.enable_twitter_connect'}
                                </label>
                                <div class="item_can_be_closed_holder">
                                    <div class="js_item_is_active"{if !$aPublisherProvider.is_active} style="display:none;"{/if}>
                                        <a href="#?call=socialbridge.activeProvider&amp;id={$aPublisherProvider.service_id}&amp;active=0" class="js_item_active_link" title="{phrase var='rss.deactivate'}"></a>
                                    </div>
                                    <div class="js_item_is_not_active"{if $aPublisherProvider.is_active} style="display:none;"{/if}>
                                        <a href="#?call=socialbridge.activeProvider&amp;id={$aPublisherProvider.service_id}&amp;active=1" class="js_item_active_link" title="{phrase var='rss.activate'}"></a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                {if phpfox::isModule('contactimporter')}
                <div class="form-group">
                    <label for="twitter-invite">{phrase var='socialbridge.maximum_invite_per_day'}</label>
                    <input type="text" size="40" value="{if isset($aPublisherProvider.params.maxInvite)}{$aPublisherProvider.params.maxInvite}{else}250{/if}" name="twitter[maxInvite]" class="form-control" id="twitter-invite">
                    <ul style="padding-top: 5px">
                        <li>{phrase var='socialbridge.description_twtitter_maximum_invite_per_day'}</li>
                        <li>{phrase var='socialbridge.viewmore_twtitter_maximum_invite_per_day'}</li>
                    </ul>
                </div>
                {/if}
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="{phrase var='core.submit'}"/>
                </div>
            </form>
            {/if}
            {if $aPublisherProvider.name == 'linkedin' }
            <div class="form-group">
                <div class="ynsocialbridge-tip" id="tip_linkedin"><a href="javascript:void(0)" onclick="viewTutorial(this);" rel="tip_linkedin_tutorial">{phrase var='socialbridge.view'}</a> {phrase var='socialbridge.tutorial_how_to_get_linkedin_api_key'}</div>
                <div id="tip_linkedin_tutorial" class="tip_tutorial" style="display: none">
                    <ul>
                        {phrase var='socialbridge.to_get_your_linkedin_api_key'}
                        <li>{phrase var='socialbridge.for_website_url_you_should_use_this_url_2'} <strong>{$sCoreUrl}</strong></li>
                        <li>{phrase var='socialbridge.for_application_use_should_be_strong_style_color_red_social_aggregation_strong'}</li>
                        <li>{phrase var='socialbridge.for_live_status_should_be_live'}</li>
                        <li>{phrase var='socialbridge.in_authentication_section_find_authorized_redirect_urls_and_add_link'}: <strong>{$sStaticUrl}module/socialbridge/static/php/linkedin.php</strong></li>
                        <li>{phrase var='socialbridge.do_follow_linkedin_steps_to_complete_create_new_application2'}</li>
                    </ul>
                </div>
            </div>
            <form action="{url link='admincp.socialbridge.providers'}" method="post" id="provider_linkedin">
                <div class="form-group">
                    <label for="linkedin-key">{phrase var='socialbridge.api_key'}</label>
                    <input type="text" size="40" value="{if isset($aPublisherProvider.params.api_key)}{$aPublisherProvider.params.api_key}{/if}" name="linkedin[api_key]" class="form-control" id="linkedin-key">
                </div>
                <div class="form-group">
                    <label for="linkedin-secret">{phrase var='socialbridge.secret_key'}</label>
                    <input type="text" size="60" value="{if isset($aPublisherProvider.params.secret_key)}{$aPublisherProvider.params.secret_key}{/if}" name="linkedin[secret_key]" class="form-control" id="linkedin-secret">
                </div>

                <div class="form-group">
                    <table>
                        <tbody>
                        <tr>
                            <td>
                                <label for="linkedin-enable">{phrase var='socialbridge.enable_linkedin_connect'}</label>
                                <div class="item_can_be_closed_holder">
                                    <div class="js_item_is_active"{if !$aPublisherProvider.is_active} style="display:none;"{/if}>
                                        <a href="#?call=socialbridge.activeProvider&amp;id={$aPublisherProvider.service_id}&amp;active=0" class="js_item_active_link" title="{phrase var='rss.deactivate'}"></a>
                                    </div>
                                    <div class="js_item_is_not_active"{if $aPublisherProvider.is_active} style="display:none;"{/if}>
                                        <a href="#?call=socialbridge.activeProvider&amp;id={$aPublisherProvider.service_id}&amp;active=1" class="js_item_active_link" title="{phrase var='rss.activate'}"></a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="{phrase var='core.submit'}"/>
                </div>
            </form>
            {/if}
        </div>
    </div>
</div>
{/foreach}
