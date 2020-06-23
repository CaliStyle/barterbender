{if $page == 1}
<link href="{$amCorePath}/assets/css/owl.theme.default.min.css" rel='stylesheet' type='text/css'>
<link href="{$amCorePath}/assets/css/owl.carousel.css" rel='stylesheet' type='text/css'>
{/if}

{if $iCntToday && $page == 1}
<div class="ynmember_birthday_block{if $iCntToday>1} ynmember_multi{else} ynmember_single{/if}">
    <div class="ynmember_birthday_block_bg" style="background-image: url('{param var='core.path_actual'}PF.Site/Apps/ync-member/assets/image/birthday_bg_page.png')"></div>
    <div class="ynmember_birthday_block_multi">
        <div class="ynmember_title uppercase fw-bold">{_p var='today_string' string=$sToday}</div>
        {if $iCntToday == 1}
            <div class="ynmember_sub_title fw-300 uppercase">{_p var='is_birthday_of_1_member'}</div>
        {/if}
        {if $iCntToday > 1}
            <div class="ynmember_sub_title fw-300 uppercase">{_p var='is_birthday_of_number_member' number=$iCntToday}</div>
        {/if}
        <ul class="owl-carousel owl-theme dont-unbind-children" id="ynmember_birthday_slider">
            {foreach from=$aTodayBirthdays name=users item=aUser}
            <li class="item">
                {template file='ynmember.block.birthday_page_multi'}
            </li>
            {/foreach}
        </ul>
        {if Phpfox::isUser()}
        <div class="ynmember_wishes_parent">
            <a href="{url link='ynmember.birthdaywish'}" class="btn btn-primary ynmember_wishes popup"><i class="fa fa-birthday-cake" aria-hidden="true"></i>{_p var='Send birthday wishes'}</a>
        </div>
        {/if}
    </div>

    <div class="ynmember_birthday_block_single">
        <div class="ynmember_avatar">
            {if $aUser.user_image}
                <a href="{url link=$aUser.user_name}" title="{$aUser.full_name}" style="background-image: url('{img user=$aUser suffix='_200_square' return_url=true}');"></a>
            {else}
                {img user=$aUser suffix='_200_square' return_url=true}
            {/if}
        </div>
        <div class="ynmember_info{if $aUser.is_online} active{/if}">
            <div class="fw-bold uppercase text-center">{_p var='Happy birthday to'}</div>
            {$aUser|user}
            {if Phpfox::isUser() && Phpfox::getUserId() != $aUser.user_id}
            <form method="post" action="{url link='ynmember.birthday'}" class="ynmember_text_wishes" id="ynmember_birthday_wish_form_{$aUser.user_id}">
                <textarea class="js_ynmember_birthday_wish" name="val[message]" placeholder="{_p var='send_your_best_wishes_to_subject' subject=$aUser|ynmember_gender}" {if $aUser.is_sent_birthday_wish} disabled{/if}>{if $aUser.is_sent_birthday_wish}{$aUser.birthday_message}{/if}</textarea>
                <input type="hidden" name="val[user_id]" value="{$aUser.user_id}" />
                {if !$aUser.is_sent_birthday_wish}
                <button id="d="ynmember_send_bw_btn_{$aUser.user_id}" onclick="ynmember.sendBirthdayWish('{$aUser.user_id}');return false;" class="fa fa-paper-plane" aria-hidden="true"></button>
                {/if}
            </form>
            {/if}
        </div>
    </div>
</div>
{/if}

{if $page == 1}
<div class="ynmember_birthday_member_block block">
    <div class="title ynmember_header clearfix breadcrumbs-top">
        <div class="ynmember_title pull-left fw-bold capitalize">{_p var='upcoming birthdays'}</div>
        </div>
    <form id="ynmember_birthday_range">
        <div class="ynmember_birthday_range form-group">
            <div class="input-group ynmember_datepicker_group">
                <input id="from_date" name="from_date" placeholder="{_p var='from'}" class="ynmember_datepicker dont-unbind form-control">
                <div class="input-group-addon ynmember_calendar_icon"><i class="fa fa-calendar" aria-hidden="true"></i></div>
            </div>
            <div class="input-group ynmember_datepicker_group">
                <input id="to_date" name="to_date" placeholder="{_p var='to'}" class="ynmember_datepicker dont-unbind form-control">
                <div class="input-group-addon ynmember_calendar_icon"><i class="fa fa-calendar" aria-hidden="true"></i></div>
            </div>
        </div>
    </form>
    <div class="ynmember_content">
        <ul class="clearfix">
            {/if}
            {if count($aUsers)}
                {foreach from=$aUsers name=users item=aUser}
                <li class="{if $aUser.is_featured} feature{/if}{if $aUser.birthday_today} birthday{/if}">
                    {template file='ynmember.block.birth_page_block'}
                </li>
                {/foreach}
                {pager}
            {/if}
            {if $page == 1}
        </ul>
        {if !count($aUsers) && $page == 1}
        <div class="extra_info">
            {_p var='No upcoming birthdays found'}
        </div>
        {/if}
    </div>
</div>
{/if}


{if $page == 1}
{literal}
<script>
    $Behavior.initLandingSlider = function(){
        var initSlider = function() {
            var owl_article = jQuery('#ynmember_birthday_slider');
            var item_amount = parseInt(owl_article.find('.item').length);
            var true_false = item_amount > 2;
            var rtl = jQuery("html").attr("dir") === "rtl";

            owl_article.owlCarousel({
                rtl:rtl,
                nav: true_false,
                navText:["<i class='fa fa-angle-left' aria-hidden='true'></i>","<i class='fa fa-angle-right' aria-hidden='true'></i>"],
                loop: true_false,
                mouseDrag: false,
                touchDrag: true,
                dots: false,
                autoplay: false,
                dotsSpeed:1000,
                autoplayHoverPause:true,
                smartSpeed: 750,
                responsive:{
                    0:{
                        items:1
                    },
                    480:{
                        items:2
                    },
                    640:{
                        items:3
                    },
                    768:{
                        items:2
                    },
                    992:{
                        items:3
                    }
                }
            });

            $('.owl-buttons').addClass('dont-unbind');
            $('.owl-buttons .owl-prev').addClass('dont-unbind');
            $('.owl-prev').addClass('dont-unbind');
            $('.owl-next').addClass('dont-unbind');
            $('.owl-buttons .owl-next').addClass('dont-unbind');
            $('.owl-carousel').addClass('dont-unbind');
        };

        if (typeof($.fn.owlCarousel) === 'undefined') {
            var script = document.createElement('script');
            script.src = '{/literal}{$amCorePath}{literal}/assets/jscript/Owl-slider/owl.carousel.min.js';
            script.onload = initSlider;
            document.getElementsByTagName("head")[0].appendChild(script);
        } else {
            initSlider();
        }

        $('.ynmember_datepicker').datepicker({
            changeYear: false,
            dateFormat: 'mm/dd',
            onSelect: function(dateText){
                if ($('#from_date').val() && $('#to_date').val()) {
                    $('#ynmember_birthday_range').submit();
                }
            },
        });

        $('#from_date').datepicker("setDate", "{/literal}{$sStart}{literal}");
        $('#to_date').datepicker("setDate", "{/literal}{$sEnd}{literal}");

        $('.ynmember_calendar_icon').each(function(){
            $(this).click(function(){
                $(this).parent().find('.ynmember_datepicker').datepicker('show');
            });
        });
    };
</script>
{/literal}
{/if}