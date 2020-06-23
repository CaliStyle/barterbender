{if !empty($aField.data)}
    <div class="ync-detail-custom-fields-item">
        <div class="ync-detail-customfield-title">{phrase var=$sPhraseVarName}</div>
        <div class="ync-detail-customfield-info">{$aField.data}</div>
    </div>
{/if}