<?php 
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" action="{url link='admincp.directory.globalsettings'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='global_settings'}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="theme">{phrase var='default_theme_for_business'}:</label>
                <div class="row">
                    <div class="col-md-3">
                        <div>
                            <a class="item-image" href="{$core_path}module/directory/static/image/theme_1.png" target="_blank">
                                <img src="{$core_path}module/directory/static/image/theme_1.png">
                            </a>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" value="1" name="val[theme]"
                                       {if isset($aGlobalSetting) && ($aGlobalSetting.default_theme_id == 1)}
                                       checked="checked"
                                       {/if}
                                > {_p var='Theme 1'}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div>
                            <a class="item-image" href="{$core_path}module/directory/static/image/theme_2.png" target="_blank">
                                <img src="{$core_path}module/directory/static/image/theme_2.png">
                            </a>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" value="2" name="val[theme]"
                                       {if isset($aGlobalSetting) && ($aGlobalSetting.default_theme_id == 2)}
                                       checked="checked"
                                       {/if}
                                > {_p var='Theme 2'}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="feature_fee">{phrase var='fee_to_feature_business'}:</label>
                <div class="row">
                    <div class="col-md-2">
                        <input class="form-control" type="text" name="val[feature_fee]" size="30" maxlength="100" value="{if isset($aGlobalSetting)}{$aGlobalSetting.default_feature_fee}{/if}" />
                    </div>
                    <div class="col-md-4">
                        <p class="help-block"><i>({$aCurrentCurrencies.0.currency_id}) {phrase var='for_1_day'}.</i></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel-footer">
            <input type="submit" value="{phrase var='submit'}" class="btn btn-primary">
        </div>
    </div>
</form>

