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
{if !count($aFeedbacks)}
<div class="extra_info">
	{_p var='no_feedback_found'}
	<ul class="action">
		<li>
                {if !Phpfox::getUserId()}
                    <a  href="#" onclick="tb_show('{_p var='create_new_feedback' phpfox_squote=true}', $.ajaxBox('feedback.addFeedBack', 'height=400&amp;width=750'))" class="inlinePopup">{_p var='be_the_first_to_add_a_feedback'}</a>

                {else}
                    <a  href="#" onclick="tb_show('{_p var='create_new_feedback' phpfox_squote=true}', $.ajaxBox('feedback.addFeedBack', 'height=400&amp;width=750'))" class="inlinePopup">{_p var='be_the_first_to_add_a_feedback'}</a>
                {/if}
                </li>
	</ul>
</div>
{else}
{foreach from=$aFeedbacks name=fb item=aFeedback}
<div class="{if is_int($phpfox.iteration.fb/2)}row1{else}row2{/if}{if $phpfox.iteration.fb == 1} row_first{/if}"{if $phpfox.iteration.fb == 1} style="padding-top:0px;"{/if}>
	<div class="go_left" style="width:52px;">
		{img user=$aFeedback max_width=50 max_height=50 suffix='_50' class='v_middle'}
	</div>
	<div style="margin-left:54px;">
		<a href="{url link='feedback.detail/'$aFeedback.title_url'}">{$aFeedback.title|clean}</a>
		<div class="extra_info">
			{$aFeedback.posted_on}
		</div>
	</div>
	<div class="clear"></div>
</div>
{/foreach}
{/if}                  
<script type="text/javascript">
    //Phpfox.init();
</script>
