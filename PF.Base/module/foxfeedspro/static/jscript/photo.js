/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Photo
 * @version 		$Id: photo.js 1354 2009-12-22 20:56:48Z Raymond_Benc $
 */

function plugin_completeProgress()
{
	// An error occured, lets let them know that none of their images were uploaded
	if ($('#js_photo_upload_failed').hasClass('js_photo_upload_failed'))
	{
		alert(oTranslations['photo.none_of_your_files_were_uploaded_please_make_sure_you_upload_either_a_jpg_gif_or_png_file']);
		
		return false;
	}	

	if ($('#js_photo_action').val() == 'upload')
	{
		$('#js_upload_form_outer').show();
	}

	iCnt = 0;
	sHtml = '';
	$('.js_uploaded_image').each(function()
	{
		iCnt++;
		if (iCnt == 1)
		{
			$(this).addClass('row_first');			
		}
		else
		{
			$(this).removeClass('row_first');
		}
		
		sHtml += '<div id="js_uploaded_photo_' + this.id.replace('js_photo_', '') + '"><input type="hidden" name="val[photo_id][]" value="' + this.id.replace('js_photo_', '') + '" /></div>';
	});
	$('#js_post_form_content').html(sHtml);	

	switch ($('#js_photo_action').val())
	{
		case 'process':
			$('#js_post_form').submit();
			break;
		default:
			iNewInputBars = 0;
			$('.js_uploader_files').remove();
			sInput = '';
			for (i = 1; i <= oProgressBar['total']; i++)
			{
				sInput += '<div class="js_uploader_files"><input type="file" name="' + oProgressBar['file_id'] + '" size="30" class="js_uploader_files_input" disabled="disabled" onchange="addMoreToProgressBar();" /></div>' + "\n";
			}	
			$('#js_uploader_files_outer').append(sInput);			
			break;
	}
}

function plugin_startProgress(sProgressKey)
{
	$('#js_upload_form_outer').hide();
}

function deleteNewPhoto(iId)
{
	if (confirm(getPhrase('core.are_you_sure'))) 
	{		
		$('#js_photo_' + iId).remove();
		$('#js_uploaded_photo_' + iId).remove();
		
		iCnt = 0;
		$('.js_uploaded_image').each(function()
		{		
			iCnt++;	
		});

		if (!iCnt)
		{
			$('#js_uploaded_images').hide();
		}			
		
		$.ajaxCall('photo.deleteNewPhoto', 'id=' + iId);
		
		return false;
	}
	
	return false;
}

function plugin_addFriendToSelectList()
{
	$('#js_allow_list_input').show();
}

function plugin_cancelFriendSelection()
{
	$('#js_allow_list_input').hide();
}

Behavior.photoCategoryDropDown = function()
{
	if (!empty($('.js_photo_active_items').html()))
	{
		var aParts = explode(',', $('.js_photo_active_items').html());
		for (i in aParts)
		{			
			if (empty(aParts[i]))
			{
				continue;
			}		
			
			$('#js_photo_category_' + aParts[i]).attr('selected', true);
		}
	}
	
	$('.js_photo_category').click(function()
	{
		iId = this.id.replace('js_photo_category_', '');
		iItemId = $(this).parents('div:first').parent().parent().parent().find('.js_photo_item_id').html();
						
		if ($(this).hasClass('selected'))
		{
			$(this).removeClass('selected');
			$('#js_photo_category_id_' + (iItemId === null ? '' : iItemId) + iId).remove();		
		}
		else
		{
			$(this).addClass('selected');
			$(this).prepend('<div id="js_photo_category_id_' + (iItemId === null ? '' : iItemId) + iId + '"><input type="hidden" name="val' + (iItemId === null ? '' : '[' + iItemId + ']') + '[category_id][]" value="' + iId + '" /></div>');		
		}		
		
		return false;
	});
	
	$('.js_photo_category_done').click(function()
	{
		$('.select_clone').hide();
		
		return false;			
	});
	
	$('.select_clone_select').click(function()
	{
		$(this).next('.select_clone').toggle();	
		
		return false;	
	});
	
	$(document).click(function()
	{
		$('.select_clone').hide();
	});	
	
	$('.hover_action').each(function()
	{
		$(this).parents('.js_outer_photo_div:first').css('width', this.width + 'px');
	});
	
	$('#js_photo_album_select').change(function()
	{
		if ($('#js_is_photo_callback').length > 0)
		{
			alert('test');
		}
		
		if (empty(this.value))
		{
			$('#js_photo_view_this_album').remove();	
		}
		else
		{
			if ($('#js_photo_view_this_album').length > 0)
			{
				$('#js_photo_view_this_album').show();
			}
			else
			{
				$('#js_photo_action').append('<option value="view_album" id="js_photo_view_this_album">' + oTranslations['photo.view_this_album'] + '</option>');
			}
		}		
	});
}