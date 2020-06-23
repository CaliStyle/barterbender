<div class="modal fade ynmember_mutual_modal ynmember_modal" id="ynmember_mutual_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>Mutual friend with Iasabella Spring</div>
                <i class="fa fa-times yn-close" aria-hidden="true" data-dismiss="modal" aria-label="Close"></i>
            </div>
            <div class="modal-body">
                <ul class="clearfix">
                    <li>
                        <div class="ynmember_avatar">
                            {if $aUser.user_image}
                            <a href="{url link=$aUser.user_name}" title="{$aUser.full_name}" style="background-image: url('{img user=$aUser suffix='_200_square' return_url=true}');"></a>
                            {else}
                            {img user=$aUser suffix='_200_square' return_url=true}
                            {/if}
                        </div>
                        {$aUser|user}
                    </li>
                    <li>
                        <div class="ynmember_avatar">
                            {if $aUser.user_image}
                            <a href="{url link=$aUser.user_name}" title="{$aUser.full_name}" style="background-image: url('{img user=$aUser suffix='_200_square' return_url=true}');"></a>
                            {else}
                            {img user=$aUser suffix='_200_square' return_url=true}
                            {/if}
                        </div>
                        {$aUser|user}
                    </li>
                </ul>
                <a href="javascript:void(0)" class="ynmember_viewmore uppercase active">
                    load more
                    <i class="fa fa-angle-down" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </div>
</div>