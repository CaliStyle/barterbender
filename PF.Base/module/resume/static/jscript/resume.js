/**
 *
 * @copyright      YouNet Company
 * @author         VuDP, TienNPL
 * @package        Module_Resume
 * @version        3.01
 * 
 */

/**
 *	Show popup save recording when user click on save button on flashplayer
 */
function publishResume(resume_id) {
	tb_show(oTranslations['resume.publish_resume'],$.ajaxBox('resume.publishResume', 'width=300&height=180&resume_id='+ resume_id));	
}

/**
 *	Display or hid advanced search box on Resume home page 
 */
function advSearchDisplay()
{
	$("#content h1").toggle();
	var $form = $('#resume_adv_search');
	var $flag = $('#form_flag');
	if($flag.val() == 1)
	{
		$form.hide();
		$flag.val(0); 
	}
	else
	{
		$form.show();
		$flag.val(1);
	}
}

/**
 *  Favorite action
 */
function FavoriteAction(sActionType, iItemId, type)
{
	if (sActionType == 'favorite') 
	{
		$('#js_favorite_link_unlike_' + iItemId).show();
		$('#js_favorite_link_like_' + iItemId).hide();

		$.ajaxCall('resume.addFavorite', 'id=' + iItemId, 'GET');
	}
	else 
	{
		$('#js_favorite_link_like_' + iItemId).show();
		$('#js_favorite_link_unlike_' + iItemId).hide();
		$.ajaxCall('resume.deleteFavorite', 'id=' + iItemId + '&type=' + type, 'GET');
	}
}

/**
 * Note action 
 */
function NoteAction(sActionType, iItemId)
{
	if(sActionType == 'note')
	{
		$Core.box('resume.addNote', 400, 'id=' + iItemId);
	}
	else
	{
		$('#js_favorite_link_note_' + iItemId).show();
		$('#js_favorite_link_unnote_' + iItemId).hide();
		$.ajaxCall('resume.deleteNote', 'id=' + iItemId, 'GET');
	}
}

function showInProfileInfo(e)
{
    var checkbox = $(e);
    var iResumeId = checkbox.val();
    var iShowInProfile = 0;
    var sCheckboxId = checkbox.attr('id');
    if (checkbox.is(':checked'))
    {
        iShowInProfile = 1
    }
    $.ajaxCall('resume.showInProfileInfo', 'iResumeId=' + iResumeId + '&iShowInProfile=' + iShowInProfile + '&sCheckboxId=' + sCheckboxId, 'GET');
}
function approveResume(resume_id) {
    $Core.jsConfirm({}, function(){
		$.ajaxCall('resume.approveResumeFrontEnd', 'id=' + resume_id);
    },function(){
	});
	return false;
}

function denyResume(resume_id) {
    $Core.jsConfirm({}, function() {
        $.ajaxCall('resume.denyResumeFrontEnd', 'id=' + resume_id);
    },function(){

	});
	return false;
}
$Behavior.ynresume_addclassbody = function(){
    if (window.jQuery)
    {
        if ($('#page_resume_index').length)
        {
            $( "body").removeClass("resumeClass");
            $( "body").addClass("resumeClass");



        }
        
            if ($('#page_resume_view').length)
            {
                $( "body").removeClass("resumeClass");
                $( "body").addClass("resumeClass");
            }

		$( ".action_drop a" ).each(function( index ) {
			var href =  $( this ).attr("href");
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
			href = href.replace("noted/?s","noted&");

			href = href.replace("3/?s","3&");
			href = href.replace("9/?s","9&");
			href = href.replace("12/?s","12&");
			href = href.replace("15/?s","15&");
			href = href.replace("1/?s","1&");


			$( this ).attr("href",href);
		});
    }


}