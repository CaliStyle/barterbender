<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{assign var='bSlideForm' value=1}

{if $bAllowRegistration}
    <div class="js-slide-visitor-form sign-up" data-title="{ _p var='sign_up' }">
        {template file='user.controller.register'}
    </div>
{/if}