<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FeedBack
 * @version          2.01
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>
{literal}
<style type="text/css">
 #feedback-bt .feedback-button-left {
    border: 1px solid #EEE;
    cursor: pointer;
    display: block;
    right: 0;
    padding: 0;
    position: fixed;
    top: 45%;
    z-index: 10001;
    background-color: #0267CC;
    color: #FFF;
    padding: 5px 15px;
     -ms-transform: rotate(-90deg); /* IE 9 */
    -webkit-transform: rotate(-90deg); /* Chrome, Safari, Opera */
    transform: rotate(-90deg);
    margin-right: -30px;
    -webkit-transition: all 300ms;
    -moz-transition: all 300ms;
    -ms-transition: all 300ms;
    -webkit-transition: all 300ms;
    /*box-shadow: -1px -2px 6px -2px #333;*/
}

#feedback-bt a{

}

#feedback-bt a:hover,
#feedback-bt a:focus{
    text-decoration: none;
    background-color: #E40000;
}

 .TB_overlayBG {
    background-color:#000000;
    opacity:0.75;
}
#TB_ajaxContent {
 height: auto !important;
}

.colmask.leftmenu .colleft #feedback-bt
{
    display: none;
}
#main_content_padding
{
    overflow: visible;
}

@media screen and (max-width: 1024px){
    #feedback-bt{display: none}
}

</style>
{/literal}
{if !Phpfox::getUserId()}
<div id="feedback-bt">
    <a href="#?call=feedback.addFeedBack&amp;height=400&amp;width=850" class="inlinePopup feedback-button-left">{_p var='feedback'}</a>
</div>
{else}
<div id="feedback-bt">
    <a href="#?call=feedback.addFeedBack&amp;height=400&amp;width=850" class="inlinePopup feedback-button-left">{_p var='feedback'}</a>
</div>
{/if}

