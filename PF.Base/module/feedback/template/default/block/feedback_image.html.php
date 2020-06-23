<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div>
    <span style="display:none">
        <img id="img_thumb_tmp{$picture_id}"  src="{img server_id=$aPic.server_id path='core.url_pic' file='feedback/'.$aPic.picture_path suffix='' return_url=true}" />
    </span>
    <img src="{img server_id=$aPic.server_id path='core.url_pic' file='feedback/'.$aPic.picture_path suffix=''  return_url=true}" style="max-height:600px;  max-width:600px"/>
</div>
<div class="ynfeedback_popup_arrow">
    {if $prePicId != -1}
    <a class="ynfeedback_popup_arrow-prev" href="#" onclick="js_box_remove(this);showPopUp('{$prePicId}','{$feedback_id}');return false;">
        <i class="fa fa-chevron-left"></i><!--  {phrase var='core.previous'} -->
    </a>
    {/if}
    {if $nextPicId != -1}
    <a class="ynfeedback_popup_arrow-next" href="#" onclick="js_box_remove(this);showPopUp('{$nextPicId}','{$feedback_id}');return false;">
        <!-- {phrase var='core.next'}  --><i class="fa fa-chevron-right"></i>
    </a>
    {/if}
</div>
{literal}
<script type="text/javascript">
    function showPopUp(pic_id, fb_id){
        $('body').scrollTop(0);
        url = $('#img_thumb_tmp'+pic_id).attr('src');
        image = new Image();
        image.src = url;
        $iHeight = image.height;
        $iWidth = image.width;
        if($iWidth==0)
        {
            $iWidth = $iHeight = 600;
        }
        if($iHeight >= 600)
        {
            $iWidth = 600 * $iWidth / $iHeight;
            $iHeight = 600;
        }
        if($iWidth >= 600)
        {
            $iHeight = 600 * $iHeight / $iWidth;
            $iWidth = 600;
        }
        $iWidth += 30;
        $iHeight += 80;

        $title = oTranslations['feedback_photo'];

        tb_show($title, $.ajaxBox('feedback.getPictureFeedbackBlock','height='+$iHeight+'&width='+$iWidth+'&link='+pic_id+'&fb_id='+fb_id));

    }
</script>
{/literal}