<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $bCanSendEmails}
<script type="text/javascript">
{literal}
    function sendEmails(oObj)
    {
        $('#js_send_email_error_message').hide();
        
        if (empty($('#js_share_email').val()))
        {
            $('#js_send_email_error_message').show();
            
            return false;
        }
        
        $(oObj).ajaxCall('document.sendEmails');
        
        return false;
    }
{/literal}
</script>
<div class="label_flow p_4">    
    <form method="post" action="#" onsubmit="return sendEmails(this);">
        <div class="p_4">
            <div id="js_send_email_error_message" class="error_message" style="display:none;">{phrase var='provide_an_e_mail_address'}</div>
            <div class="table form-group">
                <div class="table_left">
                    {phrase var='email_s'}:
                </div>
                <div class="table_right">
                    <input type="text" class="form-control" name="val[to]" size="30" id="js_share_email" value="" />
                    <div class="extra_info">
                        {phrase var='separate_multiple_emails_with_a_comma'}
                    </div>
                </div>
            </div>            
            <div class="table form-group">
                <div class="table_left">
                    {phrase var='subject'}:
                </div>
                <div class="table_right">
                    <input type="text" class="form-control" name="val[subject]" size="30" value="{phrase var='check_out'} {$sTitle|clean}" />
                </div>
            </div>    
            <div class="table form-group">
                <div class="table_left">
                    {phrase var='message'}:
                </div>
                <div class="table_right">
                    <textarea cols="30" rows="10" name="val[message]" style="width:95%;">{$sMessage}</textarea>
                </div>
            </div>
            <div class="table form-group">
                <div class="table_left">
                    {phrase var='attachment'}:
                </div>
                <div class="table_right">
                    <a href="javascript:void(0);" {if $allow_download}onclick="window.location.href='{$download_link}';return false;"{/if} >{$sFileName} </a>
                </div>
                <input type="hidden" name="val[id]" value="{$iId}" />
            </div>
            <div class="table_clear">
                <input type="submit" value="{phrase var='share.send'}" class="button btn btn-success btn-sm" />
            </div>
        </div>        
    </form>
</div>
{else}
<div class="label_flow p_4">    
    <div class="extra_info">
        {phrase var='invalid_document'}
    </div>
</div>
{/if}