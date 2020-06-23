<?php
/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="placeholder">
	<div class="ynf_js_prev_block">
		<span class="class_answer">
			<input type="text" name="val[predefined][{$iKey}]" value="{$aPredefined}" size="30" class="form-control js_predefined v_middle number greater_than_minimum" />
		</span>
		<a href="#" onclick="return appendPredefined(this);">
			{img theme='misc/add.png' class='v_middle'}
		</a>
		<a href="#" onclick="return removePredefined(this);">
			<img src="{$corepath}module/fundraising/static/image/delete.png" class="v_middle"/>
		</a>
	</div>
</div>
