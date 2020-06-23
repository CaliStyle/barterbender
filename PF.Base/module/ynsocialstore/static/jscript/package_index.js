;var package_index = {
	showLoading: function(){
		$('#ynsocialstore_loading').show();
	}, 
	hideLoading: function(){
		$('#ynsocialstore_loading').hide();
	}, 
	confirmdelete: function(id){
		// Open directly via API
		var sHtml = '';
		sHtml += '<div class="white-popup-block" style="width: 300px;">'; 
			sHtml += '<div>'; 
				sHtml += oTranslations['ynsocialstore.are_you_sure'];
			sHtml += '</div>'; 
			sHtml += '<div style="margin-top: 10px; text-align: right;">'; 
				sHtml += '<button class="btn btn-danger" onclick="package_index.delete(' + id + ');">';
					sHtml += oTranslations['ynsocialstore.yes'];
				sHtml += '</button>'; 
				sHtml += '<button class="btn btn-danger" style="margin-left: 10px;" onclick="$(\'.mfp-close-btn-in .mfp-close\').trigger(\'click\');">';
					sHtml += oTranslations['ynsocialstore.no'];
				sHtml += '</button>'; 
			sHtml += '</div>'; 
		sHtml += '</div>'; 
		$.magnificPopup.open({
		  items: {
		    src: sHtml, 
		    type: 'inline'
		  }
		});
	}, 
	delete: function(id){
		package_index.showLoading();
		$.ajaxCall('ynsocialstore.deletepackage', 'id=' + id);
	}, 
}; 