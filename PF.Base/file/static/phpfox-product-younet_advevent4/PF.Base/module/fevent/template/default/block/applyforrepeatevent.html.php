{if $isRepeat == 1}
    <div id="fevent_option_repeat_block" class="pt-2 mb-2">
        <label for="">{_p var='fevent.apply_edits_for'}</label>
        <div class="form-inline ml--1 mr--1">
            <div class="form-group pl-1 pr-1">
                <label class="mb-0 fw-normal cursor-point">
                    <input class="hidden" type="radio" name="val[ynfevent_editconfirmboxoption_value]" id="only_in_block" value="only_this_event" checked="checked" /><i class="ico ico-circle-o text-gray-dark mr-1"></i><span>{_p var='fevent.only_this_event'}</span>
                    <div class="ynfeTipOptionRepeat">
                        <a href="#" onclick="return false;"><i class="fa fa-question-circle"></i></a>
                        <div class="FETip" id="tip_only_block">
                            <span class="FETipTitle btn btn-primary">{_p var='fevent.only_this_event'}</span>
                            <span class="FETipBody">{_p var='fevent.only_this_event_tut'}</span>
                        </div>
                    </div>
                </label>
            </div><div class="form-group pl-1 pr-1">
                <label class="mb-0 fw-normal cursor-point">
                    <input class="hidden" type="radio" name="val[ynfevent_editconfirmboxoption_value]" id="following_in_block" value="following_events" /><i class="ico ico-circle-o text-gray-dark mr-1"></i><span>{_p var='fevent.following_events'}</span>
                    <div class="ynfeTipOptionRepeat">
                        <a href="#" onclick="return false;"> <i class="fa fa-question-circle"></i></a>
                        <div class="FETip" id="tip_following_block">
                            <span class="FETipTitle btn btn-primary">{_p var='fevent.following_events'}</span>
                            <span class="FETipBody">{_p var='fevent.following_events_tut'}</span>
                        </div>
                    </div>
                </label>
            </div><div class="form-group pl-1 pr-1">
                <label class="mb-0 fw-normal cursor-point">
                    <input class="hidden" type="radio" name="val[ynfevent_editconfirmboxoption_value]" id="all_in_block" value="all_events_uppercase" /><i class="ico ico-circle-o text-gray-dark mr-1"></i><span>{_p var='fevent.all_events_uppercase'}</span>
                    <div class="ynfeTipOptionRepeat">
                        <a href="#" onclick="return false;"><i class="fa fa-question-circle"></i></a>
                        <div class="FETip" id="tip_all_block">
                            <span class="FETipTitle btn btn-primary">{_p var='fevent.all_events_uppercase'}</span>
                            <span class="FETipBody">{_p var='fevent.all_events_tut'}</span>
                        </div>
                    </div>
                </label>
            </div>
        </div>
    </div>
{/if}

{literal}
<script type="text/javascript">
    $Behavior.FEEditOptionRepeat = function(){
        $('#fevent_option_repeat_block').change(function () {
            if($("#only_in_block").is(':checked')){
                $(".yn_edit_event_apply #only_in_form").prop('checked',true)
            }
            if($("#following_in_block").is(':checked')){
                $(".yn_edit_event_apply #following_in_form").prop('checked',true)
            }
            if($("#all_in_block").is(':checked')){
                $(".yn_edit_event_apply #all_in_form").prop('checked',true)
            }
        });
    }
</script>
{/literal}