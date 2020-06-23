;var managestores = {
    showLoading: function (iStoreId) {
        $('#ynsocialstore_loading').show();
    },
    hideLoading: function (iStoreId) {
        $('#ynsocialstore_loading').hide();
    },
    confirmdeleteStore: function (iStoreId) {
        // Open directly via API
        $Core.jsConfirm({
            message: oTranslations['ynsocialstore.are_you_sure_want_to_delete_this_store_this_action_cannot_be_reverted_and_all_products_in_store_will_be_lost'],
        }, function () {
            managestores.deleteStore(iStoreId);
        }, function () {
        });
    },
    confirmDeleteStores: function (sElementId) {
        // Open directly via API
        $Core.jsConfirm({
            message: oTranslations['ynsocialstore.are_you_sure_want_to_delete_these_stores_this_action_cannot_be_reverted_and_all_products_in_these_stores_will_be_lost'],
        }, function () {
            $('#' + sElementId).submit();
        }, function () {
        });
    },
    deleteStore: function (iStoreId, iOwnerId) {
        managestores.showLoading();
        $.ajaxCall('ynsocialstore.deleteStore', $.param({iStoreId: iStoreId, iOwnerId: iOwnerId}));
    },
    denyStore: function(iStoreId, sStatus){
        managestores.showLoading();
        $.ajaxCall('ynsocialstore.denyStore', $.param({iStoreId: iStoreId, sStatus: sStatus}));
    },
    approveStore: function(iStoreId, sStatus){
        managestores.showLoading();
        $.ajaxCall('ynsocialstore.approveStore', $.param({iStoreId: iStoreId, sStatus: sStatus}));
    },
    featureStore: function(iStoreId, iOwnerId, sStatus, bIsFeatured){
        managestores.showLoading();
        $.ajaxCall('ynsocialstore.featureStore', $.param({iStoreId: iStoreId, iOwnerId: iOwnerId, sStatus: sStatus, bIsFeatured: bIsFeatured}));
    },
    reopenStore: function(iStoreId, iOwnerId, sStatus){
        managestores.showLoading();
        $.ajaxCall('ynsocialstore.reopenStore', $.param({iStoreId: iStoreId, iOwnerId: iOwnerId, sStatus: sStatus}));
    },
    closeStore: function(iStoreId, iOwnerId, sStatus){
        managestores.showLoading();
        $.ajaxCall('ynsocialstore.closeStore', $.param({iStoreId: iStoreId, iOwnerId: iOwnerId, sStatus: sStatus}));
    },
};