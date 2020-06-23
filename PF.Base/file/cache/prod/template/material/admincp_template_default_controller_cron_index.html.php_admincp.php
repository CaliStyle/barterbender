<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: June 3, 2020, 6:01 pm */ ?>
<?php

?>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title"><?php echo _p("URL for setting up cron jobs"); ?></div>
    </div>
    <div class="panel-body">
        <pre><?php echo $this->_aVars['cron_url']; ?></pre>
        <div class="help-block">
<?php echo _p("Copy the URL then follow the instruction at <a target=\"_blank\" href=\"https://docs.phpfox.com/display/FOX4MAN/Setup+Cron\">here</a> to set up cron jobs for your phpFox site."); ?>
        </div>
    </div>

</div>
