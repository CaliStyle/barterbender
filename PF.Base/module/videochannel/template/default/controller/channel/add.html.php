<?php

defined('PHPFOX') or exit('NO DICE!');

?>
{if !isset($bIsLimited)}
<div id="TB_ajaxContent"></div>
{template file='videochannel.block.channel.url'}
<form id="js_form" name="js_form" method="post" action="{$sSubmitUrl}" onsubmit="return findChannels();">
    {if isset($currIndex)} <input type="hidden" name="val[sPageToken]" value="{$sPageToken}"/> {/if}
    <!-- Search form -->
    {literal}
    <script type="text/javascript">
    $Behavior.VideoChannelAddChannel = function() {
            $(document).ready(function(){
                    $('input#keyword').keydown(function(event) {
                            if (event.keyCode == '13') {
                                    event.preventDefault();
                                    $('#find_channels').click();
                            }
                    });
            });
    }
    </script>
    {/literal}
    <div class="panel panel-default">
         <div id="search_channel">
             <div class="panel-body">
                 <div class="form-group">
                    <label>{phrase var='videochannel.keywords'}: </label>
                    <input id="keyword" name="val[keyword]" type="text" class="form-control" size="40" value="{$sKeyword}" onfocus="$('#channel_error').hide()"/>
                    <div id="channel_error" style="display:none; margin-top: 10px;" class="error_message">{phrase var='videochannel.enter_keywords_to_search_channels'}</div>
                 </div>
             </div>
             <div class="panel-footer">
                <input id="find_channels" name="find_channels" class="btn btn-sm btn-primary" type="submit" value="{phrase var='videochannel.find_channels'}"/>
            </div>
        </div>
    </div>
	 <div id="search_channel_loading" style="display: none">
	{phrase var='videochannel.searching_channels'} &nbsp; &nbsp;
	{img theme='ajax/add.gif' id='channel_loading'}
	 </div>
<!-- End Search form -->

    <!-- Search Result -->
    <div id='search_channel_results'>
    {if isset($aChannels)}
        <div id="channel_entry_block">
             <h1>{phrase var='core.search_results_for'} '{$sKeyword|clean}'</h1>
             {if !count($aChannels)}
            {phrase var='videochannel.no_channels_found'}
             {else}
             {foreach from=$aChannels key=count item=channel}
             {template file='videochannel.block.channel.entry'}
             {/foreach}
             <div class="pager_outer">
            <ul class="pager">
                 {if !empty($sPageTokenPrev) }
                 <input type="submit" id="prev_channels" name="prev_channels" value="{$sPageTokenPrev}"/>
                 <li class="first" ><a href="javascript:void(0);" onclick="$('#prev_channels').click();" >{phrase var='core.previous'}</a></li>
                 {/if}
                 {if !empty($sPageTokenNext) }
                 <input type="submit" id="next_channels" name="next_channels" value="{$sPageTokenNext}"/>
                 <li class="first" ><a href="javascript:void(0);" onclick="$('#next_channels').click();" >{phrase var='core.next'}</a></li>
                 {/if}
            </ul>
             </div>
             {/if}
        </div>
    {/if}<!-- End Search Result -->
    </div>
</form>
{/if}
