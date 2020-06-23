<div class="panel panel-default">
    <form id="js_ynsa_add_package_form" action="{url link='admincp.socialad.package.add'}" method="post">
        <input type="hidden" value="{value type='input' id='package_id'}" name="val[package_id]">
        <input type="hidden" value="{value type='input' id='package_id'}" name="val[package_id]">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='add_new_package'}
            </div>
        </div>
        <div class="panel-body">
        <!-- package name -->
        <div class="form-group">
            <label for="package_name">{required} {_p var='package_name'} </label>
            <input class="form-control" type="text" name="val[package_name]" value="{value type='input' id='package_name'}" size="25" id="package_name" />
            <div class="extra_info"></div>
        </div>

        <div class="form-group">
            <!-- package description -->
            <label for="package_description">{_p var='package_description'} </label>
            <textarea class="form-control" name="val[package_description]" cols="60" rows="8" id="package_description" style="width:97%;">{value type='textarea' id='package_description'}</textarea>
        </div>

        <div class="form-group">
            <!-- package price -->
            <label for="">{required} {_p var='price'} </label>
            <div class="form-inline" id="js_ynsa_price_div">
                <input type="text" class="form-control" name="val[package_price]" value="{value type='input' id='package_price'}" size="25" id="js_ynsa_package_price" />
                <input type="hidden"  name='val[package_currency]' class="form-control" value="{$aCurrentCurrencies.currency_id}" readonly> {$aCurrentCurrencies.currency_id}
            </div>
            <div id="js_free_div">
                <input type="checkbox" name="val[package_is_free]" id="js_ynsa_free_checkbox"
                {if isset($aForms) && $aForms.package_price == 0}
                    checked="checked"
                {/if}
                /> {_p var='free_package'}
            </div>
        </div>


        <div class="form-group">
            <label for="">
                {required} {_p var='objective'}
            </label>
            <div class="extra_info">{_p var='objective_description'}</div>
            <div class="form-inline" id="js_ynsa_benefit_div">
                <input type="text" class="form-control" name="val[package_benefit_number]" value="{value type='input' id='package_benefit_number'}" size="25" id="js_ynsa_package_benefit_number_input" />
                <select  name='val[package_benefit_type_id]' class="form-control">
                    <!-- it is stupid, sorry! -->
                    {foreach from=$aBenefitTypes item=aBenefit}
                    <option {if isset($aForms) && $aForms.package_benefit_type_id == $aBenefit.id} selected="selected" {/if} value="{$aBenefit.id}">{$aBenefit.phrase}</option>
                    {/foreach}
                </select>
            </div>

            {*
            <div class="table_right" id="js_free_div">
                <input type="checkbox" name="val[package_is_unlimited]" id="js_ynsa_unlimited_checkbox"
                {if isset($aForms) && $aForms.package_is_unlimited}
                    checked="checked"
                {/if}
                /> {_p var='unlimited'}
            </div>
            *}

        </div>

            <!-- package active -->
            <div class="form-group">
                <label for="package_is_active">
                    {_p var='active'}:
                </label>
                <div class="item_is_active_holder">
                    <span class="js_item_active item_is_active">
                        <input class='form-control' type="radio" name="val[package_is_active]" value="1" {value type='radio' id='package_is_active' default='1' selected='true'}/> {_p var='yes'}
                    </span>
                    <span class="js_item_active item_is_not_active">
                        <input class='form-control' type="radio" name="val[package_is_active]" value="0" {value type='radio' id='package_is_active' default='0' }/> {_p var='no'}
                    </span>
                </div>
            </div>

            <!-- modules list -->
            <div class="form-group dont-unbind-children">
                <label for="js_ynsa_package_choose_module">
                    {_p var='allowed_modules'}:
                </label>
                <select class="ynsaMultipleChosen form-control" multiple="multiple" data-placeholder="{_p var='all_modules'}" name="val[package_allow_module][]" id="js_ynsa_package_choose_module">
                    {foreach from=$aModules key=mKey item=sModuleId}
                        <option value="{$mKey}"
                            {if isset($aForms) && isset($aForms.package_allow_module) && $aForms.package_allow_module}
                                {foreach from=$aForms.package_allow_module item=sChosen}
                                    {if $sChosen == $mKey}
                                        selected="selected"
                                    {/if}
                                {/foreach}
                            {/if}
                        > {$sModuleId} </option>
                    {/foreach}
                </select>
            </div>

            <!-- block list -->
            <div class="form-group dont-unbind-children">
                <label for="js_ynsa_package_choose_block">
                    {_p var='allowed_blocks'}:
                </label>
                <select class="ynsaMultipleChosen form-control" multiple="multiple" data-placeholder="{_p var='all_blocks'}" name="val[package_allow_block][]" id="js_ynsa_package_choose_block">
                    {foreach from=$aBlocks item=iBlockId}
                        <option value="{$iBlockId}"
                            {if isset($aForms) && isset($aForms.package_allow_block) && $aForms.package_allow_block}
                                {foreach from=$aForms.package_allow_block item=sChosen}
                                    {if $sChosen == $iBlockId}
                                        selected="selected"
                                    {/if}
                                {/foreach}
                            {/if}
                        > {_p var='block_sa'} {$iBlockId}</option>
                    {/foreach}
                </select>
            </div>

            <!-- Item Type List -->
            <div class="form-group dont-unbind-children">
                <label for="js_ynsa_package_choose_item_type">
                    {_p var='allowed_item_types'}:
                </label>
                <select class="ynsaMultipleChosen form-control" multiple="multiple" data-placeholder="{_p var='all_item_types'}" name="val[package_allow_item_type][]" id="js_ynsa_package_choose_item_type">
                    {foreach from=$aItemTypes item=aItemType}
                        <option value="{$aItemType.id}"
                            {if isset($aForms) && isset($aForms.package_allow_item_type) && $aForms.package_allow_item_type}
                                {foreach from=$aForms.package_allow_item_type item=sChosen}
                                    {if $sChosen == $aItemType.id}
                                        selected="selected"
                                    {/if}
                                {/foreach}
                            {/if}
                        >  {$aItemType.phrase}</option>
                    {/foreach}
                </select>
            </div>

            <!-- Item Type List -->
            <div class="form-group dont-unbind-children">
                <label for="js_ynsa_package_choose_ad_type">
                    {_p var='allowed_ad_types'}:
                </label>
                <select class="ynsaMultipleChosen form-control" multiple="multiple" data-placeholder="{_p var='all_ad_types'}" name="val[package_allow_ad_type][]" id="js_ynsa_package_choose_ad_type">
                    {foreach from=$aAdTypes item=aAdType}
                        <option value="{$aAdType.id}"
                            {if isset($aForms) && isset($aForms.package_allow_ad_type) && $aForms.package_allow_ad_type}
                                {foreach from=$aForms.package_allow_ad_type item=sChosen}
                                    {if $sChosen == $aAdType.id}
                                        selected="selected"
                                    {/if}
                                {/foreach}
                            {/if}
                        >  {$aAdType.phrase}
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" value="{_p var='submit'}" class="btn btn-primary" name="val[approve]" />
        </div>


    </form>
</div>
<script type="text/javascript" >
$Behavior.ynsaInitAddPackageForm = function() {l}
	ynsocialad.package.addForm.init();
{r};

</script>
