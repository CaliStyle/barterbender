<?php 
;

if (Phpfox::isModule('directory') && isset($this->_aVars['yndirectory_module']) && $this->_aVars['yndirectory_module'] == 'directory')
{
?>
    <div><input type="hidden" name="val[module_id]" value="<?php echo $this->_aVars['yndirectory_module']; ?>"></div>
    <div><input type="hidden" name="val[item_id]" value="<?php echo $this->_aVars['yndirectory_item']; ?>"></div>    
<?php
}

;
?>