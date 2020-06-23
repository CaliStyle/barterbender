<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 19/01/2017
 * Time: 15:18
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
                {_p var='Affiliate Point Conversion Rate'}
        </div>
    </div>
    <form method="post" action="">
        <div class="panel-body">
            <div class="form-group">
                {foreach from=$aCurrencies key=keyCurrency item=Currency}
                <div class="form-group">
                    <label for="">
                        {$keyCurrency}:
                    </label>
                    <input type="text" class="form-control" name="val[{$keyCurrency}]" value="{if isset($aConverionRate[$keyCurrency])}{$aConverionRate[$keyCurrency]}{/if}">
                </div>
                {/foreach}
                <div class="extra_info">
                    {_p var='Define how much an affiliate point is worth for each available currency'}
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" value="{_p('Submit')}" class="btn btn-primary" />
        </div>
    </form>
</div>
