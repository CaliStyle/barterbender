<?php
$sFullControllerName = Phpfox::getLib('template')->getVar('sFullControllerName');
if(isset($sFullControllerName) == true && strpos($sFullControllerName, "jobposting") !== false)
{
    ?> <script type="text/javascript">
        $Behavior.onLoadMenuJP = function() {
            $('.breadcrumbs_right_section').find('.page_breadcrumbs_menu a').addClass('no_ajax');
            $('.breadcrumbs_menu').find('ul > li a').addClass('no_ajax');
        }
    </script>
    <?php
}
?>
<?php
// ADD ADV SEARCH
$sFullControllerName = Phpfox::getLib('template')->getVar('sFullControllerName');

if ($sFullControllerName == 'jobposting_index') {
    $title_search = _p('search');
    $classAdvSearch = 'jb_adv_search filter-options';
    ?>

    <script type="text/javascript">
        $Behavior.ynjbLoadAdvSearchIndex = function () {
            if ($('#jpAdvSearch').length === 0) {
                // For material template
                if ($('#page_jobposting_index .header-filter-holder').length) {
                    var content = '<span id="jpAdvSearch" class="<?php echo $classAdvSearch; ?>"><a class="btn btn-xs btn-primary" onclick="ynjobposting.advSearchDisplay(\'<?php echo $title_search; ?>\'); return false;" href="javascript:void(0)"><?php echo $title_search; ?></a></span>';
                    $('#page_jobposting_index .header-filter-holder').append(content);
                }
                // For bootstrap template
                else if ($('#page_jobposting_index .header_filter_holder').length) {
                    var content = '<span id="jpAdvSearch" class="<?php echo $classAdvSearch; ?> inline-block"><a class="btn btn-sm btn-primary" onclick="ynjobposting.advSearchDisplay(\'<?php echo $title_search; ?>\'); return false;" href="javascript:void(0)"><?php echo $title_search; ?></a></span>';
                    $('#page_jobposting_index .header_filter_holder').append(content);
                }
            }
        }
    </script>
    <?php
}

if ($sFullControllerName == 'jobposting_company_index') {
    $title_search = _p('search');
    $classAdvSearch = 'jb_adv_search_company filter-options';
    ?>

    <script type="text/javascript">
        $Behavior.ynjbLoadAdvSearchComanyIndex = function () {
            if ($('#jpAdvSearchCompany').length === 0) {
                // For material template
                if ($('#page_jobposting_company_index .header-filter-holder').length) {
                    var content = '<span id="jpAdvSearchCompany" class="<?php echo $classAdvSearch; ?>"><a class="btn btn-xs btn-primary" onclick="ynjobposting.advSearchCompanyDisplay(\'<?php echo $title_search; ?>\'); return false;" href="javascript:void(0)"><?php echo $title_search; ?></a></span>';
                    $('#page_jobposting_company_index .header-filter-holder').append(content);
                }
                // For bootstrap template
                else if ($('#page_jobposting_company_index .header_filter_holder').length) {
                    var content = '<span id="jpAdvSearchCompany" class="<?php echo $classAdvSearch; ?> inline-block"><a class="btn btn-sm btn-primary" onclick="ynjobposting.advSearchCompanyDisplay(\'<?php echo $title_search; ?>\'); return false;" href="javascript:void(0)"><?php echo $title_search; ?></a></span>';
                    $('#page_jobposting_company_index .header_filter_holder').append(content);
                }
            }
        }
    </script>
    <?php
}
?>