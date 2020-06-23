
function rebuildDocument()
{
    
    var count = 0;  
    var item_name_left, item_name_right;
    var i=0;    var last_item;
    for (i=0; i<15; i++)
    {
	    
        item_name_left = '#item_' + i + '_left';
        obj1 = $(item_name_left);
        item_name_right = '#item_' + i + '_right';
		item_name_center = '#item_' + i + '_center';
        if (obj1.length >0)
        {
            
            if ((count % 3) == 0)
            {
                $(item_name_left).addClass('background-left');
                $(item_name_right).removeClass('background_right');
				$(item_name_center).removeClass('background_center');
				
            }else if ((count % 3) == 2)
            {
                $(item_name_left).removeClass('background-left');
				$(item_name_center).removeClass('background_center');	
                $(item_name_right).addClass('background_right');
				
            }else
            {
                 $(item_name_left).removeClass('background-left');
                 $(item_name_right).removeClass('background_right'); 
				 $(item_name_center).addClass('background_center');
				
            }
            last_item = i;
            count++;
        }else
        {
        }   
    }
    item_name_right = '#item_' + last_item + '_right';
    $(item_name_right).addClass('background_right');
 
}
$Behavior.imageCategoryListing = function()
{
    $('.js_mp_category_list').change(function()
    {
        $("#jp_category_message").hide();
        var iParentId = parseInt(this.id.replace('js_mp_id_', ''));
        
        $('.js_mp_category_list').each(function()
        {
            if (parseInt(this.id.replace('js_mp_id_', '')) > iParentId)
            {
                $('#js_mp_holder_' + this.id.replace('js_mp_id_', '')).hide();                
                
                this.value = '';
            }
        });
        
        $('#js_mp_holder_' + $(this).val()).show();
    });        

    
    $('.hover_action').each(function()
    {
        $(this).parents('.js_outer_video_div:first').css('width', this.width + 'px');
    });

    $("#submit_document").click(function()
    {
            
            var is_return = true;
            var selected = "val[category][]";
            var value = $("select[name=\'" + selected + "\'] option:selected").val();

           // $('#core_js_document_form_msg').hide('');
            $('#core_js_document_form_msg').html('');
            var bIsValid = true;
            if ($('#title').val() == '')
            {
                bIsValid = false; 
                $('#core_js_document_form_msg').message(oTranslations['document.fill_title_for_document'], 'error');

                $('#title').addClass('alert_input');
            }
         
            if ($('#text').val() == '' && (typeof oParams.sEditor == 'undefined'))
            {
                bIsValid = false;
                $('#core_js_document_form_msg').message(oTranslations['document.add_description_to_document'], 'error');
                $('#text').addClass('alert_input');
            }
         
           
            if ($("select[name=\'" + selected + "\'] option:selected").val() == "") 
            {
                bIsValid = false;
                $('#core_js_document_form_msg').message(oTranslations['document.choose_category_for_your_document'], 'error');
            }

        // add
        if ($("#uploadedfile").val()=="")
        {
            bIsValid = false;
            $('#core_js_document_form_msg').message(no_upload_file_message, 'error');
        }
        // edit

       // window.location.hash = '#pem';



        if ( bIsValid )
        {
            return true;
        }
        $("#uploadedfile").val("");
        $('#core_js_messages').show();
        $('#core_js_document_form_msg').show();
        $("html, body").animate({ scrollTop: 0 }, "slow");

     //   $('#core_js_document_form_msg').show(); window.location.hash = '#pem';
			return false;

    });


    $('.video_info_box').click(function()
    {    
        if (!$('.video_info_box').hasClass('video_info_box_is_clicked'))
        {
            $Core.processVideoInfo($('.video_info_toggle'));
        }
        
        return false;
    });
    
    $('.video_info_toggle').click(function()
    {        
        $Core.processVideoInfo(this);        
    
        return false;
    });
    
}

$Core.processDocumentInfo = function($oObj)
{

    if (!$($oObj).hasClass('is_already_clicked'))
    {    
        $($oObj).addClass('is_already_clicked');
        $('.document_info_box').addClass('document_info_box_is_clicked');
        $('.document_info_box_extra').show();
        $('.js_view_more_part').hide();
        $('.js_view_more_full').show();    
        $('.js_info_toggle_show_less').show();
        $('.js_info_toggle_show_more').hide();    
        $('.document_text_shorten').hide();
        $('.document_text_parsed').show();
    }
    else
    {
        $('.document_info_box').removeClass('document_info_box_is_clicked');
        $($oObj).removeClass('is_already_clicked');
        $('.video_info_box_extra').hide();
        $('.js_view_more_part').show();
        $('.js_view_more_full').hide();    
        $('.js_info_toggle_show_less').hide();
        $('.js_info_toggle_show_more').show();    
        $('.document_text_shorten').show();
        $('.document_text_parsed').hide();
    }    
}
function toggleDocumentInfo()
{
    $('.document_info_box_extra').toggle();
    $('.js_view_more_part').toggle();
    $('.js_view_more_full').toggle();
    $('.js_info_toggle_show_less').toggle();
    $('.js_info_toggle_show_more').toggle();
    $('.document_text_shorten').toggle();
    $('.document_text_parsed').toggle();
}

var $iTotalUserVideos = 0;
$Behavior.videoUserBrowser = function()
{
    var iUserVideoIteration = 1;    
    var iUserVideoPerView = 0;

    $('.video_user_bar_pager_menu ul li a').click(function() {

        iUserVideoPerView = ($iTotalUserVideos / 4);
        var iCurrentPageRel = $(this).attr('rel');
        
        $('.video_user_bar_pager_menu ul li a').removeClass('active');
        $(this).addClass('active');        
        
        var iNewLocation = ((iCurrentPageRel - 1) * (158 * 4));
        
        $('.video_user_more_holder').css({left: '-' + iNewLocation + 'px'});

        iUserVideoIteration = iCurrentPageRel;

        return false;
    });
    
    $('.video_user_bar_click').click(function() {
        
        var $sType = ($(this).attr('rel') == 'previous' ? '+' : '-');

        iUserVideoPerView = Math.ceil($iTotalUserVideos / 4);
        
        if ($(this).attr('rel') == 'previous' && iUserVideoIteration == 1)
        {        
            return false;
        }        
        
        if ($(this).attr('rel') == 'previous')
        {
            iUserVideoIteration--;
        }
        else
        {
            if (iUserVideoIteration >= iUserVideoPerView)
            {
                return false;
            }
        
            iUserVideoIteration++;        
        }

        $('.video_user_bar_pager_menu ul li a').removeClass('active');
        $('.video_user_bar_pager_menu ul li a[rel="' + iUserVideoIteration + '"]').addClass('active');
        
        $('.video_user_more_holder').animate({left: $sType + '=' + ($('.video_user_more_holder').width() + 2) + 'px'}, 'slow');

        return false;
    });    
    
    $('.video_view_embed').click(function(){
        $('.video_view_embed_holder').toggle();
        $('.video_view_embed_holder').find('textarea').select();
        return false;
    });

	 $("#document_user_is_click a").click(function(){
		 window.location = this.href;
		 return false;
	 });

}


$Behavior.yndocument_addclassbody = function(){

    //console.log("test channel.js");
    if (window.jQuery)
    {
        var addClass_yndocument = false;
        if ($('#page_document_index').length)
        {
            addClass_yndocument = true;
        }
        if ($('#page_document_view').length)
        {
            addClass_yndocument = true;
        }
        if ($('#page_document_add').length)
        {
            addClass_yndocument = true;
        }
        if (addClass_yndocument)
        {
            $( "body").removeClass("documentClass");
            $( "body").addClass("documentClass");
        }
    }
}

$Behavior.yndocument_fixsearch = function(){

    //console.log("test channel.js");
    if (window.jQuery)
    {
        if ($('#page_document_index').length)
        {
            $( ".action_drop a" ).each(function( index ) {
                //console.log( index + ": " + $( this ).text() );
                var href =  $( this ).attr("href");
                //console.log(href);
                href = href.replace("view=channels/?","view=channels&");
                href = href.replace("view=my/?","view=my&");
                href = href.replace("view=friend/?","view=friend&");
                href = href.replace("view=favorite/?","view=favorite&");
                href = href.replace("view=featured/?","view=featured&");
                href = href.replace("view=pending/?","view=pending&");
                href = href.replace("view=all_channels/?","view=all_channels&");

                href = href.replace("sort=most-talked/?","sort=most-talked&");
                href = href.replace("sort=most-viewed/?","sort=most-viewed&");

                href = href.replace("featured/?s","featured&");

                href = href.replace("3/?s","3&");
                href = href.replace("9/?s","9&");
                href = href.replace("12/?s","12&");
                href = href.replace("15/?s","15&");
                href = href.replace("1/?s","1&");


                $( this ).attr("href",href);
            });
        }
    }


}