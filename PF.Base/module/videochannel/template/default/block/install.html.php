<?php 
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
	{if isset($aForms.ffmpeg) && $aForms.ffmpeg != '' && isset($aForms.mencoder) && $aForms.mencoder != ''}
		<div class="table_sub_header">
			Video Module
		</div>
		{if isset($aForms.ffmpeg) && $aForms.ffmpeg != ''}
			<div class="table form-group">
				<div class="table_left">
					FFMPEG Path:
				</div>
				<div class="table_right">
					<input type="text" name="val[ffmpeg]" value="{value type='input' id='ffmpeg'}" size="30" />
				</div>
				<div class="clear"></div>
			</div>
		{/if}
		{if isset($aForms.mencoder) && $aForms.mencoder != ''}
			<div class="table form-group">
				<div class="table_left">
					MENCODER Path:
				</div>
				<div class="table_right">
					<input type="text" name="val[mencoder]" value="{value type='input' id='mencoder'}" size="30" />
				</div>
				<div class="clear"></div>
			</div>
		{/if}
	{/if}