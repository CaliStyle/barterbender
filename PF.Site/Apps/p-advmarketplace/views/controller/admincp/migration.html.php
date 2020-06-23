<form method="post" action="{url link='admincp.advancedmarketplace.migration'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='advancedmarketplace.migration'}
            </div>
        </div>
        <div class="panel-body">
            {phrase var='advancedmarketplace.migration_notice'}
        </div>
        <div class="panel-footer">
            <input class="btn btn-primary" type="button" value="Confirm" onclick="window.location.href='{$sMigrateUrl}'"/>
        </div>
    </div>
</form>