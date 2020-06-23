<div class="yncaffiliate_codes">
    {if $iPage <= 1}
	<p>{_p var='copy_the_appropriate_code_and_paste_it_and_make_it_available_to_pontential'}</p>
    {/if}
	<div class="yncaffiliate_codes_inner">
        {if count($aMaterials) > 0}
            {foreach from=$aMaterials item=aItem key=iKey}
                <div class="table yncaffiliate_codes_item">
                    <div class="table_left capitalize fw-400">{$aItem.material_name|clean}</div>
                    <div class="table_right">
                        <textarea type="text" rows="6" class="form-control" id="ynaf_code_item_{$aItem.material_id}" onclick="$(this).focus().select();" readonly>{$aItem.iframe_code}</textarea>
                        <div style="padding-top: 5px;">
                            <button class="btn btn-primary" data-width="{$aItem.material_width}" data-image="{$aItem.image_full_path}" data-id="{$aItem.material_id}" data-caption="{_p var='preview_l'}" onclick="return ynafReviewCode(this);">{_p var='preview_l'}</button>
                            <button class="btn btn-default js_ynaf_copy_code" data-clipboard-target="#ynaf_code_item_{$aItem.material_id}">{_p var='copy_to_clipboard'}</button>
                        </div>
                    </div>
                </div>
            {/foreach}
            {pager}
        {elseif $iPage <=1}
            <div class="p_4">
                {_p var='no_codes_found'}
            </div>
        {/if}
	</div>

</div>
{literal}
<script type="text/javascript">
    $Behavior.ynaffLoadClipboardCodeJs = function(){
        if($('.js_ynaf_copy_code').length){
            $Core.loadStaticFile('{/literal}{$corePath}{literal}/assets/jscript/clipboard.min.js');
            window.setTimeout(function(){
                new Clipboard('.js_ynaf_copy_code');
            },1500);
        }

    };
    ynafSelectAll = function(ele){
        ele.focus();
        ele.select();
    };
    ynafReviewCode = function(ele){
        var caption = $(ele).data('caption'),
            width = $(ele).data('width'),
            image = $(ele).data('image');
        if(width < 100)
        {
            width = 150;
        }
        tb_show(caption,$.ajaxBox('yncaffiliate.reviewCode',$.param({width:width,image:image})));
    }
</script>
{/literal}