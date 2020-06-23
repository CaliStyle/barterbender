<?php
?>
{if isset($aFeedBackPics) && count($aFeedBackPics)}
 <div class="feedback_image_detail" {if count($aFeedBackPics)>0}style="padding-bottom:9px;"{/if}>
	{if (!empty($aFeedBack.feedback_servertity_name) && Phpfox::isAdmin()) || (!empty($aFeedBack.status))}  
	{*<div class="feedback_status_box">
		{if $aFeedBack.feedback_servertity_name != '' && Phpfox::isAdmin()}
			<div class="feedback_servertity_entry">
                <span>{_p var='serverity'}:</span> <span class="feedback_servertity_{$aFeedBack.status} feedback_servertity" style="background-color:#{$aFeedBack.feedback_serverity_color};">{$aFeedBack.feedback_servertity_name}</span></div>
		{/if}
		{if !empty($aFeedBack.status)}
    		<span>{_p var='status'}:</span> <a class="feedback_status_{$aFeedBack.status} feedback_status" style="background-color:#{$aFeedBack.color};{if phpfox::isAdmin()}{/if}">{$aFeedBack.status}</a>
    		<div class="feedbacks_browse_info_blurb" style=" margin-top:5px;">{$aFeedBack.feedback_status} </div>
		{/if}
	</div>*}
	{/if}

		<div class="feedback_images" id="feedback_images">   	
	    	<div class="ynf_small_thumb clearfix">
	    	{foreach from=$aFeedBackPics  item=aPic name=aPic}
	        <span style="display:none">
	            <img id="img_thumb_tmp{$aPic.picture_id}"  src="{img server_id=$aPic.server_id path='core.url_pic' file='feedback/'.$aPic.picture_path suffix='' return_url=true}" />
	        </span>
	    		<div class="feedback_img feedback-item-img">            				
	        		<a href="javascript:void(0);">
	                    <img id="img_thumb{$aPic.picture_id}"  original="{img server_id=$aPic.server_id path='core.url_pic' file='feedback/'.$aPic.picture_path suffix='' return_url=true}" onclick="showPopUp('{$aPic.picture_id}','{$feedback_id}');return false;" rel="{$aPic.picture_id}"  src="{img server_id=$aPic.server_id path='core.url_pic' file='feedback/'.$aPic.thumb_url_temp suffix='_thumb' max_height=55 return_url=true}"   />
	                </a>

					{if $aFeedBack.user_id == Phpfox::getUserId() || Phpfox::IsAdmin()} 
	                    <span onclick="deletePic({$aPic.picture_id},{$aFeedBack.feedback_id});return false;">
	                        <i class="fa fa-times-circle"></i>
	                    </span> 
	                {/if}
	    		</div>
		 		{/foreach}
			</div>		
		</div>
</div>

{literal}
<script type="text/javascript">
    function showPopUp(pic_id,fb_id){
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
{/if}