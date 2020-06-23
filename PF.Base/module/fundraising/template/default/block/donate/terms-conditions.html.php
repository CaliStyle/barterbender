<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="ynfr term">
<p class="select-amount extra_info">{phrase var='campaign_s_terms_and_conditions'}</p>
<p>
{$aCampaign.term_condition}
</p>
</div>
<div class="ynfr term-agree checkbox"><label><input type="checkbox" class="required" style="" name="val[is_agree]" >{phrase var='i_have_read_and_agreed_with_all_terms_and_conditions'}</label>       </div>
