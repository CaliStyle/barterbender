
<div class="">
	
<div id="menu_create_resume" class="page_section_menu page_section_menu_header">
    <ul class="action">
        <li class="yns-add {if $sTabView=='add'}active{/if}">
        	<a
				class="{if $sTabView=='add'}ynresume_active_menu{/if}"
				href="{url link='resume.add'}{if isset($id) && $id!=0}id_{$id}/{/if}">
				{_p var='basic_information'}</a>
        </li>
        
        {if $typesession>1}
        <li class="yns-summary {if $sTabView=='summary'}active{/if}"><a
				class="{if $sTabView=='summary'}ynresume_active_menu{/if}"
				href="{url link='resume.summary'}{if isset($id) && $id!=0}id_{$id}/{/if}">{_p var='summary'}</a></li>
        {else}
        <li><a href="#" class="btn disabled">{_p var='summary'}</a></li>
        {/if}
        
        {if $typesession>2}
        <li class="yns-experience {if $sTabView=='experience'}active{/if}"><a
				class="{if $sTabView=='experience'}ynresume_active_menu{/if}"
				href="{url link='resume.experience'}{if isset($id) && $id!=0}id_{$id}/{/if}">{_p var='experience'}</a></li>
        {else}
        <li><a href="#" class="btn disabled">{_p var='experience'}</a></li>
        {/if}
        
        {if $typesession>3}
        <li class="yns-education {if $sTabView=='education'}active{/if}"><a
				class="{if $sTabView=='education'}ynresume_active_menu{/if}"
				href="{url link='resume.education'}{if isset($id) && $id!=0}id_{$id}/{/if}">{_p var='education'}</a></li>
        {else}
        <li><a href="#" class="btn disabled">{_p var='education'}</a></li>
        {/if}
        
        {if $typesession>4}
        <li class="yns-skill {if $sTabView=='skill'}active{/if}"><a
				class="{if $sTabView=='skill'}ynresume_active_menu{/if}"
				href="{url link='resume.skill'}{if isset($id) && $id!=0}id_{$id}/{/if}">{_p var='add_skill_expertise'}</a></li>
        {else}
        <li><a href="#" class="btn disabled">{_p var='add_skill_expertise'}</a></li>
        {/if}


				{if $typesession>5}
				<li class="yns-certi {if $sTabView=='certification'}active{/if}"><a
						class="{if $sTabView=='certification'}ynresume_active_menu{/if}"
						href="{url link='resume.certification'}{if isset($id) && $id!=0}id_{$id}/{/if}">{_p var='certifications'}</a></li>
				{else}
				<li><a href="#" class="btn disabled">{_p var='certifications'}</a></li>
				{/if}
				
				{if $typesession>6}
				<li class="yns-lang {if $sTabView=='language'}active{/if}"><a
						class="{if $sTabView=='language'}ynresume_active_menu{/if}"
						href="{url link='resume.language'}{if isset($id) && $id!=0}id_{$id}/{/if}">{_p var='languages'}</a></li>
				{else}
				<li><a href="#" class="btn disabled">{_p var='languages'}</a></li>
				{/if}
				
				{if $typesession>7}
				<li class="yns-public {if $sTabView=='publication'}active{/if}"><a
						class="{if $sTabView=='publication'}ynresume_active_menu{/if}"
						href="{url link='resume.publication'}{if isset($id) && $id!=0}id_{$id}/{/if}">{_p var='publications'}</a></li>
				{else}
				<li><a href="#" class="btn disabled">{_p var='publications'}</a></li>
				{/if}
				
				{if $typesession>8}
				<li class="yns-addition {if $sTabView=='addition'}active{/if}"><a
						class="{if $sTabView=='addition'}ynresume_active_menu{/if}"
						href="{url link='resume.addition'}{if isset($id) && $id!=0}id_{$id}/{/if}">{_p var='additional_information'}</a></li>
				{else}
				<li><a href="#" class="btn disabled">{_p var='additional_information'}</a></li>
				{/if}        
        {if $bIsEdit}
		<li>
		    <a href="{permalink module='resume.view' id=$id}">{_p var='view_my_resume'}</a>
	    </li> 
	    {/if}
    </ul> 
</div>
</div>


<script type="text/javascript">
var typesession = '<?php echo $_SESSION['showmenu']; ?>';
{literal}
	$Behavior.loadMenuAdd = function(){
		if(typesession==0)
		{
			$('.yns-more-option ul').hide();
			$('.yns-more-option > a').addClass('more-down');
		}
		else
		{
			$('.yns-more-option ul').show();
			$('.yns-more-option').css('padding-bottom','26px');
			$(this).removeClass('more-down');
		}
		$('.yns-more-option > a').bind('click',function() {
			  if($(this).hasClass('more-down'))
			  {
				$('.yns-more-option').css('padding-bottom','26px');
				$('.yns-more-option ul').slideDown();
				$(this).removeClass('more-down');
			  }
			  else
			  {
				$('.yns-more-option ul').slideUp();
				$('.yns-more-option').css('padding-bottom','0px');
				$(this).addClass('more-down');
			  }
	        }
		);
	};
	{/literal}
</script>


