<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 20, 2020, 7:51 pm */ ?>
<?php

?>

<?php if ($this->_aVars['uninstall']): ?>
<div class="panel">
    <div class="panel-body">
	<div class="error_message">
<?php echo _p('to_continue_with_uninstalling__please_enter_your_admin_login_details'); ?>.
	</div>
	<form method="post" action="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('current'); ?>" class="form ajax_post">
		<div class="form-group">
			<label for="email"><?php echo _p('email'); ?></label>
            <input type="text" id="email" name="val[email]" class="form-control">
		</div>

		<div class="form-group">
			<label for="password"><?php echo _p('password'); ?></label>
            <input type="password" id="password" name="val[password]" autocomplete="off" class="form-control">
		</div>
        
		<div style="hide">
            <div class="error_message">
<?php echo _p('please_re_type_your_ftp_account'); ?>
            </div>
            <div class="session_ftp_account">
                <div class="form-group">
                    <label for="method"><?php echo _p('file_upload_method'); ?></label>
                    <select id="method" name="val[method]"
                            onchange="if (this.value=='file_system') $('.hide_file_system_items').hide(); else $('.hide_file_system_items').show();">
<?php if (count((array)$this->_aVars['listMethod'])):  foreach ((array) $this->_aVars['listMethod'] as $this->_aVars['sKey'] => $this->_aVars['sMethod']): ?>
                        <option value="<?php echo $this->_aVars['sKey']; ?>" <?php if ($this->_aVars['sKey'] == $this->_aVars['currentUploadMethod']): ?> selected <?php endif; ?>>
<?php echo $this->_aVars['sMethod']; ?>
                        </option>
<?php endforeach; endif; ?>
                    </select>
                </div>

                <div class="hide_file_system_items <?php if ('file_system' == $this->_aVars['currentUploadMethod']): ?>hide<?php endif; ?>">
                    <div class="form-group">
                        <label for="host_name"><?php echo _p('ftp_host_name'); ?></label>
                        <input type="text" id="host_name" class="form-control" placeholder="<?php echo _p('ftp_host_name'); ?>"  value="<?php echo $this->_aVars['currentHostName']; ?>" name="val[host_name]"/>
                    </div>

                    <div class="form-group">
                        <label for="port"><?php echo _p("Port"); ?></label>
                        <input type="text" class="form-control" id="port" placeholder="<?php echo _p('Port'); ?>" value="<?php echo $this->_aVars['currentPort']; ?>" name="val[port]"/>
                    </div>

                    <div class="form-group">
                        <label for="user_name"><?php echo _p('ftp_user_name'); ?></label>
                        <input type="text" id="user_name" class="form-control" placeholder="<?php echo _p('ftp_user_name'); ?>"  value="<?php echo $this->_aVars['currentUsername']; ?>" name="val[user_name]"/>
                    </div>

                    <div class="form-group">
                        <label for="ftp_password"><?php echo _p('ftp_password'); ?></label>
                        <input type="password" id="ftp_password" class="form-control" placeholder="<?php echo _p('ftp_password'); ?>" value="<?php echo $this->_aVars['currentPassword']; ?>" name="val[ftp_password]"/>
                    </div>
                </div>
            </div>
        </div>
		<div class="form-group">
            <button type="submit" class="btn btn-primary" name="_submit"><?php echo _p('Submit'); ?></button>
		</div>
	
</form>

    </div>
</div>
<?php else: ?>
<?php if (! PHPFOX_IS_AJAX_PAGE): ?>
    <div id="app-custom-holder" style="min-height:400px;"></div>
    <div id="app-content-holder">
<?php endif; ?>
<?php if ($this->_aVars['customContent']): ?>
		<div id="custom-app-content"><i class="fa fa-circle-o-notch fa-spin"></i></div>
		<script>
			var customContent = '<?php echo $this->_aVars['customContent']; ?>', contentIsLoaded = false, extraParams = <?php echo $this->_aVars['extraParams']; ?>, appUrl = '<?php echo $this->_aVars['appUrl']; ?>';
		<?php echo '
			$Ready(function() {
                contentIsLoaded =  _admincp_load_content(customContent, contentIsLoaded, extraParams, appUrl);
			});
		'; ?>

		</script>
<?php endif; ?>
<?php if (! PHPFOX_IS_AJAX_PAGE): ?>
	</div>
    <div id="app-details">
<?php if (( ! $this->_aVars['ActiveApp']['is_core'] )): ?>
            <ul>
<?php if ($this->_aVars['ActiveApp']['allow_disable']): ?>
                    <li><a <?php if ($this->_aVars['App']['is_module']): ?>class="sJsConfirm" data-message="<?php echo _p('are_you_sure', array('phpfox_squote' => true)); ?>"<?php endif; ?> href="<?php echo $this->_aVars['uninstallUrl']; ?>"><?php echo _p('uninstall'); ?></a></li>
<?php endif; ?>
<?php if ($this->_aVars['export_path'] && defined ( 'PHPFOX_IS_TECHIE' ) && PHPFOX_IS_TECHIE): ?>
                    <li><a href="<?php echo $this->_aVars['export_path']; ?>"><?php echo _p("Export"); ?></a></li>
<?php endif; ?>
            </ul>
<?php endif; ?>
        <div class="app-copyright">
<?php if ($this->_aVars['ActiveApp']['vendor']): ?>
                ©<?php echo $this->_aVars['ActiveApp']['vendor']; ?>
<?php endif; ?>
<?php if ($this->_aVars['ActiveApp']['credits']): ?>
                <div class="app-credits">
                    <div><?php echo _p("Credits"); ?></div>
<?php if (count((array)$this->_aVars['ActiveApp']['credits'])):  foreach ((array) $this->_aVars['ActiveApp']['credits'] as $this->_aVars['name'] => $this->_aVars['url']): ?>
                    <ul>
                        <li><a href="<?php echo $this->_aVars['url']; ?>"><?php echo Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['name']); ?></a></li>
                    </ul>
<?php endforeach; endif; ?>
                </div>
<?php endif; ?>
        </div>
    </div>
<?php endif;  endif; ?>
