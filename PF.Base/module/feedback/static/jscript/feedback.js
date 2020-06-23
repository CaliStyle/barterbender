/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FeedBack
 * @version          2.01
 *
 */
 var cifb = 0;
 function sltFeedback(e)
 {

     var f = $('#dbTmContent382').width();
     var m = $('#dbTmContent382').parent().width();
    //ttifb
     if ($('#dbTmContent382').position().left <= m-f)
     {
    	$('.dbTmRight').hide();
    	$('.dbTmLeft').show();
        return;   
     }
     $('.dbTmRight').show();
     $('.dbTmLeft').show();
     var l = $("#dbTmContent382").css('left') ? parseInt($("#dbTmContent382").css('left')) : 0;
     var iem = $('#dbTmContent382 .menu_cat').eq(cifb);
     var ilw = 0;
     if(iem)
     {
        ilw = iem.width()+2;
     }
     l = l - ilw;
     cifb++;
     
     $("#dbTmContent382").animate({left:l+'px'},'fast');
 }
  function srtFeedback(e)
 {
     if ($('#dbTmContent382').position().left >=0)
     {	
    	 $('#dbTmContent382').css('left','5px');
    	 $('.dbTmLeft').hide();
        return;   
     }
     $('.dbTmRight').show();
     $('.dbTmLeft').show();
     var l = $("#dbTmContent382").css('left') ? parseInt($("#dbTmContent382").css('left')) : 0;
     var iem = $('#dbTmContent382 .menu_cat').eq(cifb);
     var ilw = 0;
     if(iem)
     {
        ilw = iem.width()+2;
     }
     l = l + ilw;
     cifb--;
     $("#dbTmContent382").animate({left:l+'px'},'fast');
 }
 $Behavior.recheckPostion = function()
 {
     if($aBoxHistory['feedback.addFeedBack'])
     {
        var nvb = $('.js_box').length;
        if( nvb > 0)
        {
            for(i = 0 ; i < nvb-1 ; i ++)           
            {
                for(j = i+1; j < nvb; j++)
                {   
                    var z = $('.js_box').eq(j).css('z-index');
                    $('.js_box').eq(j).css('z-index',$('.js_box').eq(i).css('z-index'));
                    $('.js_box').eq(i).css('z-index',z);
                    
                    
                }
            }
                
        }
        
        
     }
     
     
 }
function updatevote(feedback_id,total_vote,sType)
{ 
   $.ajaxCall('feedback.updateVote','feedback_id='+feedback_id+'&total_vote='+total_vote+'&sType='+sType);
}
function approve(id)
{
    $.ajaxCall('feedback.approve','id='+id+'&inline=true');
}
function updatevotepopup(feedback_id,total_vote,sType,usid)
{ 
    if(usid ==0)
    {
        // tb_remove();
		$('#TB_window').html('');
        $('#TB_window').hide('');
    }
    $.ajaxCall('feedback.updateVotePopUp','feedback_id='+feedback_id+'&total_vote='+total_vote+'&sType='+sType);
}

function deleteFeedBack(feedback_id)
{
    $Core.jsConfirm({}, function() {
        $.ajaxCall('feedback.inlineDelete', 'feedback_id=' + feedback_id);
    },function(){});
}

function deleteAdminFeedBack(feedback_id)
{
    $Core.jsConfirm({}, function() {
        $.ajaxCall('feedback.inlineDeleteFeedBack', 'feedback_id=' + feedback_id);
    },function(){});
}

function deleteCategory(category_id)
{
    $Core.jsConfirm({}, function() {
        $.ajaxCall('feedback.callDeleteCategory', 'category_id=' + category_id);
    },function(){});
    
}

function deleteServerity(serverity_id)
{
    $Core.jsConfirm({}, function() {
        $.ajaxCall('feedback.callDeleteServerity', 'serverity_id=' + serverity_id);
    },function(){});
}

function deleteStatus(status_id)
{
    $Core.jsConfirm({}, function() {
        $.ajaxCall('feedback.callDeleteSatus', 'status_id=' + status_id);
    },function(){});
}
function deletePic(picture_id,feedback_id)
{
    $Core.jsConfirm({message: oTranslations['feedback.are_you_sure_you_want_to_delete_this_photo']}, function() {
        $.ajaxCall('feedback.deletePicture', 'picture_id=' + picture_id + '& feedback_id=' + feedback_id);
    },function(){});
}

function updatefeatured(id,is_featured)
{
   $('#item_update_featured_'+id).html('Updating...');
   $.ajaxCall('feedback.updateFeatured','item_id='+id+'&is_featured='+is_featured);
}

function updatevotable(id,votable)
{
   $('#item_update_votable_'+id).html('Updating...');
   $.ajaxCall('feedback.updateVotable','item_id='+id+'&votable='+votable);
}

function selectAll()
{
    var check = document.getElementsByName('is_selected');
    var is_select = document.getElementById('checkAll');
    var count = check.length;
    for(var i = 0 ; i < count ; i++){
        check[i].checked = is_select.checked;
    }
}

var is_submit=true;
function getsubmit()
{
    return is_submit;
}
function setValue()
{
    var check = document.getElementsByName('is_selected');
    var count = check.length;
    var arr = "";
    for(var i = count-1 ; i >=0 ; i--){
        if ( check[i].checked == true)
        {
             arr+=","+check[i].value;


        }
    }
    document.getElementById('arr_selected').value =arr ;
}
function plugin_completeProgress()
{
	if(redict_url == null)
    {
        return false;
    }
    window.location.href = redict_url;
}

function viewFeedback(feedback_id)
{
	
    var iId=$('#feedback_id').val();
    $.ajaxCall("feedback.viewFeedback",'id='+feedback_id);
}

function showFormPostFeedBack()
{
	$.ajaxCall("feedback.showFormPostFeedBack");
}

function viewFeedbackByCategory(category_id)
{
	var iCat = category_id;
	$(".category_feedback_entry").removeClass('active');
	$("#feedback_category_" + iCat).addClass('active');
	$(".top-feedback").hide();
	$("#feedback_show_by_cat_"+category_id).show();
	//$.ajaxCall("feedback.viewFeedbackByCategory", 'id='+category_id);
}

function nextCategories()
{
	// var pos = document.getElementById("category-2").style.left;
	// pos -= 5;
	// alert(document.getElementById("category-2").style.left)
	document.getElementById("category-2").style.display = "none";
	document.getElementById("category-3").style.display = "none";
	document.getElementById("category-4").style.display = "";
	document.getElementById("category-5").style.display = "";
	document.getElementById("pre-button").style.display = "";
	document.getElementById("next-button").style.display = "none";
	// window.setTimeout("nextCategories();",3000);
	// alert(document.getElementById("category-2").style.display);
}

function preCategories()
{
	// var pos = document.getElementById("category-2").style.left;
	// pos -= 5;
	// alert(document.getElementById("category-2").style.left)
	document.getElementById("category-4").style.display = "none";
	document.getElementById("category-5").style.display = "none";
	document.getElementById("category-3").style.display = "";
	document.getElementById("category-2").style.display = "";
	document.getElementById("next-button").style.display = "";
	document.getElementById("pre-button").style.display = "none";
	// window.setTimeout("preCategories();",3000);
	// alert(document.getElementById("category-2").style.display);
}

function moveScrollRightAuto(el_id, b ) 
{
	
	if (b)
	{
		scrollTimerId = setInterval("moveScrollRight('"+el_id+"')", 100); 
	}
	  
	else
	{
		clearInterval(scrollTimerId);
	}
	  
}

function moveScrollLeftAuto(el_id, b ) 
{
	if(b)
	{
		scrollTimerId = setInterval("moveScrollLeft('"+el_id+"')", 100);	
	}
	else
	{
		clearInterval(scrollTimerId);
	}
	
}

function moveScrollRight(el_id ) 
{	
	 var step = 5;
	 var e = $('#' + el_id);
	 var parent_width = getChildNodeWidth();
	 var left = e.css('left') ? parseInt(e.css('left')) : 0;
	 var minLeft = parent_width - parseInt(e.width());
	
	 if((left-step) > minLeft)
	 {    
		 e.css('left', left - step + 'px');
	 }
		 
	 else 
	 {
		 e.css('left', minLeft + 'px');
		 moveScrollRightAuto(el_id, false);
	 }
}

function moveScrollLeft(el_id) 
{
	 var step = 5;
	 var e = $('#' + el_id);
	 var left = e.css('left') ? parseInt(e.css('left')) : 0;
	 if(left + step < 0)
	 {
		 console.log(getChildNodeWidth());
		 e.css('left', left + step + 'px');
	 }
	   
	 else 
	 {
		 e.css('left', '0px');
		 moveScrollLeftAuto(el_id, false);
	 }
}

function getChildNodeWidth(el_id)
{
	 var e = $('#' + el_id);
	 var nodes = document.getElementById("dbTmContent382").getElementsByTagName("div");
	 var width = 0;
	 for(i = 0; i < nodes.length; i++)
	 {	
		width += $(nodes[i]).width(); 
	 }
	 return width;
}

function showStatusDescription(b)
{
	var el = document.getElementById('show_status_description');
	if(b)
	{
		$(el).slideDown();
	}
	else
	{
		$(el).slideUp();
	}
}

function showPictureFeedback(picture_id)
{
	$.ajaxCall("feedback.showPictureFeedback", 'id='+picture_id);
}

function getFeedbackPic(picture_path1, picture_id)
{
	var temp_path = $('#img_show').attr('src');
	var temp_id = $('#img_show').attr('rel');
	$('#img_show').attr('src', picture_path1);
	$('#img_show').attr('rel', picture_id);
	$('#img_thumb'+picture_id).attr('src', temp_path);
	$('#img_thumb'+picture_id).attr('rel', temp_id);
}

function adjustPopup(height) 
{
	//$('.js_box').css('width','auto');
	$(".js_box").each(function(i,v){
		$(v).css("margin-left", $(v).width()/2*-1);
		$(v).css("top", ($(window).height() - height)/2);
	});
}

var iNewInputBars = 0;

function addMoreToProgressBar()
{
	iNewInputBars++;	

	if ((iNewInputBars + oProgressBar['total']) > oProgressBar['max_upload'])
	{
		iNewInputBars--;
		
		return false;
	}

	$('.js_uploader_files_input').each(function()
	{
        previewImages(this);
        console.log(this.files);
        if (empty(this.value))
		{
			iNewInputBars--;
			$(this).parent().remove();
		}
	});	
	
	$('#js_uploader_files_outer').append('<div class="js_uploader_files form-group" id="js_new_add_input_' + iNewInputBars + '"><input type="file" name="' + oProgressBar['file_id'] + '" class="js_uploader_files_input" size="30" onchange="addMoreToProgressBar();" /></div>' + "\n");
	
	return false;
}

var iTotalImagesToBeUploaded = 0;
var iTotalUploadedFiles = 0;
var hasUploaded = 0;
var hasErrors = 0;

function progressBarInit()
{
    if (!isset(oProgressBar['html5upload']) || (isset(oProgressBar['html5upload']) && !oProgressBar['html5upload'])){
        bIsHTML5ProgressUpload = false;
    }

	p('__LOADING_IMAGE_UPLOADER__');

	if ($(oProgressBar['uploader']).length > 0)
	{
		$(oProgressBar['progress_id']).html('<div id="js_progress_outer" style="width:300px;"><div id="js_progress_inner"><span id="js_progress_percent_value">0</span>/100%</div></div>');
		
		sInput = '<div id="js_uploader_files_outer">';
        if (bIsHTML5ProgressUpload)
        {
            oProgressBar['total'] = 1;
        }
		for (i = 1; i <= oProgressBar['total']; i++)
		{
			sInput += '<div class="js_uploader_files form-group"><input ' + (bIsHTML5ProgressUpload ? 'multiple="multiple"' : '') + ' type="file" name="' + oProgressBar['file_id'] + '" class="js_uploader_files_input" size="30" ' + (bIsHTML5ProgressUpload ? '' : 'onchange="addMoreToProgressBar();"') + ' /></div>' + "\n";
		}
		sInput += '</div>';
         sInput += '<div id="galery"> </div>';
		
		var iDivHeight = $(oProgressBar['holder']).innerHeight();	
		// $(oProgressBar['holder']).hide().after('<div id="js_progress_cache_loader" style="height:' + (iDivHeight <= 0 ? '200' : iDivHeight)  + 'px;">' + $.ajaxProcess('Loading', 'large') + '</div>');
		
		$(oProgressBar['holder']).after('<div id="js_progress_cache_loader" style="height:' + (iDivHeight <= 0 ? '200' : iDivHeight)  + 'px; display:none;"></div>');

        if (isset(oProgressBar['frame_id'])) {
            sInput += '<iframe id="' + oProgressBar['frame_id'] + '" name="' + oProgressBar['frame_id'] + '" height="500" width="500" frameborder="1" style="display:none;"></iframe>';
        }
		
		$(oProgressBar['uploader']).html(sInput);
		
		// $.ajaxCall('user.checkSpaceUsage', 'holder=' + oProgressBar['holder'].replace('#', ''), 'GET');

        if (bIsHTML5ProgressUpload){
            $('.js_uploader_files_input')[0].addEventListener("change", function(e) {

	            iTotalImagesToBeUploaded = 0;
	            iTotalUploadedFiles = 0;
	            hasUploaded = 0;
	            hasErrors = 0;

	            $(oProgressBar['holder']).hide();
				$('html, body').animate({
					scrollTop: $(oProgressBar['uploader']).scrollTop()
				});

                var files = e.target.files || e.dataTransfer.files;
	            iTotalUploadedFiles = files.length;
                for (var i = 0, f; f = files[i]; i++) {
                    if (i >= oProgressBar['max_upload']){
                        break;
                    }

                    if (isset(oProgressBar['valid_file_ext']))
                    {
                        sExt = f.name.split('.').pop().toLowerCase();
                        if ($.inArray(sExt, oProgressBar['valid_file_ext']) == -1)
                        {
                            sExts = '';
                            for (iExt in oProgressBar['valid_file_ext'])
                            {
                                if (iExt > 0)
                                {
                                    sExts += ', ';
                                }
                                sExts += oProgressBar['valid_file_ext'][iExt];
                            }
                            alert($('<div/>').html(oTranslations['core.not_a_valid_file_extension_we_only_allow_ext'].replace('{ext}', sExts)).text());

                            break;
                        }
                    }

                    ParseFile(f, i);
                    UploadFile(f, i);
                }
            }, false);
        }
	}
};


function previewImages(input) {

    $('#js_uploaded_images').css('display','block');
    $('#js_uploaded_images img').css('margin-right','5px');
    $('#js_uploaded_images img').css('margin-bottom','5px');

    var $preview = $('#js_uploaded_images').empty();
    if (input.files) $.each(input.files, readAndPreview);

    function readAndPreview(i, file) {

        if (!/\.(jpe?g|png|gif|jpg)$/i.test(file.name)){
            return alert(file.name +" is not an image");
        } // else...

        var reader = new FileReader();

        $(reader).on("load", function() {
            $preview.append($("<img/>", {src:this.result, height:100}));
        });

        reader.readAsDataURL(file);

    }
}

var feedbackUploadPhoto = {
    success: false,
    successIds: '',
    dropzoneOnSuccess: function (ele, file, response) {
        response = JSON.parse(response);
        if (typeof response.id !== 'undefined') {
            file.item_id = response.id;
        }
        // show error message
        if (typeof response.errors != 'undefined') {
            for (var i in response.errors) {
                if (response.errors[i]) {
                    $Core.dropzone.setFileError('feedback', file, response.errors[i]);
                    return;
                }
            }
        }
        feedbackUploadPhoto.success = true;
        feedbackUploadPhoto.successIds += response.id + ',';
        return file.previewElement.classList.add('dz-success');
    },
    dropzoneQueueComplete: function () {
        if (feedbackUploadPhoto.success) {
            $('#js_feedback_success_message').fadeIn().fadeOut(2000);
        }
    }
};