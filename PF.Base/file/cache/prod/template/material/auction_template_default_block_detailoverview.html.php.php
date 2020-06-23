<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: June 1, 2020, 8:35 pm */ ?>
<div class="ynauction-detail-overview">
<?php if (count ( $this->_aVars['aCustomFields'] ) || count ( $this->_aVars['aAuction']['additioninfo'] )): ?>

		<div class="ynauction_trix_header">
			<span class="section_title"> <i class="fa fa-bookmark"></i> <?php echo _p('auction_specifications'); ?></span>
	        <span class="section_toggle">
	            <i class="fa fa-chevron-down"></i>
	        </span>
		</div>
	    <div class="content">
	        <?php
						Phpfox::getLib('template')->getBuiltFile('auction.block.custom.view');
						?>
<?php if (count ( $this->_aVars['aAuction']['additioninfo'] )): ?>
		    <div class="ynauction-detail-overview-additional">
<?php if (count ( $this->_aVars['aCustomFields'] )): ?>
			        <div class="subsection_header">
<?php echo _p('additional_information'); ?>
			        </div>
<?php endif; ?>
<?php if (count((array)$this->_aVars['aAuction']['additioninfo'])):  foreach ((array) $this->_aVars['aAuction']['additioninfo'] as $this->_aVars['iAdditionalInfo'] => $this->_aVars['aAdditionalInfo']): ?>
		        <div class="ynauction-detail-overview-additional-item">
		            <div class="item_label">
		                <i class="fa fa-stop"></i>
		                <span><?php echo $this->_aVars['aAdditionalInfo']['usercustomfield_title']; ?></span>
		            </div>
		            <div class="item_value">
<?php echo $this->_aVars['aAdditionalInfo']['usercustomfield_content']; ?>
		            </div>
		        </div>
<?php endforeach; endif; ?>
		    </div>
<?php endif; ?>
	    </div>     
<?php endif; ?>
	
<?php if ($this->_aVars['aAuction']['description'] != ''): ?>
	<div class="ynauction_trix_header">
		<span class="section_title"> <i class="fa fa-list"></i> <?php echo _p('auction_description'); ?></span>
        <span class="section_toggle">
            <i class="fa fa-chevron-down"></i>
        </span>
	</div>
    <div class="content">
        <div class="ynauction-detail-overview-item">
    		<div class="ynauction-description item_view_content">
<?php echo Phpfox::getLib('phpfox.parse.output')->parse($this->_aVars['aAuction']['description']); ?>
    		</div>
    	</div>
    </div> 	
<?php endif; ?>
</div>

<?php echo '
<script type="text/javascript">
$Behavior.countryIsoChangeAddNewAddress = function()
{
	$(".section_toggle").click(function(e) {
		    var parent = $(this).parents(\'div.ynauction_trix_header\');
	        var content = parent.next( ); 
			var icon = $(this).children().first();
			 if ( icon.hasClass(\'fa-chevron-down\') ) {
                icon.removeClass(\'fa-chevron-down\');
                icon.addClass(\'fa-chevron-up\');
            } else {
                icon.removeClass(\'fa-chevron-up\');
                icon.addClass(\'fa-chevron-down\');
            }
	        content.slideToggle();
	});
};
</script>
'; ?>

