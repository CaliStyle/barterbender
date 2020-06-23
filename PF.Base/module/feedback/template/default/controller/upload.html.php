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

<script type="text/javascript">
var max_file_size_upload_mb = 2;
var current_file = "";
var numfile = 5;
{literal}
$(function(){
   $('#swfupload-control').swfupload({    {/literal}
        upload_url: "{url link='current'}",
        file_post_name: 'uploadfile',
        file_size_limit : "2048",	
        file_types : "*.jpg;*.gif;*.png;*.jpeg",
        file_types_description : "Feedback Pictures Files",
        file_upload_limit : "5",
        file_queue_limit : "5",
        flash_url : "{$core_path}module/feedback/static/swf/swfupload.swf",
        button_image_url : '{$core_path}module/feedback/static/image/wdp_buttons_upload_114x29.png',
        button_width : 114,
        button_height : 29,
        button_placeholder : $('#button')[0],
        debug: false {literal}

    })

        .bind('fileQueued', function(event, file){
            var listitem='<li id="'+file.id+'" >'+
                'File: <em>'+file.name+'</em> ('+Math.round(file.size/1024)+' KB) <span class="progressvalue" ></span>'+
                '<div class="progressbar" ><div class="progress" ></div></div>'+
                '<p class="status" >Pending</p>'+
                '<span class="cancel" >&nbsp;</span>'+
                '</li>';
            $('#log').append(listitem);
            $('li#'+file.id+' .cancel').bind('click', function(){
                var swfu = $.swfupload.getInstance('#swfupload-control');
                swfu.cancelUpload(file.id);
                $('li#'+file.id).slideUp('fast');
            });

            // start the upload since it's queued
            $(this).swfupload('startUpload');

        })

      
         .bind('fileQueueError', function(even,file, errorCode, message){
               var listitem='';
               switch(errorCode)
               {
                   case -130:
                       listitem='<li id="'+file.id+'" class="fileQueueError">'+
                '<p>'+message+'</p>' +
                '</li>';
                        break;
                   case -100:
                        break;
                   default:
                        listitem='<li id="'+file.id+'" class="fileQueueError">'+
                '<p> Size of the file ' + file.name + ' is greater than limit '+ max_file_size_upload_mb + ' MB </p>' +
                '</li>';
                    break;
               }
                 

            $('#queuestatus_status').append(listitem);

        })

        .bind('fileDialogComplete', function(event, numFilesSelected, numFilesQueued){
            $('#queuestatus').text('Files Selected: '+numFilesSelected+' / Queued Files: '+numFilesQueued);
            if(numFilesSelected==0)
            {
                $('#queuestatus_status').text('');
            }
        })

        .bind('uploadStart', function(event, file){
            $('#log li#'+file.id).find('p.status').text('Uploading...');
            $('#log li#'+file.id).find('span.progressvalue').text('0%');
            $('#log li#'+file.id).find('span.cancel').hide();
        })
        .bind('uploadProgress', function(event, file, bytesLoaded){
            //Show Progress
            var percentage=Math.round((bytesLoaded/file.size)*100);
            $('#log li#'+file.id).find('div.progress').css('width', percentage+'%');
            $('#log li#'+file.id).find('span.progressvalue').text(percentage+'%');
        })
        .bind('uploadSuccess', function(event, file, serverData){
            var item=$('#log li#'+file.id);
            item.find('div.progress').css('width', '100%');
            item.find('span.progressvalue').text('100%');
            item.addClass('success').find('p.status').html('Complete!!! ');
        })
        .bind('uploadComplete', function(event, file){
            // upload has completed, try the next one in the queue
            $(this).swfupload('startUpload');

        })
        .bind('fileDialogStart', function(){
             $('#queuestatus_status').text('');
             $('#log').text('');
        })



});

</script>
{/literal}
<h1>{_p var='upload_feedback_pictures'}</h1>
<h2>{$feedback_title}</h2>
            {if $feedback_id  <= 0 }
                <div style="color:red">
                    {_p var='please_select_a_feedback_before_uploading_the_picture'}
                </div>
            {else}
                <div id="swfupload-control" style="padding-left: 10px;">
                <div style="font-size: 11pt;">{_p var='browse_the_pictures_on_your_computer_and_upload_them_to_your_feedback'}</div>  <br/>
                <div>{_p var='you_can_upload_the_files_with_max_size_up_to'} <font color="red" style="font-weight: bold;">2 MB </font> .</div>
                <div>{_p var='you_can_only_upload_the_files_with_types'} <font color="red" style="font-weight: bold;">(jpg, png, gif, jpeg)</font> .</div>
                <div>{_p var='you_can_upload'} <span style="font-weight: bold;color:red" id="number_upload_files">{$rest_picture}</span> {_p var='files_for_this_feedback'} .</div>
                    <button type="button" class="btn btn-success" id="button">upload photo</button>
                    <p id="queuestatus" style="color: red;" ></p>
                    <ol id="queuestatus_status" style="color: red;" ></ol>
                    <ol id="log"></ol>
                </div>
                <table cellpadding='0' cellspacing='3' width='150'>
                      <tr>
                          <td class='button' nowrap='nowrap'><img src='{$core_path}module/feedback/static/image/back16.gif' border='0' align="absmiddle" ><a href="{url link='feedback.detail' feedback=$feedback_id}"> Back to the Feedback</a></td>
                          <td class='button' nowrap='nowrap'><img src='{$core_path}module/feedback/static/image/back16.gif' border='0' align="absmiddle" ><a href="{url link = 'feedback.manage'}">Back to Your Feedback</a></td>
                      </tr>
                </table>
            {/if}

       

