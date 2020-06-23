<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 17, 2020, 12:24 am */ ?>
<?php

?>
<select class="form-control"
    name="val[<?php echo $this->_aVars['aArgsCountry']['name']; ?>]<?php if ($this->_aVars['bIsMultiple']): ?>[]<?php endif; ?>"
    id="<?php echo $this->_aVars['aArgsCountry']['name']; ?>"
    style="<?php echo $this->_aVars['aArgsCountry']['style']; ?>"
<?php if ($this->_aVars['bIsMultiple']): ?> multiple="multiple" <?php endif; ?>
    >
        <option value=""><?php echo $this->_aVars['aArgsCountry']['value_title']; ?></option>
<?php if (count((array)$this->_aVars['aCountries'])):  foreach ((array) $this->_aVars['aCountries'] as $this->_aVars['sIso'] => $this->_aVars['sCountryName']): ?>
            <option class="js_country_option" id="js_country_iso_option_<?php echo $this->_aVars['sIso']; ?>" value="<?php echo $this->_aVars['sIso']; ?>" <?php if (PHPFOX_IS_AJAX_PAGE && isset ( $this->_aVars['country_iso'] ) && ( $this->_aVars['country_iso'] == $this->_aVars['sIso'] )): ?>selected<?php endif; ?>><?php echo $this->_aVars['sCountryName']; ?></option>
<?php endforeach; endif; ?>
</select>

<?php if (! PHPFOX_IS_AJAX_PAGE && isset ( $this->_aVars['country_iso'] )): ?>
    <script type="text/javascript"> $Behavior.setCountry = function()
        {
        $("#js_country_iso_option_<?php echo $this->_aVars['country_iso']; ?>").prop("selected", true);
        }
    </script>
<?php endif; ?>
