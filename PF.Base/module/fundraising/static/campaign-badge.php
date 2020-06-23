<?php
	include 'cli.php';
	$iCampaignId = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
	$iStatus = isset($_REQUEST['status']) ? $_REQUEST['status'] : 0;
	$oAjax = Phpfox::getLib('ajax');
	Phpfox::getBlock('fundraising.highlight-campaign', array('iCampaignId' => $iCampaignId, 
															 'bIsBadge' => true, 
														 	 'iStatus' => $iStatus
														));

	$sContent = $oAjax->getContent();
	$sContent =  stripslashes($sContent);
	$sCorePath = Phpfox::getParam('core.path');

?>

<style>
    #js_block_border_fundraising_highlight-campaign .ynfr-highligh-detail .ynfr-donor a span.hidden{
        display: none !important;
    }
	a{
		text-decoration: none;
		color: #298ada;
	}
    .meter-wrap{
        background: #989898;
    }
    .meter-value{
        text-align: center;
        background: #298ADA;
        color: #FFF;
        text-indent: 10px;
    }
	.ynfr-title a{
        font-size: 15px;
        font-weight: bold;
        line-height: 1.3em;
        max-height: 37px;
        overflow: hidden;
        margin-top: 7px;
        margin-bottom: 3px;
        text-transform: capitalize;
        text-decoration: none;
        color: #298ada;
    }

    p{
    	margin: 5px 0px;
    }
    .ynfr-donor a{
    	margin-right: 5px;
    	width: 32px;
    	display: inline-block;
    	vertical-align: middle;
    	height: 32px;
    }
    .ynfr-donor a.no_image_user, .ynfr-donor a.no_image_user:hover
    {
        display: inline-block;
    }
    .ynfr-donor a img{
		width: 32px;
		height: 32px;
    }
    .ynfr-donate div a{
		background: #ffa800;
		color: #FFF;
		padding: 15px 45px;
		text-transform: uppercase;
		font-weight: bold;
		font-size: 16px;
		border-radius: 3px;
		display: block;
		text-align: center;
    }
    .ynfr-short-des{
    	margin: 10px 0;
    }

    ._size__32{
		width: 32px;
		height: 32px;
		background: #81CFE0;
		display: inline-block;
		text-align: center;
		text-transform: uppercase;
		color: #FFF;
		padding-top: 7px;
		box-sizing: border-box;
    }

    span.no_image_campaign{
    	display: none !important;
    }
</style>
<script type="text/javascript" src="<?php echo( $sCorePath);?>/static/jscript/jquery/jquery.js" /></script>
<body style="margin: 0; padding: 0;">
<div style="font-family: 'Open Sans',tahoma,verdana,arial,sans-serif; font-size: 13px;">
	<?php echo $sContent; ?>
</div>
<script type="text/javascript">
	$('.ynfr-donor img').each(function(index, el){$(el).attr('src', $(el).attr('data-src'))});
</script>
</body>
<?php
	ob_flush();
?>