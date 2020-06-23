<?php
/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');
?>
{if count($aMenus)}
<div id="menu_edit_{$module_name}" class="page_section_menu page_section_menu_header">
	<ul class="action">
		{foreach from=$aMenus key=sPageSectionKey item=sPageSectionMenu}
		<li {if $sPageSectionKey == 'detail'} class="active" {/if}>
		<a href="#" 
		class="dont-unbind"
		onclick='$("#show-side-panel").trigger("click");' 
		rel="js_{$module_name}_block_{$sPageSectionKey}">{$sPageSectionMenu}</a>
		</li>
		{/foreach}
		<li>
			<a href="{$sLink}">{$sView}</a>
		</li>
	</ul>
</div>
{/if}

<script type="text/javascript">
var current_tab = '{$current_tab}';
var module_name = '{$module_name}';
{literal}
// current_tab in add.class.php
$Behavior.ynPetitionCurrentTab = function()
{
	$( document ).ready(function() {
		 
	 {
		 if (current_tab>1)
		 {
			$( "#menu_edit_" + module_name +  " ul li:nth-child("+current_tab+") a" ).trigger('click');	 
		 }else
		 {
		 }
	 }
	 
	 
	 Editor.setId("description");

     $("a[rel='js_petition_block_detail']").bind("click", function(){
            Editor.setId("description");
            
});


$("a[rel='js_petition_block_letter']").bind("click", function(){
          Editor.setId("letter");
});
		
	});
}
{/literal}

</script>