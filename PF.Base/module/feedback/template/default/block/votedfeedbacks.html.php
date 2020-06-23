<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FeedBack
 * @version          2.01
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>
{literal}


{*<script type="text/javascript">
$(document).ready(function() {
	$(".tip_trigger").hover(function(){
		tip = $(this).parent().find('.tip_show');
		tip.show(); //Show tooltip
    $('.tip_show').css("z-index", 100000);
    $('.tip_show').css("position", "absolute");
	}, function() {
		tip.hide(); //Hide tooltip
	}).mousemove(function(e) {
		var mousex = e.pageX + 20; //Get X coodrinates
		var mousey = e.pageY + 20; //Get Y coordinates
		var tipWidth = tip.width(); //Find width of tooltip
		var tipHeight = tip.height(); //Find height of tooltip

		//Distance of element from the right edge of viewport
		var tipVisX = (mousex);
		//Distance of element from the bottom of viewport
		var tipVisY = $(window).height() - (mousey);

		if ( tipVisX < 20 ) { //If tooltip exceeds the X coordinate of viewport
			mousex = e.pageX;
		} if ( tipVisY < 20 ) { //If tooltip exceeds the Y coordinate of viewport
			mousey = e.pageY - tipHeight -10;
		}
		tip.css({  top: mousey, left: mousex});
    tip.css("display:inline-block");
	});
});
</script>*}
{/literal}
{if count($votedFeedBacks) > 0}
	<ul class="action">
   {foreach from=$votedFeedBacks item=aFeedBack}
     	<li class="most_voted_feedback clearfix">
     	<div class="item-media">
         	{if $aFeedBack.user_id != 0}     	  
                {img user=$aFeedBack suffix='_200_square' max_width=50 max_height=50}
            {else}
                <img src="{$aFeedBack.user_image}" style="width: 50px;height: 50px;"/>
            {/if}
        </div>
        <div style="overflow: hidden" class="fb_info">
                <a class="link no_ajax_link" title="{$aFeedBack.title}" target="_parent" href="{url link='feedback.detail'}{$aFeedBack.title_url}">     	    
                	<div class="most_voted_feedback_title">{$aFeedBack.title}</div> 
                </a>	           	          
                    	<p class="extra_info" style="font-size: 13px;">
                            {phrase var="feedback.user_posted_feedback" username=''} {if $aFeedBack.user_id != 0}<span>{$aFeedBack|user}</span>{else}{$aFeedBack.full_name}{/if}<br/>
                    		<i class="fa fa-thumbs-o-up"></i> {$aFeedBack.total_vote}&nbsp;&nbsp;  
                    		<i class="fa fa-commenting-o"></i> {$aFeedBack.total_comment}&nbsp;&nbsp;   
                    		<i class="fa fa-eye"></i> {$aFeedBack.total_view}&nbsp;&nbsp;   
                		</p>
         </div>   
      	</li>
   {/foreach}
   </ul>
	
{else}
{_p var='no_feedback_found'}
{/if}

