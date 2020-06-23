{literal}
    <style>
        .ultimatevideo-table {
            border-bottom: 1px solid #dbdbdb;
        }


        .ultimatevideo-table .ultimatevideo-table_left {
            width: 70%;
            padding: 10px 0px;
            float: left;
            font-weight: bold;
            box-sizing: border-box;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
        }

        .ultimatevideo-table .ultimatevideo-table_right {
            width: 30%;
            padding: 10px 0px;
            float: left;
            box-sizing: border-box;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
        }
    </style>
{/literal}

{if count($aGroupsInfo)}
    <span style="font-weight: bold;font-size: 14px;display: block;text-align: center;margin-bottom: 10px;">{softPhrase var=$sCategory.title}</span>
    <div class="ultimatevideo-table clearfix">
        <div class="ultimatevideo-table_left">
            {_p('custom_field_group')}
        </div>
        <div class="ultimatevideo-table_right">
            {_p('Option')}
        </div>
    </div>
    {foreach from=$aGroupsInfo item=aGroup}
        <div class="ultimatevideo-table clearfix">
            <div class="ultimatevideo-table_left">
                {$aGroup.phrase_var_name}
            </div>
            <div class="ultimatevideo-table_right">
                <a href="javascript:void(0)" data-id="{$aGroup.group_id}"
                   onclick="js_box_remove($('.js_box_close')); return $Core.ultimatevideo.editCustomgroupFromPopup(this)">{_p('Edit')}</a>
            </div>
        </div>
    {/foreach}
{else}
    {_p('there_are_no_custom_groups_associate_with_this_category')}
{/if}
