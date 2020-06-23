<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{$sJs}

{if $bIsUserTimeLine}
{literal}
<script language="javascript" type="text/javascript">
    $Behavior.addCustomSubMenus = function(){
        $('#section_menu ul').last().append($('.buton_on_top_right_for_timeline').html());
        $('.buton_on_top_right_for_timeline').html('');
    }





</script>
{/literal}
<ul class="buton_on_top_right_for_timeline" style="display: none;">
    {foreach from=$aCustomSubMenus key=iKey name=submenu item=aSubMenu}
    <li>
        <a href="{url link=$aSubMenu.url)}" class="ajax_link">
            {if $aSubMenu.showAddButton}
                {img theme='layout/section_menu_add.png' class='v_middle'}
            {/if}
            {$aSubMenu.phrase}
        </a>
    </li>
    {/foreach}
</ul>
{/if}

{if isset($aSlideShowVideos) && isset($aVideos)}

{if $aSlideShowVideos}

<style type="text/css">
.slide_info{l}
    background:url({$sCorePath}module/videochannel/static/image/black50.png);
{r}
</style>
<div class="block" id="yn_slide_show_block">
    <div class="title">{phrase var='videochannel.featured_videos'}</div>
    <div class="border">
        <div class="content">
      {if $error_folder}
      <div class='block'>
        <strong style="color:red">{$error_folder}</strong>
      </div>
      {/if}


            <div style="opacity: 0;filter: alpha(opacity = 0);" id="jhslider" class="jhslider">
        <ul>
         {foreach from=$aSlideShowVideos item=aVideo name=af}

         {*<a href="{permalink module='videochannel' id=$aVideo.video_id title=$aVideo.title}">*}
          <li>
            <div class="jhslider-info-detail">
              <a href="{permalink module='videochannel' id=$aVideo.video_id title=$aVideo.title}"

                  >
              <strong style="text-transform:uppercase;">{$aVideo.title|clean|shorten:60:"...":false}</strong></a>
              {*<div class="highlight"> {$aVideo.text|clean|shorten:150:"...":false}</div>*}
              <div> {$aVideo.total_view} {phrase var='videochannel.views'} - {phrase var='videochannel.by_lowercase'}: {$aVideo|user|shorten:20:'...'|split:20}</div>

            </div>
            <div class="jhslider-info-quick">
              {img
              class="dont-unbind"
              server_id=$aVideo.image_server_id title=''
              path='core.url_pic'
              file=$aVideo.image_path
              suffix='_120'
              onerror=$sImageOnError}
            </div>

              {img
              class="big-image dont-unbind"
              thickbox=true
              server_id=$aVideo.image_server_id
              title=$aVideo.title
              path='core.url_pic'
              file=$aVideo.image_path
              suffix='_480'
              onerror=$sImageOnError}
          </li>

        {*</a>*}
        {/foreach}
        </ul>

        {literal}
        <script type="text/javascript" language="javascript">

            $Behavior.setupSlideShowVideo = function() {
                //page_videochannel_index
                if ($('#page_videochannel_index').length)
                {
                    console.log("$Behavior.setupSlideShowVideo");
                    setTimeout(function(){
                        if($(".jhslider").hasClass("init-ed")) {
                            return false;
                        }

                        $(".jhslider").unbind();
                        $(".jhslider").children().unbind();


                        $(".jhslider-info-quick img").each(function(index, value) {
                            $(this).attr('src',$(this).data("src"));
                        });


                        $(".big-image").each(function(index, value) {
                            $(this).attr('src',$(this).data("src"));
                        });

                        $(".jhslider").css({
                            "opacity": "1"
                        });

                        $(".jhslider").JHSlide(5000, 400);

                        $(".jhslider").addClass("init-ed");
                        $(".jhslider-buff").each(function(index, value) {
                            $(this).addClass('dont-unbind');
                        });

                        $(".jhslider-thumbnail-div").each(function(index, value) {
                            $(this).addClass('dont-unbind');
                        });

                    }, 100);
                };


                $Behavior.VideoChannelLoadingSlideShow = function(){

                    $(window).load(function() {
                        var my_regex = /static\/image\/noimage/;
                        $('.big-image').each(
                            function(index, dom){
                                if($(dom).width() == 100 || $(dom).width() == 120)
                                {
                                    $(dom).attr('src', $(dom).attr('onerror'));
                                }
                            }
                        );
                    });

                }

            }
        </script>
        {/literal}

      </div>
    </div>
    </div>
</div>

{/if}

{/if}



{if $sSortTitle}
  <div class='block'>
    <div class="title mb-2">{$sSortTitle}</div>

{/if}


<div id="TB_ajaxContent"></div>
{if isset($aChannels)}
    {if !count($aChannels) && $current_page <= 1}
    <div class="extra_info">
      {phrase var='videochannel.no_channels_found'}
    </div>
    {else}
      {foreach from=$aChannels key=count item=channel}
      {template file='videochannel.block.channel.entry'}
      {/foreach}
      <div class="clear"></div>
      {if (Phpfox::getUserParam('videochannel.can_add_channels')) || ($bCanAddChannelInPage)}
      {moderation}
      {/if}
      {if count($aChannels)}
      {pager}
      {/if}
    {/if}
{/if}
{if $current_page == 1}
<input type="hidden" id="daclear" value="0">
{literal}
<script type="text/javascript">
  $Behavior.clearcookiealla =function()
  {
    // khong in ra nhung van chay.
    if ($('#daclear').val()=="0")
    {
      $Core.moderationLinkClear();
      $('#daclear').val("1");

    }

  }
</script>
{/literal}
{/if}


{if isset($aVideos)}
    {if !count($aVideos) && $current_page <= 1}
    <div class="extra_info">
      {phrase var='videochannel.no_videos_found'}
    </div>
    {else}

    {if $current_page <= 1}
    <div id="js_video_edit_form_outer" style="display:none;">
      <form method="post" action="#" onsubmit="$(this).ajaxCall('videochannel.viewUpdate'); return false;">
        <div id="js_video_edit_form"></div>
        <div class="table_clear">
          <ul class="table_clear_button">
            <li><input type="submit" value="{phrase var='videochannel.update'}" class="button btn-primary" /></li>
            <li><a href="#" id="js_video_go_advanced" class="button button_off">{phrase var='videochannel.go_advanced_uppercase'}</a></li>
            <li><a href="#" onclick="$('#js_video_edit_form_outer').hide(); $('#js_video_outer_body').show(); return false;" class="button button_off">{phrase var='videochannel.cancel_uppercase'}</a></li>
          </ul>
          <div class="clear"></div>
        </div>
      </form>
    </div>

    <div class="js_video_outer_body">
    {/if}
      {foreach from=$aVideos name=videos item=aVideo}
        {template file='videochannel.block.entry'}
      {/foreach}
      {if count($aVideos)}
      {pager}
      {/if}
    {if $current_page <= 1}
      <div class="clear"></div>
          {if Phpfox::getUserParam('videochannel.can_approve_videos') || Phpfox::getUserParam('videochannel.can_delete_other_video') }
            {moderation}
          {/if}
    </div>
    {/if}
    {/if}
{/if}
{if $sSortTitle}
	</div>
{/if}

