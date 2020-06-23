<?php 
;

if (Phpfox::isModule('directory') && isset($this->_aVars['yndirectory_module']) && $this->_aVars['yndirectory_module'] == 'directory' && isset($this->_aVars['yndirectory_item']) && $this->_aVars['yndirectory_item'] != false)
{
?>
    <div><input type="hidden" name="val[yndirectory_module]" value="<?php echo $this->_aVars['yndirectory_module']; ?>"></div>
    <div><input type="hidden" name="val[yndirectory_item]" value="<?php echo $this->_aVars['yndirectory_item']; ?>"></div>
<?php
}

;
?>