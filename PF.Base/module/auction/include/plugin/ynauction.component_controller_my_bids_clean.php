<?php
;

defined('PHPFOX') or exit('NO DICE!');
if (Phpfox::isModule('auction'))
{
    // ADD ADV SEARCH 
    $sFullControllerName = Phpfox::getLib('template')->getVar('sFullControllerName');
    
    if($sFullControllerName == 'auction_my-bids')
    {
        $title_search = _p('advanced_search');
        $classAdvSearch = 'filter-options';
    ?>

        <script type="text/javascript">
           $Behavior.ynAuctionLoadContentIndex = function(){
                if ($('#ynAuctionAdvancedSearch').length == 0)
                {
                    var content = '<span id="ynAuctionAdvancedSearch" class="<?php echo $classAdvSearch; ?>"><a class="dropdown-toggle" onclick="ynauction.advSearchDisplay(); return false;" href="javascript:void(0)"><?php echo $title_search; ?><span class="ico ico-caret-down"></span></a></span>';
                    $('#page_auction_my-bids .header-filter-holder').append(content);
                }
            }
        </script>

    <?php 
    }
}
;
?>