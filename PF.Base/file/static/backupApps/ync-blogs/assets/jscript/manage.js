
var ynadvancedblog_manage = {
	updateFeatured: function(iBlogId, iIsFeatured)
	{
		$.ajaxCall('ynblog.updateFeaturedInAdmin', $.param({iBlogId: iBlogId, iIsFeatured: iIsFeatured, global_ajax_message: true}));
	},

	actionMultiSelect : function(obj)
	{
		$.ajaxCall('ynblog.actionMultiSelectBlog',$(obj).serialize()+'&global_ajax_message=true','post');
		return false;
	},
	switchAction : function (event,sType)
	{
        event.preventDefault();
		switch(sType){
			case 'delete':
				$("#ynab_multi_select_action").val('1');
				break;
			case 'approve':
				$("#ynab_multi_select_action").val('2');
				break;
			case 'deny':
				$("#ynab_multi_select_action").val('3');
				break;
		}
	},
	getSearchData: function (obj, sType)
	{
        $Core.ajaxMessage();
		switch(sType) {
			case 'adminImport':
                $.ajaxCall('ynblog.filterAdminFilterImportBlog',$(obj).serialize()+'&global_ajax_message=true','post');
                break;
            case 'adminManage':
            default:
                $.ajaxCall('ynblog.filterAdminFilterBlog',$(obj).serialize()+'&global_ajax_message=true','post');
                break;
		}
		return false;
	},
    chooseCategoryInAdmin: function (obj, iBlogId) {
		if (iBlogId) {
            tb_show('', $.ajaxBox('ynblog.chooseCategoryInAdmin', 'height=300&width=420&iBlogId=' + iBlogId));
        } else {
            tb_show('', $.ajaxBox('ynblog.chooseCategoryInAdmin', 'height=300&width=420&' + $(obj).parents('form').serialize()));
		}
        return false;
    },
    importBlogInAdminProcess: function (obj) {
        $Core.ajaxMessage();
        $.ajaxCall('ynblog.importBlogInAdmin',$(obj).parents('form').serialize()+'&global_ajax_message=true','post');
        return false;
    }
}