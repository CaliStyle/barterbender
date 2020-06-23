;var package_index = {
	showLoading: function(){
		$('#yndirectory_loading').show();
	}, 
	hideLoading: function(){
		$('#yndirectory_loading').hide();
	},
    confirmdelete: function(id){
        $Core.jsConfirm({message: oTranslations['directory.are_you_sure']}, function () {
            $.ajaxCall('directory.deletepackage', 'id=' + id);
        }, function () {

        });
        return false;
    },
	delete: function(id){
		package_index.showLoading();
		$.ajaxCall('directory.deletepackage', 'id=' + id);
	}, 
}; 