<?php
/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');


$oDonation = Phpfox::getService('donation');
$sDonation = _p('donation.donation');
$iPageId = $this->request()->getInt('id'); 
$iCurrentUserId = Phpfox::getUserId();
$iUserId = $oDonation->getUserIdOfPage($iPageId);
$sWidget = $this->request()->get('req3','0');
$this->template()->setEditor();
?>

<script type="text/javascript" language="javascript">
	$Behavior.onLoadPlugin_donation = function() {
		<?php
		if ($iPageId > 0 && $iUserId == $iCurrentUserId && Phpfox::isModule('donation')) {
	    if ($oDonation->checkPermissions('can_add_donation_on_own_page', array('iPageId' => $iPageId)))
	    {
		?>	
			var bIsFirstRun = false;
			 $(".page_section_menu").find("li").find("a").click(function(evt) {
	        	$('#js_pages_add_holder').find(".donation").hide();
	        });
			if ( ($("#content").find("ul").first().find("#donation_tab").length == 0))
	        {
	            <?php if ($iPageId) { ?>
	                $('#js_pages_add_holder').find("form").append($("<div class=\"donation\" style=\"display: none;\">"));
	                $("#page_pages_add").find(" ._block_error .page_section_menu ul").first().append("<li><a ref=\"donation_tab\" id=\"donation_tab\" href=\"#\" onclick=\"showDonation(this); return false;\"><?php echo $sDonation;?></a></li>");
	                <?php if ($this->request()->get('tab') == 'donation') { ?>	 
	                      $("#donation_tab").click(); bIsFirstRun = true; 
	                <?php } ?>
	            <?php } ?>  
	        }
	     <?php } 
		}?>   
	};
    function showDonation(obj){
        $this = $(obj); 
    	$this.unbind();
        $('#js_pages_add_holder').find(".donation").load($.ajaxBox('donation.config','iPageId=<?php echo $iPageId; ?>'));
        $('#js_pages_add_holder').find(".donation").show();
        $('#js_pages_add_holder').find(".js_pages_block").hide();

        $this.parents("ul").find(".active").removeClass("active");
        $this.parent().addClass("active");
    }
</script>
<?php ?>