<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>


<div>
<h3 class="yns add-res">
	<ul class="yns menu-add">
		<li>{_p var='additional_information'}</li>
	</ul>
	</h3>
</div>

<form method="post" id="yresume_add_form" enctype="multipart/form-data">

<div id="headline">

	<div class="table form-group">
		<div class="table_left table_left_add">
			<label for="website">{_p var='websites'}:</label>
		</div>
		<div class="table_right">
			{if $bIsEdit && count($aForms.website)>0}
			{foreach from=$aForms.website item=sWebsite}
			<div class="placeholder">
            	<div class="js_prev_block">
                	<span class="class_answer">
                    	<input type="text" name="val[website][]" value="{$sWebsite}" size="30" class="js_predefined_websites v_middle form-control" />
                    </span>
                    <a href="#" class="add_icon" onclick="return appendPredefined(this,'website');">
                    	{img theme='misc/add.png' class='v_middle'}
                    </a>
                    <a href="#" class="remove_icon" onclick="return removePredefined(this,'website');">
                    	{img theme='misc/delete.png' class='v_middle'}
                    </a>
                    </div>
               </div>

			{/foreach}
			{else}
			<div class="placeholder">
            	<div class="js_prev_block">
                	<span class="class_answer">
                    	<input type="text" name="val[website][]" value="" size="30" class="js_predefined_websites v_middle form-control" />
                    </span>
                    <a href="#" class="add_icon" onclick="return appendPredefined(this,'website');">
                    	{img theme='misc/add.png' class='v_middle'}
                    </a>
                    <a href="#" class="remove_icon" onclick="return removePredefined(this,'website');">
                    	{img theme='misc/delete.png' class='v_middle'}
                    </a>
                    </div>
               </div>

			{/if}
			</div>
		</div>


	<div class="table form-group">
		<div class="table_left table_left_add">
			<label for="sport">{_p var='sport'}:</label>
		</div>
		<div class="table_right">
			<textarea class="form-control" cols="70" rows="5" name='val[sport]'>{if $bIsEdit and isset($aForms.sport)}{$aForms.sport}{/if}</textarea>
		</div>
	</div>

	<div class="table form-group">
		<div class="table_left table_left_add">
			<label for="movies">{_p var='movies'}:</label>
		</div>
		<div class="table_right">
			<textarea class="form-control" cols="70" rows="5" name='val[movies]'>{if $bIsEdit and isset($aForms.movies)}{$aForms.movies}{/if}</textarea>
		</div>
	</div>

	<div class="table form-group">
		<div class="table_left table_left_add">
			<label for="interests">{_p var='interests'}:</label>
		</div>
		<div class="table_right">
			<textarea class="form-control" cols="70" rows="5" name='val[interests]'>{if $bIsEdit and isset($aForms.interests)}{$aForms.interests}{/if}</textarea>
		</div>
	</div>

	<div class="table form-group">
		<div class="table_left table_left_add">
			<label for="music">{_p var='music'}:</label>
		</div>
		<div class="table_right">
			<textarea class="form-control" cols="70" rows="5" name='val[music]'>{if $bIsEdit and isset($aForms.music)}{$aForms.music}{/if}</textarea>
		</div>
	</div>



		<div class="table_clear ">
			<input type="submit" class="button btn btn-primary btn-sm" value = "{_p var='update'}"/>
		</div>

</div>

</form>
{literal}
<script type="text/javascript">
    $Behavior.onAddResumeCheckDefine = function(){
        iCnt = 0;      
        $('.js_predefined_websites').each(function()
        {
            iCnt++;
        });
        if (iCnt <= 1)
        {
            $('.js_prev_block .remove_icon').css('display','none');            
            return false;
        }    
    }
</script>
{/literal}