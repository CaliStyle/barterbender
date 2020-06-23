<?php

if ( Phpfox::isModule( 'ynmember' ) ) {
	// ADD ADV SEARCH
	$sFullControllerName = Phpfox::getLib( 'template' )->getVar( 'sFullControllerName' );

	if ( $sFullControllerName == 'ynmember_index' ) {
		$title_search   = _p( 'Adv Search' );
		$classAdvSearch = 'ynmember_adv_search';
		?>

        <script type="text/javascript">
            $Behavior.ynmemberLoadContentIndex = function () {
                if ($('#ynmemberSearch').length === 0) {
                    var $member_index_page = $('#page_ynmember_index'),
                        $content = $('<span id="ynmemberSearch" class="<?php echo $classAdvSearch; ?>"><a onclick="ynmember.advSearchDisplay(\'<?php echo $title_search; ?>\'); return false;" href="javascript:void(0)" class="btn"><i class="fa fa-server space-right" aria-hidden="true"></i><?php echo $title_search; ?><i class="fa fa-caret-down space-left" aria-hidden="true"></i></a></span>'),
                        $bs_header_filter_holder = $member_index_page.find('.header_filter_holder'),
                        $mt_header_filter_holder = $member_index_page.find('.header-filter-holder'),
                        $search_btn = $content.find('a.btn'),
                        btn_classes = $mt_header_filter_holder.length ? "btn-xs btn-primary" : "btn-sm btn-primary"
                    ;
                    $search_btn.addClass(btn_classes);
                    $bs_header_filter_holder.length && $bs_header_filter_holder.append($content);
                    $mt_header_filter_holder.length && $mt_header_filter_holder.append($content);
                }
                ynmember.selectYnmemberMenu();
            }
        </script>

		<?php
	}
}