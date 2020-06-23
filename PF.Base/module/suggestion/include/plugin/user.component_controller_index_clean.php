<?php

    $sSuggestion = _p('suggestion.suggestion_setting', array(), false, "Suggestion");
    $iPageId=1;
    if(!phpfox::getUserParam("suggestion.enable_friend_suggestion"))
        $iPageId=0;
    if(!phpfox::getUserParam("suggestion.enable_friend_recommend") && !phpfox::getUserParam("suggestion.enable_friend_suggestion_popup") && !phpfox::getUserParam("suggestion.enable_content_suggestion_popup"))
        $iPageId=0;
?>
<script type="text/javascript" language="javascript">

    $Behavior.onLoadPlugin_suggestion = function() 
    {
    	if ($('#page_user_privacy').length)
    	{
    		$(".page_section_menu").find("li").find("a").click(function(evt) {
        		$('#_privacy_holder_table').find("#js_privacy_block_suggestion2").hide();
        		$('#js_privacy_block_suggestion').hide();
    		});

            if ( ($("#content").find("ul").first().find("#suggestion_tab").length == 0)
                && ($("#content").find("ul").first().find("li:not(:last)").hasClass('active')))
		    {
		        <?php 
		            if ($iPageId)
		            {
		        ?>
                $('#_privacy_holder_table').find("form").append($("<div id=\"js_privacy_block_suggestion\" class=\"js_privacy_block page_section_menu_holder\" style=\"display: none;\">"));
                $("#content").find("ul").first().append("<li><a href=\"#suggestion_tab\" rel=\"js_privacy_block_suggestion2\" id=\"suggestion_tab\" onclick=\"showSuggestion(this);return false;\"><?php echo $sSuggestion;?></a></li>");


                <?php
                    if ($this->request()->get('tab') == 'suggestion_tab')
                    {
                ?>
                        $("#suggestion_tab").click();
                <?php
                    }
                }
                ?>
    		}

            <?php
                if ($this->request()->get('tab') == 'suggestion_tab')
                {
            ?>
                    $('#page_user_privacy #_privacy_holder_table').append("<div id='js_privacy_block_suggestion2'></div>");
                    $('#page_user_privacy #js_privacy_block_suggestion2').load($.ajaxBox('suggestion.config'));
            <?php
                } else { ?>
                    $('#page_user_privacy #_privacy_holder_table').append("<div style='display:none' id='js_privacy_block_suggestion2'></div>");
                    $('#page_user_privacy #js_privacy_block_suggestion2').load($.ajaxBox('suggestion.config'));
             <?php } ?>
    		
        }
    };
    function showSuggestion(obj){
    	$('.page_section_menu_holder').hide();
        $this = $(obj); 
    	$this.unbind();
        $('#_privacy_holder_table').find("#js_privacy_block_suggestion").show();

        $this.parents("ul").find(".active").removeClass("active");
        $this.parent().addClass("active");
        
        $('#js_privacy_block_suggestion2').show();
    }
    
</script>
