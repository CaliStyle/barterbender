<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if !isset($sView) || $sView != 'pending'}
<div class="sticky-label-icon sticky-pending-icon {if $aListing.view_id != 1}hide{/if}">
    <span class="flag-style-arrow"></span>
    <i class="ico ico-clock-o"></i>
</div>
{/if}

{if !isset($sView) || $sView != 'featured'}
<div class="sticky-label-icon sticky-sponsored-icon {if !$aListing.is_sponsor}hide{/if}">
    <span class="flag-style-arrow"></span>
    <i class="ico ico-sponsor"></i>
</div>
{/if}

{if !isset($sView) || $sView != 'sponsor'}
<div class="sticky-label-icon sticky-featured-icon {if !$aListing.is_featured}hide{/if}">
    <span class="flag-style-arrow"></span>
    <i class="ico ico-diamond"></i>
</div>
{/if}