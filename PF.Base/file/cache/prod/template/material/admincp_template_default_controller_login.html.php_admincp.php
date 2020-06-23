<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 19, 2020, 4:43 am */ ?>
<?php 

?>

<div id="admincp_login">
	<form method="post" action="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('current'); ?>" class="form">
		<div class="adminp_login_body">
            <h3 class="admin_login_title"><?php echo _p('admincp_login'); ?></h3>
            <div class="clearfix">
<?php if (!$this->bIsSample):  $this->getLayout('error');  endif; ?>
                <div class="form-group">
                    <label for="admincp_login_email"><?php echo _p('email'); ?></label>
                    <input required class="form-control" id="admincp_login_email" type="text" name="val[email]" value="<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val')); echo (isset($aParams['email']) ? Phpfox::getLib('phpfox.parse.output')->clean($aParams['email']) : (isset($this->_aVars['aForms']['email']) ? Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aForms']['email']) : '')); ?>
" placeholder="<?php echo _p('email'); ?>" />
                </div>
                <div class="form-group">
                    <label for="admincp_login_password"><?php echo _p('password'); ?></label>
                    <input required type="password" id="admincp_login_password" name="val[password]" class="form-control" value="<?php $aParams = (isset($aParams) ? $aParams : Phpfox::getLib('phpfox.request')->getArray('val')); echo (isset($aParams['password']) ? Phpfox::getLib('phpfox.parse.output')->clean($aParams['password']) : (isset($this->_aVars['aForms']['password']) ? Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aForms']['password']) : '')); ?>
" placeholder="<?php echo _p('password'); ?>" size="40" autocomplete="off"/>
                </div>
                <div class="form-group">
                    <button type="submit" id="admincp_btn_login" class="btn btn-danger"><?php echo _p('login'); ?></button>
                    <a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl(''); ?>" class="no_ajax btn btn-link pull-right"><?php echo _p('back_to_site'); ?></a>
                </div>
            </div>
		</div>
	
</form>

</div>
