<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 17, 2020, 12:24 am */ ?>
<?php

?>
<div class="sticky-label-icon sticky-pending-icon <?php if ($this->_aVars['aItem']['view_id'] != 1): ?>hide<?php endif; ?>">
    <span class="flag-style-arrow"></span>
    <i class="ico ico-clock-o"></i>
</div>

<?php if ($this->_aVars['dataSource'] != 'sponsored'): ?>
<div class="sticky-label-icon sticky-sponsored-icon <?php if (! $this->_aVars['aItem']['is_sponsor']): ?>hide<?php endif; ?>">
    <span class="flag-style-arrow"></span>
    <i class="ico ico-sponsor"></i>
</div>
<?php endif; ?>

<div class="sticky-label-icon sticky-featured-icon <?php if (! $this->_aVars['aItem']['is_featured']): ?>hide<?php endif; ?>">
    <span class="flag-style-arrow"></span>
    <i class="ico ico-diamond"></i>
</div>

