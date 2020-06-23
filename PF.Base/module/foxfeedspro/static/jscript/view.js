/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Photo
 * @version 		$Id: view.js 1174 2009-10-11 13:56:13Z Raymond_Benc $
 */

$Behavior.photoView = function()
{	
	$('#js_update_photo_form').submit(function()
	{
		$('#js_updating_photo').html($.ajaxProcess(oTranslations['photo.updating_photo']));
		
		$(this).ajaxCall('photo.updatePhoto');
		
		$('#js_photo_edit_form').hide();
		$('#js_photo_outer_content').show();		

		return false;
	});
	
	$('#js_photo_cancel_edit').click(function()
	{
		$('#js_photo_edit_form').hide();
		$('#js_photo_outer_content').show();
		
		return false;
	});		
}