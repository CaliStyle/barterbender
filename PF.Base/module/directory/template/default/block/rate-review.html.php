<?php
?>
<script type="text/javascript" src="{$core_url}static/jscript/jquery/plugin/star/jquery.rating.js"></script>
<link rel="stylesheet" type="text/css" href="{$core_url}static/jscript/jquery/plugin/star/jquery.rating.css" />

<div id="js_rating_holder_{$aRatingCallback.type}">
    <form id="form-rating" method="post">
        <div id="js_alert_title" class="alert alert-danger" style="display: none;">
            {phrase var='you_have_to_input_title'}
        </div>
        <div id="js_alert_content" class="alert alert-danger" style="display: none;">
            {phrase var='you_have_to_input_content'}
        </div>
        <div class="alert alert-danger" style="display: none;">
            {phrase var='You have to rate this business'}
        </div>

        <input type="hidden" name="action" id="form-rating-action" value="{if isset($aOldReview)}editReviewBusiness{else}reviewBusiness{/if}">
        <input type="hidden" name="rating[type]" value="{$aRatingCallback.type}"/>
        <input type="hidden" name="rating[business_id]" value="{$aRatingCallback.item_id}"/>
        <div class="space-bottom-small"><strong>{phrase var="directory.title"}: </strong></div>
        <div class="space-bottom-medium">
            <input required type="text" class="form-control" cols="61" name="rating[title]" id="js_yndirectory_title_review" placeholder="{_p var = 'write_your_review_title'}..."
                   value="{if isset($aOldReview.title)}{$aOldReview.title}{/if}">
        </div>

        <div class="space-bottom-small"><strong>{phrase var="directory.content"}: </strong></div>
        <div class="space-bottom-medium">
            <textarea required class="form-control mb-0" cols="61" name="rating[content]" id="js_yndirectory_content_review" value="" placeholder="{_p var = 'write_your_review_content'}...">{if isset($aOldReview.content)}{$aOldReview.content}{/if}</textarea>
        </div>
        {if isset($aOldReview.total_score_text)}
        <div class="space-bottom-small" style="float: left;"><strong>{phrase var='old_rating'}: </strong></div>
        <div class="space-bottom-medium">
            <div class="ync-outer-rating ync-outer-rating-row mini ync-rating-sm">
                <div class="ync-outer-rating-row">
                    <div class="ync-rating-star">
                        {for $i = 0; $i < 10; $i+=2}
                        {if $i < (int)$aOldReview.rating}
                        <i class="ico ico-star" aria-hidden="true"></i>
                        {elseif ((round($aOldReview.rating) - $aOldReview.rating) > 0) && ($aOldReview.rating - $i) > 0}
                        <i class="ico ico-star half-star" aria-hidden="true"></i>
                        {else}
                        <i class="ico ico-star disable" aria-hidden="true"></i>
                        {/if}
                        {/for}
                    </div>
                </div>
            </div>
        </div>
        {/if}
        <div class="clear"></div>
        <div class="yndirectory-popup-review-footer">
            <div class="yndirectory-rating-star-action" style="height:18px; position: relative; float: left;">
                <div style="position:absolute; width: 200px; margin-top: 2px;">
                    {foreach from=$aRatingCallback.stars key=sKey item=sPhrase}
                    <input type="radio" class="js_rating_star" id="js_rating_star_{$sKey}" name="rating_star"
                           value="{$sKey}" title="{$sKey}{if $sPhrase != $sKey} ({$sPhrase}){/if}" {if
                           $aRatingCallback.default_rating>= $sKey} checked="checked"{/if} />
                    {/foreach}
                    <div class="clear"></div>
                </div>

            </div>
            <div class="yndirectory-button-reviews t_right">
                <input type="hidden" name="rating[star]" id="ynd_rating_star_submit" value=""/>
                <input type="reset" onclick="return js_box_remove(this);" value="{phrase var="directory.cancel"}" class="button btn-default btn-sm" />
                <button type="submit" id="rating" onclick="return addStarValue();" value="{phrase var=" directory.submit
                "}" class="btn btn-sm btn-primary">{phrase var="directory.review"}</button>
            </div>
        </div>
        
    </form>
</div>
{literal}
<script language="javascript" type="text/javascript">
    function addStarValue() {
        var totalstar = 0;
        $('div').find('.star-rating-on').each(function () {
            totalstar = totalstar + 1;
        });
        totalstar = totalstar * 2;
        $('#ynd_rating_star_submit').val(totalstar);
    }

    $Behavior.init_ync_business_review = function () {
        $('.js_rating_star').rating();
        $('#form-rating').submit(function(){
            addStarValue();
            var $action_ele = $(this).find('#form-rating-action'),
                $form = $(this),
                $danger = $form.find('.alert-danger'),
                $danger1 = $form.find('#js_alert_title'),
                $danger2 = $form.find('#js_alert_content');
            if ($action_ele.length > 0 && $('#ynd_rating_star_submit').val() > 0 && $.trim($('#js_yndirectory_title_review').val()) && $.trim($('#js_yndirectory_content_review').val())) {
                var action = $action_ele.val();
                $form.ajaxCall('directory.' + action);
            }
            else {
                if($('#ynd_rating_star_submit').val() == 0) {
                    $danger.slideDown();
                }else {
                    $danger.slideUp();
                }
                if(!$.trim($("#js_yndirectory_title_review").val())) {
                    $danger1.slideDown();
                }else {
                    $danger1.slideUp();
                }
                if(!$.trim($("#js_yndirectory_content_review").val())) {
                    $danger2.slideDown();
                }else {
                    $danger2.slideUp();
                }
            }
            return false;
        });
    }
 </script>
 
<style type="text/css">
	div.rating-cancel, 
	div.star-rating,
	div.rating-cancel a, 
	div.star-rating a{
		cursor: pointer;
	}	
</style>
 {/literal}
 
 
 