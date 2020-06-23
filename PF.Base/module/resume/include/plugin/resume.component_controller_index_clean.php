<?php
	$title_search = _p('resume.advanced_search');
	$view = $this->request()->get('view');
	$textImport = _p('resume.import_from_linkedin');
	$textCreate = _p('resume.create_new_resume');
	$numberofresume = Phpfox::getService("resume.basic")->getItemCount('rbi.user_id='.Phpfox::getUserId());
	$total_allowed = Phpfox::getUserParam("resume.maximum_resumes");
	$is_import = true;
	$bIsAddedModule = false;
	// check linkedin enabled
	if(Phpfox::isModule('socialbridge'))
	{
		$config = Phpfox::getService('socialbridge') -> getSetting('linkedin');
		if(Phpfox::getService('socialbridge') -> hasProvider('linkedin') && !empty($config['api_key']) && !empty($config['secret_key']))
		{
			$bIsAddedModule = true;
		}
	}

	if($total_allowed > 0 && $numberofresume >= $total_allowed)
	{
		$is_import = false;
	}
	$core_path = Phpfox::getParam('core.path_file');
	$returnUrl = PHpfox::getLib("url")->makeUrl("resume.import");
	$url = $core_path.'module/socialbridge/static/php/linkedin.php?callbackUrl='.urlencode($returnUrl);
	$icon = '<img class="v_middle" alt="" src="'.$core_path.'theme/frontend/default/style/default/image/layout/section_menu_add.png">';
?>
<script type="text/javascript">
   $Behavior.loadContentResume = function(){
   <?php if(PHpfox::getLib("module")->getControllerName()=="index" && !$view)
	{?>
       var sClass = 'btn btn-sm btn-primary';
       if ($('#page_resume_index .header-filter-holder').length) {
           sClass = 'btn btn-xs btn-primary';
       }
		var content = '<div class="inline-block"><a id="advsearch" class="'+sClass+'" onclick="advSearchDisplay();return false;tb_show(\'<?php echo $title_search; ?>\',$.ajaxBox(\'resume.advancedsearch\'))" href="javascript:void(0)"><?php echo $title_search; ?></a></div>';
	   if($('#page_resume_index').length && $('._is_profile_view').length == 0)
	   {
		   if (!$('#advsearch').length)
		   {
			   $('#page_resume_index .header_filter_holder').append(content);
			   $('#page_resume_index .header-filter-holder').append(content);
		   }
	   }
	   <?php }?>
   }
</script>

<script language="javascript">
    function openWindow(anchor, options) {
        var w = 990;
        var h = 560;

        var title = "Linkedin Import",
            left = (screen.width / 2) - (w / 2),
            top = (screen.height / 2) - (h / 2);
        var props = [
            'toolbar=no',
            'location=no',
            'directories=no',
            'status=no',
            'menubar=no',
            'scrollbars=yes',
            'resizable=yes',
            'copyhistory=no',
            'width=' + w,
            'height=' + h,
            'top=' + top,
            'left=' + left
        ].join(',');
        var newwindow = window.open(anchor, title, props);

        if (window.focus) {
            newwindow.focus();
        }
        return newwindow;
    }

    $Behavior.loadMenuResume = function(){
    $().ready(function(){
    	<?php if(Phpfox::getUserParam('resume.can_create_resumes')){?>
                 if(!$Core.exists('#create_resume')){
	    	$('#section_menu').eq(0).find('ul').append('<li id="create_resume"><a href="<?php echo Phpfox::getLib("url")->makeUrl('resume.add'); ?>"><?php  echo $icon.$textCreate;?></a></li>');
                }
	    	<?php if($bIsAddedModule){?>
                    if(!$Core.exists('#resume_import')) {
                        <?php if($is_import){?>
                        if ($('#page_resume_index').length)
                            $('.breadcrumbs_menu').eq(0).find('ul').append('<li id="resume_import"><a onclick="openWindow(\'<?php echo $url; ?>\',{width:430,height:550,center:true});return false;" href="#"><?php  echo $icon . $textImport;?></a></li>');
                        $('body[id^="page_resume"] .page_breadcrumbs_menu').find('.js_resume_import_from_linkedin').remove();
                        $('body[id^="page_resume"] .page_breadcrumbs_menu').append('<a id="add_linkedIn" class="btn btn-warning" onclick="openWindow(\'<?php echo $url; ?>\',{width:430,height:550,center:true});return false;" href="#"><?php echo '+ ' . $textImport;?></a>');
                        //Support material
                        $('body[id^="page_resume"] .breadcrumbs_right_section ul.dropdown-menu').find('.js_resume_import_from_linkedin').closest('li').remove();
                        $('body[id^="page_resume"] .breadcrumbs_right_section ul.dropdown-menu').append('<li id="add_linkedIn"><a onclick="openWindow(\'<?php echo $url; ?>\',{width:430,height:550,center:true});return false;" href="#"><?php echo $textImport;?></a></li>');
                        <?php }else{ ?>
                        //$('#section_menu').eq(0).find('ul').append('<a onclick="$Core.box(\'resume.alertimport\');return false;" href="#"><?php  echo $icon . $textImport;?></a></li>');
                        <?php } ?>
                    }
		    <?php } ?>
    	<?php } ?>

		$('#section_menu').eq(0).find('ul>li').each(function(item,value){
		    if($(value).attr('id')!="viewresume" && $(value).attr('id')!="create_resume" && $(value).attr('id')!="resume_import")
		    {
		       $(value).remove();
		    }
	    });
    });
}
</script>
