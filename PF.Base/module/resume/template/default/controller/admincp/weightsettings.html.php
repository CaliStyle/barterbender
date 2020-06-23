<style type="text/css">
    {literal}
    .space_weight >ul >li{
        padding: 10px 0 10px 0;
        border-bottom: 1px solid #DFDFDF;
    }

    .space_weight >ul >li >b{
        font-weight: bold;
    }

    .link_profile_edit_weight{
        padding: 10px 0;
        font-size: 0.5cm;
    }
    .link_profile_edit_weight a:hover{

        text-decoration: underline !important;

    }
    .admin_notice {
        margin: 0px 0px 5px 0px;
    }
    {/literal}
</style>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {_p var='admin_menu_weight_settings'}
        </div>
    </div>
    <div class="table-responsive">
        <table cellpadding="0" cellspacing="0" class="table table-bordered">
            <tr>
                <th>{_p('Field Name')}</th>
                <th>{_p('Weight')}</th>
            </tr>
            {foreach from=$aRows key=iKey item=aRow}
                <tr class="checkRow tr" style="border-bottom: 1px #ebebeb solid;">
                    <td>{$aRow.phrase}</td>
                    <td>+{$aRow.score}</td>
                </tr>
            {/foreach}
        </table>
    </div>
    <div class="panel-footer">
        <a class="btn btn-primary" href="{url link='admincp.resume.editweightsettings'}">{_p var='edit_weight_of_resume_fields'}</a>
    </div>
</div>