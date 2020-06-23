<div class="yncaffiliate_links">
	<div>{_p var='start_to_promote_us_to_your_contacts'}</div>
	<h4 class="capitalize">{_p var='create_custom_link'}:</h4>
	<div class="capitalize fw-bold url">{_p var='destination_url'}:</div>
	<div class="table">
		<div class="table_left fw-400">{_p var='please_enter_link_within_this_domain'}:</div>
		<div class="table_right form-inline">
			<div class="form-group">
				<input type="text" id="ynaff_dynamic_link">
			</div>
		</div>
	</div>
	<div class="table">
		<div class="table_left fw-400">{_p var='affiliate_link'}:</div>
		<div class="table_right">
			<div class="form-inline">
				<div class="form-group">
					<input type="text" id="ynaff_dynamic_link_des" onclick="ynafSelectAll($(this));">
					<button class="btn btn-primary js_ynaf_copy" data-clipboard-target="#ynaff_dynamic_link_des">{_p var='copy_to_clipboard'}</button>
				</div>
			</div>
		</div>
	</div>
	<div>{_p var='you_can_copy_the_referred_url_and_share_with_your_friends'}</div>
	<h4 class="capitalize">{_p var='suggested_links'}:</h4>
    {foreach from=$aSuggestLinks item=aLink key=key}
        <div class="form-inline yncaffiliate_suggeted_link">
            <div class="form-group">
                <div class="table_left fw-400 capitalize">{_p var=$aLink.suggest_title}</div>
                <div class="table_right">
                    <input type="text" value="{$aLink.aff_link}" id="ynaf_suggest_link_{$aLink.suggest_id}" onclick="ynafSelectAll($(this));">
                    <button class="btn btn-default js_ynaf_copy" data-clipboard-target="#ynaf_suggest_link_{$aLink.suggest_id}">{_p('copy_to_clipboard')}</button>
                </div>
            </div>
        </div>
    {/foreach}
</div>
{literal}
<script type="text/javascript">
    $Behavior.ynaffLoadClipboardJs = function(){
        var eles =  $('.js_ynaf_copy');
        if(eles.length){
            $Core.loadStaticFile('{/literal}{$corePath}{literal}/assets/jscript/clipboard.min.js');
            window.setTimeout(function(){
                new Clipboard('.js_ynaf_copy');
            },1500);
        }
        $('#ynaff_dynamic_link').on('keyup',function() {
            var link = $(this).val();
            if (link != "") {
                $Core.ajax('yncaffiliate.getAffiliateLink',
                    {
                        type: 'POST',
                        params: {
                            link: link
                        },
                        success: function (sHref) {
                            $('#ynaff_dynamic_link_des').val(sHref);
                        }
                    }
                );
            }
        });
    };
    ynafSelectAll = function(ele){
        ele.focus();
        ele.select();
    }
</script>
{/literal}