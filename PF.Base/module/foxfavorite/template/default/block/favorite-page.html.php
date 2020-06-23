
{if phpfox::isUser()}
    {$sHtml}
{if PHPFOX_IS_AJAX_PAGE}
    {literal}
    <script type="text/javascript" language="javascript">
        var gan_nut_unfavorite = true;
        $Behavior.onCreateFavoriteButtonBlock7 = function() {
            if($('#section_menu').size() == 0 && $('#yn_section_menu').html() != null)
            {
                $bt_favor = '<div id="ynfavorite"   class="yn_sectionmenu">' + $('#yn_section_menu').first().html() + '</div>';
                $('.profile-actions .profile-action-block:first').prepend($bt_favor); //cosmic theme
                $('#section_menu').show();
            }
            else
            {
                $bt_favor = $('.yn_page_favorite').first();
                $bt_unfavor = $('.unfavor');
                if (gan_nut_unfavorite) {
                    $('#ynfavorite').prepend($bt_unfavor);
                }
            }
            $Behavior.inlinePopup();
            $('#yn_section_menu').html('');
        };
    </script>
    {/literal}
{/if}
{/if}