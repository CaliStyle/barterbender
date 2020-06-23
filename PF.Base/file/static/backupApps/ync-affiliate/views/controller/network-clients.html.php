<div class="yncaffiliate_network_clients clearfix">
	<div class="yncaffiliate_download_search_block clearfix">
		<a class="btn btn-primary no_ajax" href="{param var='core.path_actual'}/PF.Site/Apps/ync-affiliate/downloadCSV.php?id={$aUser.user_id}">{_p var='download_csv'}</a>
		<div>
			<input type="text" id="ynaf_search_client" placeholder="{_p('Search client name')}">
			<i class="fa fa-search" aria-hidden="true"></i>
            <ul id="ui-id-1" tabindex="0" class="ui-menu ui-widget ui-widget-content ui-autocomplete ui-front" style="display: none;"></ul>
		</div>
	</div>
	<div class="yncaffiliate_owner_clients">
		<div class="yncaffiliate_avatar">
            {img user=$aUser suffix='_100_square' max_width=100 max_height=100}
		</div>
		<div class="yncaffiliate_owner_clients_info">
			<span>{_p var='client_network_of'}</span>
			<span class="yncaffiliate_name"><a href="{url link=$aUser.user_name}" title="{$aUser.full_name|clean}">{$aUser.full_name|clean}</a></span>
			<span class="yncaffiliate_total_count">
				{_p var='total'}:
				<b>{$iTotalClients}</b>
			</span>
		</div>
	</div>
    {if count($aClients)}
        <div class="yncaffiliate_level_clients yncaffiliate_client">
            {if $iMaxLevel == 1}
                <ul class="yncaffiliate_level_items" id="ynaf_client_container">
            {else}
                <ul class="yncaffiliate_level_items yncaffiliate_last_level" id="ynaf_client_container">
            {/if}
                    {$sHtmlTree}
                </ul>
        </div>
    {else}
    <div class="extra_info">
        {_p var='no_clients_found'}
    </div>
    {/if}
</div>
{literal}
<script type="text/javascript">
	$Ready(function(){
        initAffiliateExplain();

		if($('#ynaf_search_client').prop('built') || $('#ynaf_search_client').length == 0) return;
        $('#ynaf_search_client').prop('built',true);
        $('#ynaf_search_client').addClass('dont-unbind-children');
		$('#ynaf_search_client').autocomplete({
            source: function( request, response ) {
                $Core.ajax('yncaffiliate.searchClient',
                {
                    params:{
                        text: request.term
                    },
                    type: 'post',
                    dataType: 'jsonp',
                    success: function( data ) {
                        response( $.parseJSON(data) );
                    }
                });
            },
            position: {
                my: "right top",
                at: "right bottom",
            },
            minLength: 0,
            select: function( event, ui ) {
                user_id = ui.item.id;
                $.ajaxCall('yncaffiliate.searchClientTree',$.param({iUserId: user_id}));
            },
        });
		$('.ui-autocomplete').addClass('dont-unbind-children');
        $('#ynaf_search_client').data( "ui-autocomplete" )._renderItem = function( ul, item ) {

            var $li = $('<li>'),
                $img = $('<img>');

            $li.attr('data-value', item.label);
            $li.append(item.icon).append(item.label);

            return $li.appendTo(ul);
        };
		$('#ynaf_search_client').on('keyup',function(){
            if($(this).val() == "")
            {
                $.ajaxCall('yncaffiliate.loadMoreClient',$.param({iUserId: {/literal}{$aUser.user_id}{literal},iLevel: 1,iLastAssocId: 0,iLoadedClient: 0,iMaxLevel: {/literal}{$iMaxLevel}{literal},iTotalDirect: {/literal}{$iTotalDirect}{literal},iSearchUserId: 0,iLastUserId: 0,iOverWriteLayout: 1}));
            }
        });
	});
    function initAffiliateExplain() {
        $('.yncaffiliate_btn_action_items_more').each(function(){
            $(this).unbind('click');
            $(this).bind('click',function () {
                $(this).siblings('.yncaffiliate_level_items_more').toggle();
                $(this).parent('.yncaffiliate_level_item').toggleClass('yncaffiliate_item_more_explain');
            });
        })
        $('.yncaffiliate_btn_action_explain').each(function(){
            $(this).unbind('click');
            $(this).bind('click',function () {
                $('body').find('.yncaffiliate_explain_info').removeClass('yncaffiliate_explain_info');
                $(this).parents('.yncaffiliate_level_clients').find('.yncaffiliate_client_item_info').hide();
                $(this).siblings('.yncaffiliate_client_item_info').toggle();
                $(this).parent('.yncaffiliate_level_item').toggleClass('yncaffiliate_explain_info');
            });
        });

        $('.yncaffiliate_btn_action_close').each(function() {
            $(this).unbind('click');
            $(this).bind('click',function () {
                $(this).parent('.yncaffiliate_client_item_info').hide();
                $('body').find('.yncaffiliate_explain_info').removeClass('yncaffiliate_explain_info');
            });
        });
    }
	function ynaLoadMoreClient(iUserId,iLevel,iLastAssocId,iLoadedClient,iMaxLevel,iTotalDirect,iSearchUserId,iLastUserId){
        $.ajaxCall('yncaffiliate.loadMoreClient',$.param({iUserId: iUserId,iLevel: iLevel,iLastAssocId: iLastAssocId,iLoadedClient: iLoadedClient,iMaxLevel: iMaxLevel,iTotalDirect: iTotalDirect,iSearchUserId: iSearchUserId,iLastUserId: iLastUserId,iOverWriteLayout: 0}));
        return false;
    }
</script>
{/literal}