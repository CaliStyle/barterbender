;var manageproducts = {
    showLoading: function (iProductId) {
        $('#ynsocialstore_loading').show();
    },
    hideLoading: function (iProductId) {
        $('#ynsocialstore_loading').hide();
    },
    confirmdeleteProduct: function (iProductId) {
        // Open directly via API
        $Core.jsConfirm({
            message: oTranslations['ynsocialstore.are_you_sure_want_to_delete_this_product_this_action_cannot_be_reverted'],
        }, function () {
            manageproducts.deleteProduct(iProductId);
        }, function () {
        });
    },
    confirmDeleteProducts: function (sElementId) {
        // Open directly via API
        $Core.jsConfirm({
            message: oTranslations['ynsocialstore.are_you_sure_want_to_delete_these_products_this_action_cannot_be_reverted'],
        }, function () {
            $('#' + sElementId).submit();
        }, function () {
        });
    },
    deleteProduct: function (iProductId, iOwnerId) {
        manageproducts.showLoading();
        $.ajaxCall('ynsocialstore.deleteProduct', $.param({iProductId: iProductId, iOwnerId: iOwnerId}));
    },
    denyProduct: function(iProductId, sStatus){
        manageproducts.showLoading();
        $.ajaxCall('ynsocialstore.denyProduct', $.param({iProductId: iProductId, sStatus: sStatus}));
    },
    approveProduct: function(iProductId, sStatus){
        manageproducts.showLoading();
        $.ajaxCall('ynsocialstore.approveProduct', $.param({iProductId: iProductId, sStatus: sStatus}));
    },
    featureProduct: function(iProductId, iOwnerId, sStatus, bIsFeatured){
        manageproducts.showLoading();
        $.ajaxCall('ynsocialstore.featureProduct', $.param({iProductId: iProductId, iOwnerId: iOwnerId, sStatus: sStatus, bIsFeatured: bIsFeatured}));
    },
    reopenProduct: function(iProductId, iOwnerId, sStatus){
        manageproducts.showLoading();
        $.ajaxCall('ynsocialstore.reopenProduct', $.param({iProductId: iProductId, iOwnerId: iOwnerId, sStatus: sStatus}));
    },
    closeProduct: function(iProductId, iOwnerId, sStatus){
        manageproducts.showLoading();
        $.ajaxCall('ynsocialstore.closeProduct', $.param({iProductId: iProductId, iOwnerId: iOwnerId, sStatus: sStatus}));
    },
};