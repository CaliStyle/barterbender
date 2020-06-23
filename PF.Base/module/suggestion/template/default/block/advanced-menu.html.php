<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_Suggestion
 * @version          3.04
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="suggestion-categories sub_section_menu">
	{$html}
</div>

{literal}
<script type="text/javascript">
	function showSub(id)
		{
			var ul = $('#'+id);
			if(ul.parent().hasClass('open'))
			{
				ul.parent().removeClass();
			}
			else{
				ul.parent().addClass('open');
			}
			
		}
</script>
{/literal}