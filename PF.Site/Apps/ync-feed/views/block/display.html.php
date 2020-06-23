<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{if !$bIsHashTagPop && !PHPFOX_IS_AJAX && !empty($sIsHashTagSearch)}
  <h1 id="sHashTagValue">#{$sIsHashTagSearchValue|clean}</h1>
{/if}

{plugin call='feed.component_block_display_process_header'}
{if Phpfox::isUser() && !defined('FEED_LOAD_MORE_NEWS') && !defined('FEED_LOAD_NEW_FEEDS') && (!isset($bIsGroupMember) || $bIsGroupMember) && !(isset($aFeedCallback.disable_share) && $aFeedCallback.disable_share) && !$bIsFilterPosts}
  {template file='ynfeed.block.form'}
{/if}
<div id="js_new_feed_update"></div>



<!--IF-->
{if isset($bForceFormOnly) && $bForceFormOnly}
{else}
  <!--If 1-->
  {if Phpfox::isUser() && !PHPFOX_IS_AJAX && $sCustomViewType === null && $bUseFeedForm}
    <div id="js_main_feed_holder">
    </div>
  {/if}
  <!--End if 1-->


  <!--If 2-->
  {if Phpfox::isUser() && !defined('PHPFOX_IS_USER_PROFILE') && !PHPFOX_IS_AJAX && !defined('PHPFOX_IS_PAGES_VIEW') && empty($aFeedCallback.disable_sort)}
  {if count($aFilters)}
  <ul class="ynfeed_filters">
    <!--For 1-->
    {for $i=0; $i < min(count($aFilters), $iNumberShownFilter); $i++}
    <li class="ynfeed_filter {if $aFilterRequests.filter_type == $aFilters[$i].type}active{/if}" data-type="{$aFilters[$i].type}">
      <a href="javascript:void(0);" onclick="$Core.ynfeed.prepareFiltering(this, 'ynfeed.reload', '{if defined('PHPFOX_IS_USER_PROFILE') && isset($aUser.user_id)}&profile_user_id={$aUser.user_id}{/if}{if isset($aFeedCallback.module)}&callback_module_id={$aFeedCallback.module}&callback_item_id={$aFeedCallback.item_id}{/if}&year={$sTimelineYear}&month={$sTimelineMonth}{if !empty($sIsHashTagSearch)}&hashtagsearch={$sIsHashTagSearch}{/if}&filter-id={$aFilters[$i].filter_id}&filter-module={$aFilters[$i].module_id}&filter-type={$aFilters[$i].type}');">
        {_p var=$aFilters[$i].title}
      </a>
    </li>
    {/for}
    <!--End for 1-->
    <div class="dropdown ynfeed_filter_dropdown">
      <span class="dropdown-toggle ynfeed_filter ynfeed_filter_more" data-toggle="dropdown">{_p('More')} <i class="fa fa-caret-down" aria-hidden="true"></i></span>
      <ul class="dropdown-menu dropdown-menu-right">
        <!--For 2-->
        {for $i=$iNumberShownFilter; $i < count($aFilters); $i++}
        <li class="ynfeed_filter_more_item {if $aFilterRequests.filter_type == $aFilters[$i].type}active{/if}" data-type="{$aFilters[$i].type}">
          <a href="javascript:void(0);"
             onclick="$Core.ynfeed.prepareFiltering(this, 'ynfeed.reload', '{if defined('PHPFOX_IS_USER_PROFILE') && isset($aUser.user_id)}&profile_user_id={$aUser.user_id}{/if}{if isset($aFeedCallback.module)}&callback_module_id={$aFeedCallback.module}&callback_item_id={$aFeedCallback.item_id}{/if}&year={$sTimelineYear}&month={$sTimelineMonth}{if !empty($sIsHashTagSearch)}&hashtagsearch={$sIsHashTagSearch}{/if}&filter-id={$aFilters[$i].filter_id}&filter-module={$aFilters[$i].module_id}&filter-type={$aFilters[$i].type}');">{_p var=$aFilters[$i].title}</a>
        </li>
        {/for}
        <!--End for 2-->
        <li class="ynfeed_filter_more_item ynfeed_settings">
          <a href="javascript:void(0)" onclick="tb_show('{_p('manage_hidden')}', $.ajaxBox('ynfeed.manageHidden', ''));">
            <i class="fa fa-cog" aria-hidden="true"></i>{_p('manage_hidden')}
          </a>
        </li>
      </ul>
    </div>
  </ul>
  {/if}
  <div class="ynfeed_sort_order feed_sort_order">
    <a href="#" class="ynfeed_sort_order_link feed_sort_order_link">{_p var='sort'} <span class="caret"></span></a>
    <div class="ynfeed_sort_holder open">
      <ul class="dropdown-menu dropdown-menu-right">
        <li><a href="#"{if !$iFeedUserSortOrder} class="active"{/if} rel="0">{_p var='top_stories'}</a></li>
        <li><a href="#"{if $iFeedUserSortOrder} class="active"{/if} rel="1">{_p var='most_recent'}</a></li>
      </ul>
    </div>
  </div>
  {/if}
  <!--End if 2-->


  <!--If 3-->
  {if Phpfox::isModule('captcha') && Phpfox::getUserParam('captcha.captcha_on_comment')}
  {module name='captcha.form' sType='comment' captcha_popup=true}
  {/if}
  <!--End if 3-->

  <!--If 4-->
  {if !PHPFOX_IS_AJAX && !defined('FEED_LOAD_NEW_FEEDS') && !defined('FEED_LOAD_MORE_NEWS') && !$bIsFilterPosts}
  <div id="feed"><a name="feed"></a></div>
  <!--Feeds content-->
  <div id="ynfeed_filtering">
    <i class="fa fa-spin fa-spinner" aria-hidden="true"></i>
  </div>
  <div id="js_feed_content" class="js_feed_content">
    <!--If 4.1-->
    {if $sCustomViewType !== null}
    <h2>{$sCustomViewType}</h2>
    {/if}
    <!--End if 4.1-->

    <div id="js_new_feed_comment"></div>
  {/if}
    <!--End if 4-->



    <script type="text/javascript">
      /*set filter param*/
      ynfeed_filter_id = {$aFilterRequests.filter_id};
      ynfeed_filter_type = '{$aFilterRequests.filter_type}';
      ynfeed_filter_module = '{$aFilterRequests.filter_module}';
    </script>

    <!--If 5-->
    {if isset($bStreamMode) && $bStreamMode}
      {foreach from=$aFeeds item=aFeed}
        <!--If 5.1-->
        {if isset($aFeed.sponsored_feed) || $aFeed.feed_id != $iSponsorFeedId}
        <div class="feed_stream" data-feed-url="{if (isset($aFeedCallback.module))}{url link='feed.stream' id=$aFeed.feed_id module=$aFeedCallback.module item_id=$aFeedCallback.item_id}{else}{url link='feed.stream' id=$aFeed.feed_id}{if isset($aFeed.sponsored_feed)}&sponsor=1{/if}{/if}"></div>
        {/if}
        <!--Endif 5.1-->
      {/foreach}
    {else}
      <!--If 5.2-->
      {if isset($bNoLoadFeedContent)}
      {else}
        {foreach from=$aFeeds name=iFeed item=aFeed}
          {if isset($aFeed.sponsored_feed) || $aFeed.feed_id != $iSponsorFeedId}
            {if isset($aFeed.feed_mini) && !isset($bHasRecentShow)}
              {if $bHasRecentShow = true}{/if}
              <div class="activity_recent_holder">
                <div class="activity_recent_title">
                  {_p var='recent_activity'}
                </div>
              {/if}
              {if !isset($aFeed.feed_mini) && isset($bHasRecentShow)}
              </div>
              {unset var=$bHasRecentShow}
              {/if}

              <div class="js_feed_view_more_entry_holder">
                {template file='ynfeed.block.entry'}
                {if isset($aFeed.more_feed_rows) && is_array($aFeed.more_feed_rows) && count($aFeed.more_feed_rows)}
                  {foreach from=$aFeed.more_feed_rows item=aFeed}
                    {if $bChildFeed = true}{/if}
                    <div class="js_feed_view_more_entry" style="display:none;">
                      {template file='ynfeed.block.entry'}
                    </div>
                  {/foreach}
                  {unset var=$bChildFeed}
            {/if}
              </div>
          {/if}
        {/foreach}
      {/if}
      <!--Endif 5.2-->
  {/if}
    <!--Endif 5-->
<!--If 7-->
{if $sCustomViewType === null && !defined('FEED_LOAD_NEW_FEEDS')}
  <!--IF 7.1-->
  {if !defined('PHPFOX_IN_DESIGN_MODE')}
    {if count($aFeeds) || (isset($bForceReloadOnPage) && $bForceReloadOnPage)}
      {if !(defined('FEED_LOAD_NEW_NEWS') && FEED_LOAD_NEW_NEWS) }
      <div id="feed_view_more">
        {if $bIsHashTagPop}
          {if count($aFeeds) > 8}
          <a href="{url link='hashtag'}{$sIsHashTagSearch}/page_1/" class="global_view_more no_ajax_link" style="display:block;">{_p var='view_more'}</a>
          {/if}
        {else}
        <div id="js_feed_pass_info" style="display:none;">page={$iFeedNextPage}{if defined('PHPFOX_IS_USER_PROFILE') && isset($aUser.user_id)}&profile_user_id={$aUser.user_id}{/if}{if isset($aFeedCallback.module)}&callback_module_id={$aFeedCallback.module}&callback_item_id={$aFeedCallback.item_id}{/if}&year={$sTimelineYear}&month={$sTimelineMonth}{if !empty($sIsHashTagSearch)}&hashtagsearch={$sIsHashTagSearch}{/if}</div>
        <div id="feed_view_more_loader"><i class="fa fa-spin fa-spinner" aria-hidden="true"></i></div>
        <a
            href="{if Phpfox_Module::instance()->getFullControllerName() == 'core.index-visitor'}{url link='core.index-visitor' page=$iFeedNextPage}{else}{url link='current' page=$iFeedNextPage}{/if}"
            onclick="$(this).hide(); $('#feed_view_more_loader').show();var oLastFeed = $('.js_parent_feed_entry').last();var iLastFeedId = (oLastFeed) ? oLastFeed.attr('id') : null; $.ajaxCall('ynfeed.viewMore', 'page={$iFeedNextPage}{if defined('PHPFOX_IS_USER_PROFILE') && isset($aUser.user_id)}&profile_user_id={$aUser.user_id}{/if}{if isset($aFeedCallback.module)}&callback_module_id={$aFeedCallback.module}&callback_item_id={$aFeedCallback.item_id}{/if}&year={$sTimelineYear}&month={$sTimelineMonth}{if !empty($sIsHashTagSearch)}&hashtagsearch={$sIsHashTagSearch}{/if}&last-feed-id='+iLastFeedId+'&filter-id={$aFilterRequests.filter_id}&filter-module={$aFilterRequests.filter_module}&filter-type={$aFilterRequests.filter_type}', 'GET'); return false;"
            class="global_view_more no_ajax_link">
            {_p var='view_more'}
        </a>

        {/if}
      </div>
      {/if}
    {else}
        {if !defined('FEED_LOAD_NEW_FEEDS') }
        <div class="message js_no_feed_to_show">{_p var='there_are_no_new_feeds_to_view_at_this_time'}</div>
        {/if}
    {/if}
  {/if}
  <!--End if 7.1-->
{/if}
<!--End if 7-->

<!--If 8-->
  {if !PHPFOX_IS_AJAX || (PHPFOX_IS_AJAX && count($aFeedVals))}
  </div>
  {/if}
<!--End if 8-->

<!--IF 9-->
  {if Phpfox::getParam('feed.refresh_activity_feed') > 0}
  <script type="text/javascript">
    window.$iCheckForNewFeedsTime = {param var="feed.refresh_activity_feed"};
  </script>
  {/if}
<!--Endif 9-->
{/if}

<!--END-->

<script type="text/javascript">
  $Behavior.hideEmptyActionList = function() {l}
    $('.feed_options_holder ul.dropdown-menu').each(function() {l}
      if ($(this).find('li').length == 0) {l}
        oParent = $(this).parent('.feed_options_holder');
        if (oParent) {l}
            oParent.find('a.feed_options:first').hide();
        {r}
      {r}
    {r});
  {r};
</script>
