<div class="ynsaAttractTitle" >
	{$sAttractTitle}
</div>
<div class="ynsaAttractContent" >

	<div class="ynsaAttractText" > 
		{$sAttractText}
	</div>
</div>

<div class="ynsaAttractButton" > 
	<button class="btn btn-primary btn-sm" onclick="window.location='{url link='socialad.ad.add'}';">{phrase var='create_an_ad'}</button>
</div>

<div class="clear"></div>
{literal}
<style >
.ynsaAttractTitle {
	float: left;
	width: 100%;
	color: #3b5998;
	font-size: 10pt;
	line-height: 13px;
	padding: 0px 0px 10px 3px;
	font-weight: bold;
}

.ynsaAttractImage {
	float: left
}

.ynsaAttractText { 
	float: left;
	width: 130px;
	padding-left: 10px;
}

.ynsaAttractButton {
	float: left;
	width: 100%;
	padding-left: 34px;
	padding-top: 10px;
}

/*Thaotlh: Want more Attention*/
#js_block_border_socialad_attract.block{background:#fff;}
#js_block_border_socialad_attract.block .content,
#js_block_border_socialad_attract.block .title{padding:10px;}
#js_block_border_socialad_attract .ynsaAttractContent{display:table;}
#js_block_border_socialad_attract .ynsaAttractImage,
#js_block_border_socialad_attract .ynsaAttractText {
	float: none;
	display: table-cell;
	padding-left:0;
	width:auto;
	vertical-align:top;
}
#js_block_border_socialad_attract .ynsaAttractImage img{margin-right:8px;}
#js_block_border_socialad_attract .no_image_user > span,
#js_block_border_socialad_attract .no_image_user:hover > span{position:static;}
#js_block_border_socialad_attract .ynsaAttractButton{padding-left:0;float:none;}
#js_block_border_socialad_attract .ynsaAttractButton input.button{
	width:100%;
	box-sizing:border-box;
	-webkit-box-sizing:border-box;
	-moz-box-sizing:border-box;
}
#js_block_border_socialad_attract .no_image_user,
#js_block_border_socialad_attract .no_image_user:hover{
	padding:10px;
	margin-top:3px;
	margin-right:5px;
}

</style>
{/literal}
