<?php

if ( Phpfox::isModule( 'ynmember' ) ) {
	// ADD ADV SEARCH
	$sFullControllerName = Phpfox::getLib( 'template' )->getVar( 'sFullControllerName' );

	if ( $sFullControllerName == 'ynmember_review' ) {
		$title_search   = _p( 'Advanced Search' );
		$classAdvSearch = 'ynmember_adv_search';
		?>

        <script type="text/javascript">
            $Behavior.ynmemberLoadContentIndex = function () {
                $('input[name="search[reviewer]"]').attr('placeholder', '<?php echo _p( 'Reviewed by' )?>')
                if ($('#ynmemberSearch').length === 0) {
                    var $member_review_page = $('#page_ynmember_review'),
                        $content = $('<span id="ynmemberSearch" class="<?php echo $classAdvSearch; ?>"><a onclick="ynmember.advSearchDisplay(\'<?php echo $title_search; ?>\'); return false;" href="javascript:void(0)" class="btn"><i class="fa fa-server space-right" aria-hidden="true"></i><?php echo $title_search; ?></a></span>'),
                        $bs_header_filter_holder = $member_review_page.find('.header_filter_holder'),
                        $mt_header_filter_holder = $member_review_page.find('.header-filter-holder'),
                        $search_btn = $content.find('a.btn'),
                        btn_classes = $mt_header_filter_holder.length ? "btn-xs btn-primary" : "btn-sm btn-primary"
                    ;
                    $search_btn.addClass(btn_classes);
                    $bs_header_filter_holder.length && $bs_header_filter_holder.append($content);
                    $mt_header_filter_holder.length && $mt_header_filter_holder.append($content);
                }
            }
        </script>

		<?php
	}
}