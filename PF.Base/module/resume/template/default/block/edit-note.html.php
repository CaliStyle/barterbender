<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         VuDP, TienNPL, TrucPTM
 * @package        Module_Resume
 * @version        3.01
 * 
 */?>
 
 <form method="post" onsubmit="$(this).ajaxCall('resume.updateNote');return false;">
 	<div><input type="hidden" name="resume_id" value="{$aView.resume_id}" /></div>
	<div class="table form-group">
		<div class="table_left">
			{_p var='note'}:
		</div>
		<div class="table_right">
			<textarea class="form-control" cols="55" rows="7" name="text" id="note">{$aView.note}</textarea>
		</div>
		<div>
			({_p var='maximum_note_length'})
		</div>
	</div>
	<div class="table_clear">
		<input type="submit" name = "note[submit]" value="{phrase var='core.submit'}" class="button btn btn-primary btn-sm" />
	</div>
 </form>
 
