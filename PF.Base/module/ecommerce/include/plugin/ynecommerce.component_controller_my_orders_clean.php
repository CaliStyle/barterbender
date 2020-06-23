<?php
;

defined('PHPFOX') or exit('NO DICE!');
if (Phpfox::isModule('ecommerce'))
{
    // ADD ADV SEARCH 
    $sFullControllerName = Phpfox::getLib('template')->getVar('sFullControllerName');
    
    if($sFullControllerName == 'ecommerce_my-orders')
    {
        $title_search = _p('advanced_search');
        $classAdvSearch = 'ynecommerce_adv_search';
    ?>

        <script type="text/javascript">
           $Behavior.ynEcommerceLoadAdvancedSearchMyOrders = function(){
                if ($('#ynEcommerceAdvancedSearch').length == 0)
                {
                    var content = '<span id="ynEcommerceAdvancedSearch" class="<?php echo $classAdvSearch; ?>"><a onclick="ynecommerce.advSearchDisplay(\'<?php echo $title_search; ?>\'); return false;" href="javascript:void(0)"><?php echo $title_search; ?></a></span>';
                    $('.header_bar_search').append(content);
                }
            }
        </script>

    <?php 
    }
}
;
?>