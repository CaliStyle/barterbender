<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/12/16
 * Time: 1:41 PM
 */
?>

<?php
// ADD ADV SEARCH
$sFullControllerName = Phpfox::getLib('template')->getVar('sFullControllerName');

if ($sFullControllerName == 'ynsocialstore_store_index') {
    $title_search = _p('advanced_search');
    $classAdvSearch = 'filter-options';
    ?>

    <script type="text/javascript">
        $Behavior.ynstLoadContentIndex = function () {
            if ($('#stAdvSearch').length == 0) {
                if ($('#page_ynsocialstore_store_index .header-filter-holder').length) {
                    var content = '<span id="stAdvSearch" class="<?php echo $classAdvSearch; ?>"><a class="btn btn-xs btn-primary" onclick="ynsocialstore.advSearchDisplay(); return false;" href="javascript:void(0)"><?php echo $title_search; ?></a></span>';
                    $('#page_ynsocialstore_store_index .header-filter-holder').append(content);
                } else if ($('#page_ynsocialstore_store_index .header_filter_holder').length) {
                    var content = '<span id="stAdvSearch" class="<?php echo $classAdvSearch; ?> inline-block"><a class="btn btn-sm btn-primary" onclick="ynsocialstore.advSearchDisplay(); return false;" href="javascript:void(0)"><?php echo $title_search; ?></a></span>';
                    $('#page_ynsocialstore_store_index .header_filter_holder').append(content);
                }
            }
        }
    </script>
    <?php
}
?>