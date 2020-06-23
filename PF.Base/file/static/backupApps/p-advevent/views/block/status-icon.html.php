<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="sticky-label-icon sticky-pending-icon {if $aItem.view_id != 1}hide{/if}">
    <span class="flag-style-arrow"></span>
    <i class="ico ico-clock-o"></i>
</div>

{if $dataSource != 'sponsored'}
<div class="sticky-label-icon sticky-sponsored-icon {if !$aItem.is_sponsor}hide{/if}">
    <span class="flag-style-arrow"></span>
    <i class="ico ico-sponsor"></i>
</div>
{/if}

<div class="sticky-label-icon sticky-featured-icon {if !$aItem.is_featured}hide{/if}">
    <span class="flag-style-arrow"></span>
    <i class="ico ico-diamond"></i>
</div>
