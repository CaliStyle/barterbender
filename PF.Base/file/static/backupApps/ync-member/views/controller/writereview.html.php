<div id="js_ajax_compose_error_message"></div>
<div>
    <div class="ynmember_rating_block ynmember-modal">
        <span class="ynmember_rating_stars" data-rating="{$iRating}">
            {$iRating|ynmember_ratingaction:true}
        </span>
    </div>
    <form method="post" action="{url link='ynmember.writereview'}" id="ynmember_js_review_form" onsubmit="return ynmemberSubmitReview()" enctype="multipart/form-data">
        {if $bIsEdit}
            <div><input type="hidden" name="val[review_id]" value="{$aForms.review_id}" /></div>
        {/if}
        <div><input type="hidden" name="val[rating]" value="{if $bIsEdit}{$aForms.rating}{else}0{/if}" id="rating" /></div>
        <div><input type="hidden" name="user_id" value="{$iUserId}" /></div>
        <div class="form-group">
            <label for="title">{required}{_p('Review Title')}:</label>
            <input class="form-control close_warning" type="text" name="val[title]" value="{value type='input' id='title'}" id="title" size="40" />
        </div>

        <div class="form-group">
            <label for="text">{required}{_p('Review Message')}:</label>
            {editor id='text'}
        </div>

        <div class="form-group">
            {template file='ynmember.block.custom.form'}
        </div>

        <div class="ynmember_groupbutton_review">
            <ul class="form-button">
                <li><input type="submit" name="val[{if $bIsEdit}update{else}submit{/if}]" value="{if $bIsEdit}{_p var='Update Review'}{else}{_p var='Submit Review'}{/if}" class="button btn-primary" /></li>
                <li><button type="button" value="{_p var='Cancel'}" class="button btn-default" onclick="tb_remove()">{_p var='Cancel'}</button></li>
            </ul>
            <div class="clear"></div>
        </div>
    </form>
</div>

{literal}
<script>
    var ynmemberSubmitReview = function(){
        var form = document.getElementById('ynmember_js_review_form'),
            message = '';
        if (window.CKEDITOR && CKEDITOR.instances['text'].getData()) {
            form.text.value = CKEDITOR.instances['text'].getData();
        }
        if (!form.rating.value || (form.rating.value < 1)) {
            message = '{/literal}{_p var="Please rate the member"}{literal}';
        } else if (!trim(form.title.value)) {
            message = '{/literal}{_p var="Review title is required"}{literal}';
        } else if (!trim(form.text.value)) {
            message = '{/literal}{_p var="Please enter message for your review"}{literal}';
        }
        if (message) {
            if ($("#js_ajax_compose_error_message"))
                $("#js_ajax_compose_error_message").html("<div class='error_message'>" + message + "</div>");
            return false;
        }

        $(form).ajaxCall('ynmember.submitReview');
        return false;
    }
</script>
{/literal}
