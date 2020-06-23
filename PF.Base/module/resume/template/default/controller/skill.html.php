<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<div style="position: relative">
	<h3  class="yns add-res">
		<ul class="yns menu-add">
			<li>{_p var='add_skill_expertise'}</li>
		</ul>
	</h3>
</div>

<form class="resume_add_content" id="yresume_add_form" method="post" enctype="multipart/form-data">

<div id="headline">
	<div class="table form-group">
		<div class="table_left table_left_add">

		</div>
		<div class="table_right">
			<input type="text" class="form-control" name="val[kill_name]" value="" size="20" maxlength="200" id= 'element_name'/>
			<a id="add_more_element" href="#" onclick="javascript:void(0);return false;" class="fa" title="{phrase var ='resume.add_skill_expertise'}">
				{img theme='misc/add.png' class='v_middle'}
			</a>
		</div>
	</div>

	<div class="table form-group" style="display:none">
		<div class="table_left table_left_add">
		</div>
		<div class="table_right">
			<textarea class="form-control" cols="70" rows="5" name='val[kill_list]' id='element_list'>
				{if $bIsEdit}{$aForms.kill_list}{/if}
			</textarea>
		</div>
	</div>

	<div class="table form-group textareaselect ynr-skill">
		<div class="table_left table_left_add">
		</div>
		<div class="table_right tablecontent" >
			{if isset($aForms.akill_list)}
			{foreach from=$aForms.akill_list item=kill}
				<ul class="chzn-choices">
					<li id="selEEW_chzn_c_1" class="search-choice">
						<span>{$kill}</span>
						<a rel="1" class="search-choice-close closeskill" href="javascript:void(0);" onclick="removeElement($(this));return false;" ></a>
					</li>
				</ul>
			{/foreach}
			{/if}
		</div>
	</div>


		<div class="table_clear resume-btn-group">
			<input type="submit" class="button btn btn-primary btn-sm" value = "{_p var='update'}"/>
			<button type="button" class="button btn btn-default btn-sm" onclick="window.location.href='{url link='resume.certification'}id_{$id}'">{_p var='skip'}</button>
		</div>

</div>

</form>
