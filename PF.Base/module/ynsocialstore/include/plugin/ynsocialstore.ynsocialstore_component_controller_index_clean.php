<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/28/16
 * Time: 11:36 AM
 */
?>

<?php
// ADD ADV SEARCH
$sFullControllerName = Phpfox::getLib('template')->getVar('sFullControllerName');

if ($sFullControllerName == 'ynsocialstore_index') {
    $title_search = _p('advanced_search');
    $classAdvSearch = 'filter-options';
    ?>

    <script type="text/javascript">
        $Behavior.ynstLoadContentIndex = function () {
            if ($('#stAdvSearchProduct').length == 0) {
                // For material template
                if ($('#page_ynsocialstore_index .header-filter-holder').length) {
                    var content = '<span id="stAdvSearchProduct" class="<?php echo $classAdvSearch; ?>"><a class="btn btn-xs btn-primary" onclick="ynsocialstore.advSearchProductDisplay(); return false;" href="javascript:void(0)"><?php echo $title_search; ?></a></span>';
                    $('#page_ynsocialstore_index .header-filter-holder').append(content);
                }
                // For bootstrap template
                else if ($('#page_ynsocialstore_index .header_filter_holder').length) {
                    var content = '<span id="stAdvSearchProduct" class="<?php echo $classAdvSearch; ?> inline-block"><a class="btn btn-sm btn-primary" onclick="ynsocialstore.advSearchProductDisplay(); return false;" href="javascript:void(0)"><?php echo $title_search; ?></a></span>';
                    $('#page_ynsocialstore_index .header_filter_holder').append(content);
                }
            }
        }
    </script>
    <?php
}
?>