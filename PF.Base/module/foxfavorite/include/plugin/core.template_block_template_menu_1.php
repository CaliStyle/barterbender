<?php 
?>
<script type="text/javascript">
function ynfoxfavorite_loadlazyimage()
{
	$('.image_deferred:not(.built)').each(function() {
		var t = $(this),
			src = t.data('src'),
			i = new Image();

		t.addClass('built');
		if (!src) {
			t.addClass('no_image');
			return;
		}

		t.addClass('has_image');
		i.onerror = function(e, u) {
			t.replaceWith('');
		};
		i.onload = function(e) {
			t.attr('src', src);
		};
		i.src = src;
	});

	$('.image_load:not(.built)').each(function() {
		var t = $(this),
			src = t.data('src'),
			i = new Image();

		t.addClass('built');
		if (!src) {
			t.addClass('no_image');
			return;
		}

		t.addClass('has_image');
		i.onload = function(e) {
			t.css('background-image', 'url(' + src + ')');
		};
		i.src = src;
	});
}
</script>

<?php ?>