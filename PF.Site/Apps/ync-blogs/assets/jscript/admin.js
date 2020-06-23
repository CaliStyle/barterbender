var ynblogAdmin = {
    $dataSourceSelect: $(),
    $definedTimeSelect: $(),
    initBlockSettings: function() {
        ynblogAdmin.$dataSourceSelect = $('select[name="val[value][data_source]"]');
        ynblogAdmin.$definedTimeSelect = $('select[name="val[value][defined_time]"]');

        if (!ynblogAdmin.$dataSourceSelect.length || !ynblogAdmin.$definedTimeSelect.length) {
            return;
        }
        ynblogAdmin.toggleDefinedTime();
        ynblogAdmin.$dataSourceSelect.change(ynblogAdmin.toggleDefinedTime);
    },
    toggleDefinedTime: function() {
        ynblogAdmin.$definedTimeSelect.closest('.form-group').toggle(ynblogAdmin.$dataSourceSelect.val() == 'most_popular');
    }
};

$Behavior.initYnblocBlockSettings = function(){
    ynblogAdmin.initBlockSettings();
};
