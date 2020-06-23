<div class="table form-group-follow">
    <label for="">
      {phrase var='videochannel.channel_url'}
    </label>
    <div class="table_right">
        <input type="text" onfocus="$('#channel_url_error').hide()" name="val[url]" id="channel_url" class="form-control" size="40">
         <div id="channel_url_error" class="error_message" style="display: none">{phrase var='videochannel.enter_url_to_add_channel'}</div>
         <div class="table_clear">
          <input type="button" class="btn btn-sm btn-primary" value="{phrase var='videochannel.add'}" onclick="return addChannelUrl('{$sModule}', {$iItem});" >
         </div>
    </div>
</div>