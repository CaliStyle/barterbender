<style type="text/css">
    {literal}
    .space_weight >ul >li{
        padding: 10px 0 10px 0;
        border-bottom: 1px solid #DFDFDF;
        position: relative;
        overflow: hidden;

    }

    .space_weight >ul >li >span{
        position: absolute;
        left: 250px;
        bottom: 4px;
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

<form method="post" action="{url link='admincp.resume.editweightsettings'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='edit_weight_of_resume_fields'}
            </div>
        </div>
            <div class="table-responsive">
                <table id="" class="table table-bordered" cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{_p('Field Name')}</th>
                            <th class="w220">{_p('Weight')}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$aRows item=aRow}
                        <tr class="checkrow tr form-group">
                            <td>{$aRow.phrase|clean|shorten:40:'...'|split:40}</td>
                            <td><input class="form-control" type="text" value="<?php if(isset($this->_aVars['aForms'][$this->_aVars['aRow']['name']])) echo $this->_aVars['aForms'][$this->_aVars['aRow']['name']] ; else ?>{$aRow.score}<?php ; ?>" name="val[{$aRow.name}]"></td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
            <div class="panel-footer">
                <input type="submit" class="btn btn-primary" name="save" id="SaveChangesProfile" value="{_p var='save_changes'}"/>
                <input type="button" class="btn btn-default" onclick="window.location.href= '{url link='admincp.resume.weightsettings'}'" name="cancel" id="" value="{_p var='cancel'}"/>
            </div>
    </div>
</form>
