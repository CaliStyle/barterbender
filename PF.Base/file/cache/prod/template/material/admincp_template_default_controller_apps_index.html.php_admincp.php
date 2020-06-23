<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 20, 2020, 7:50 pm */ ?>
<?php

 if (isset ( $this->_aVars['vendorCreated'] )): ?>
	<i class="fa fa-spin fa-circle-o-notch"></i>
	<?php echo '
		<script>
			$Ready(function() {
				$Behavior.addDraggableToBoxes();
				$(\'.admin_action_menu .popup\').trigger(\'click\');
			});
		</script>
	'; ?>

<?php else: ?>
	<div class="admincp_apps_holder">
<?php if (isset ( $this->_aVars['warning'] ) && $this->_aVars['warning']): ?>
        <section class="apps">
            <div class="text-danger text-center"><?php echo $this->_aVars['warning']; ?></div>
        </section>
<?php endif; ?>
        <section class="apps">
			<div class="table-responsive admincp_apps_installed">
                <input type="text" onkeyup="$Core.searchTable(this, 'list_apps', 'app_column_index');" placeholder="<?php echo _p('search_for_app_names_dot'); ?>" class="form-control">
                <div class="ajax" data-url="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('admincp.apps.ajax'); ?>"></div>
			</div>
		</section>
		<section class="preview">
			<h1><?php echo _p('featured_apps'); ?></h1>
			<div class="phpfox_store_featured" data-type="apps" data-parent="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('admincp.store', array('load' => 'apps')); ?>">
			</div>
		</section>
	</div>
<?php endif; ?>
