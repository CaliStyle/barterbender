<?php

//it is used to fix url hash from linkedin
$url = base64_decode($_GET['url']);

?>
<script type='text/javascript'>
	window.location.href='<?php echo $url; ?>';
</script>