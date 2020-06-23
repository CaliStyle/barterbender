{if !empty($aField.data)}
    <div class="ync-detail-custom-fields-item">
        <div class="ync-detail-customfield-title">{_p var=$sPhraseVarName}</div>
        <div class="ync-detail-customfield-info">{if $aField.data == "yes"}{phrase var="advancedmarketplace.yes"}{else}{phrase var="advancedmarketplace.no"}{/if}</div>
    </div>
{/if}