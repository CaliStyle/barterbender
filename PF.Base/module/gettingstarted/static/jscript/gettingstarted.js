/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function viewNextTodoList()
{
    var order=$('#ordering').val();
    $.ajaxCall("gettingstarted.viewNextTodoList",'order='+order);
}

function viewPreTodoList()
{
    var order=$('#ordering').val();
    $.ajaxCall("gettingstarted.viewPreTodoList",'order='+order);
}

function doneTodoList()
{
    $.ajaxCall("gettingstarted.doneTodoList");
}
$Behavior.imageCategoryListing = function()
{
	$('.js_mp_category_list').change(function()
	{
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
		$(this).parents('.js_outer_gettingstarted_div:first').css('width', this.width + 'px');
	});	
	
	$('.full_name a, .js_allow_gettingstarted_click a').click(function(){
		window.location.href = $(this).attr('href');	
		return false;
	});
	
	$('.js_edit_gettingstarted_form').keydown(function(){$Core.resizeTextarea($(this));});	
}

$Core.processVideoInfo = function($oObj)
{	
	if (!$($oObj).hasClass('is_already_clicked'))
	{	
		$($oObj).addClass('is_already_clicked');
		$('.gettingstarted_info_box').addClass('gettingstarted_info_box_is_clicked');
		$('.gettingstarted_info_box_extra').show();
		$('.js_view_more_part').hide();
		$('.js_view_more_full').show();	
		$('.js_info_toggle_show_less').show();
		$('.js_info_toggle_show_more').hide();
	}
	else
	{
		$('.gettingstarted_info_box').removeClass('gettingstarted_info_box_is_clicked');
		$($oObj).removeClass('is_already_clicked');
		$('.gettingstarted_info_box_extra').hide();
		$('.js_view_more_part').show();
		$('.js_view_more_full').hide();	
		$('.js_info_toggle_show_less').hide();
		$('.js_info_toggle_show_more').show();	
	}	
}
String.prototype.ucwords = function() {
    str = this.toLowerCase();
    return str.replace(/(^([a-zA-Z\p{M}]))|([ -][a-zA-Z\p{M}])/g,
        function($1){
            return $1.toUpperCase();
        });
}
String.prototype.capitalizeFirstLetter = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
}

$Behavior.yncapitalizeCateTitle = function(){
	if ($('#page_gettingstarted_index').length)
	{
		$( '#content .kblist_block .title a' ).each(function(  ) {
			 var title = $(this).html();
			 title = title.toLowerCase();
			 title = title.capitalizeFirstLetter();
			  $(this).html(title);
		});
	}
}
