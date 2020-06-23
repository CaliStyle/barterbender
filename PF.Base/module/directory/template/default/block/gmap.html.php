<div style="height:300px;">
	<div style="width: 450px; height: 300px;" align="center" id="yndirectory_map"><br/></div>

	{literal}
		<script type="text/javascript">
			if(undefined !== yndirectory && null !== yndirectory){
				var item = '{/literal}{$item}{literal}';
				yndirectory.viewMapSuccess(item);
			}
		</script>
	{/literal}
</div>
