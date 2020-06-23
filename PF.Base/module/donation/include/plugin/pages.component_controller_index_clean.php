<?php
/**
 * [PHPFOX_HEADER]
 */
/*
defined('PHPFOX') or exit('NO DICE!');
$oDonation = Phpfox::getService('donation');
$iPageId = $oDonation->getPageIdFromUrl();
if ($iPageId == 0) 
{
    $iPageId = $this->request()->get('id');
}
$iDonation = (int) $oDonation->isEnableDonation($iPageId);
$iCurrentUserId = Phpfox::getUserId();
$iUserId = $oDonation->getUserIdOfPage($iPageId);
$sPageTitle = $oDonation->getPageDetail($iPageId); 
$sUrl = urlencode(Phpfox::getLib('url')->getFullUrl());
$sImg = $oDonation->getDonationButtonImagePath();

if ($iPageId > 0 && $iDonation>0 && Phpfox::isModule('donation'))
{
    if ($oDonation->checkPermissions('can_donate', array('iPageId' => $iPageId)))
    {
        $sDonation = _p('donation.donation_for_page_page_name', array('page_name'=>$sPageTitle));
		
		$aRow = Phpfox::getService('pages')->getPage($iPageId);
    	if(!$aRow['use_timeline'])//for normal
		{
            if($iUserId == $iCurrentUserId ) { //for owner
		?>
		<script type="text/javascript">
		    $Behavior.DonationShowInPage = function() {
                $().ready(function(){        
                    if ($('#donateBlock').html() == null && !$('#page_button_donate')[0])
                    {
                        $(".sub_section_menu:first").prepend("<div id='page_button_donate'><p id=\"donateBlock\" style=\"text-align:center\"> <a class=\"donate\" href=\"<?php echo Phpfox::getLib('url')->makeUrl('pages',array('add', 'id' =>$iPageId , 'tab'=>'donation'));?>\" ><?php echo _p('donation.donation_setting'); ?></a></p><p style=\"text-align:center\"> <a class=\"donate\"  onclick=\"showDonationIndex(); return false;\" href=\"#\"  ><img src=\"<?php echo $sImg;?>\"/></a></p></div>");
                    }
                });
            }
		    
		    function showDonationIndex(){     
		        tb_show('<?php echo $sDonation; ?>',$.ajaxBox('donation.detail','iPageId=<?php echo (int) $iPageId; ?>&sUrl=<?php echo $sUrl; ?>'));
		    }
		</script>
	   <?php 
            }
            else{ // for other user
        ?>
            <script type="text/javascript">
            $Behavior.DonationShowInPage = function() {
                $().ready(function(){        
                    if ($('#donateBlock').html() == null && !$('#page_button_donate')[0])
                    {
                        $(".sub_section_menu:first").prepend("<div id='page_button_donate'><p style=\"text-align:center\"> <a class=\"donate\"  onclick=\"showDonationIndex(); return false;\" href=\"#\"  ><img src=\"<?php echo $sImg;?>\"/></a></p></div>");
                    }
                });
            }
            
            function showDonationIndex(){     
                tb_show('<?php echo $sDonation; ?>',$.ajaxBox('donation.detail','iPageId=<?php echo (int) $iPageId; ?>&sUrl=<?php echo $sUrl; ?>'));
            }
            </script>
        <?php
            }        

        }
		else{ //for timeline mode

            if($iUserId == $iCurrentUserId ) { //for owner
            ?>
		<script type="text/javascript">
		    $Behavior.DonationShowInPage = function() {
                $().ready(function(){        
                    if($('.profile_header_inner') && !$('#page_button_donate')[0])
                    {
                        $(".profile_header_inner").append("<div id='page_button_donate' style='float:left;'><p style=\"text-align:center\"> <a class=\"donate\"  onclick=\"showDonationIndex(); return false;\" href=\"#\" ><img src=\"<?php echo $sImg;?>\"/></a></p><p id=\"donateBlock\" style=\"text-align:center\"> <a class=\"donate\"  href=\"<?php echo Phpfox::getLib('url')->makeUrl('pages',array('add', 'id' =>$iPageId,'tab'=>'donation'));?>\"><?php echo _p('donation.donation_setting'); ?></a></p></div>");
                    }
                });
            }
		    
		    function showDonationIndex(){     
		        tb_show('<?php echo $sDonation; ?>',$.ajaxBox('donation.detail','iPageId=<?php echo (int) $iPageId; ?>&sUrl=<?php echo $sUrl; ?>'));
		    }
		</script>
		<?php
            }
           else{ //for other user
            ?>
        <script type="text/javascript">
            $Behavior.DonationShowInPage = function() {
                $().ready(function(){        
                    if($('.profile_header_inner') && !$('#page_button_donate')[0])
                    {
                        $(".profile_header_inner").append("<div id='page_button_donate' style='float:left;'><p style=\"text-align:center\"> <a class=\"donate\"  onclick=\"showDonationIndex(); return false;\" href=\"#\" ><img src=\"<?php echo $sImg;?>\"/></a></p></div>");
                    }
                });
            }
            
            function showDonationIndex(){     
                tb_show('<?php echo $sDonation; ?>',$.ajaxBox('donation.detail','iPageId=<?php echo (int) $iPageId; ?>&sUrl=<?php echo $sUrl; ?>'));
            }
        </script>
            <?php
               }
		   }
	} 
	
} */
?>