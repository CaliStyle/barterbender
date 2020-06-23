<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 2/15/17
 * Time: 09:42
 */
?>


<form method="post" action="{url link='admincp.yncaffiliate.edit-commission-rule'}">
    <div class="panel panel-default">
        <!-- Filter Search Form Layout -->
        <div class="panel-heading">
            <div class="panel-title">
                {_p var=$aRuleDetail.rule_title}
            </div>
        </div>
        <input type="hidden" name="rulemap" value="{$iRuleMapId}" />
        <input type="hidden" name="rule" value="{$iRuleId}" />
        <input type="hidden" name="group" value="{$iGroupId}" />
        <div class="panel-body">
            <div class="form-group">
                {foreach from=$aMapDetail item=aDetail key=iKey}
                    <label for="">
                        {_p('Level')} {$aDetail.rule_level}
                    </label>
                    <input class="form-control" type="text" name="val[level_{$aDetail.rule_level}][rule_value]" value="{if $aDetail.rule_value > 0}{$aDetail.rule_value}{/if}">
                    <input type="hidden" name="val[level_{$aDetail.rule_level}][rulemapdetail_id]" value="{$aDetail.rulemapdetail_id}">
                    <input type="hidden" name="val[level_{$aDetail.rule_level}][rule_level]" value="{$aDetail.rulemapdetail_id}">
                {/foreach}
                <input  type="checkbox" name="val[is_active]" {if $iRuleMapActive}checked{/if} id="ynaf_rule_active"> <label for="ynaf_rule_active">{_p('Active')}</label>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" class="btn btn-primary" value="{_p('Update')}" name="submit"/>
        </div>
</form>
{literal}
<script type="text/javascript">
    $Ready(function(){
        if($('.apps_menu').length == 0) return false;
        $('.apps_menu > ul').find('li:eq(3) a').addClass('active');
    });
</script>
{/literal}