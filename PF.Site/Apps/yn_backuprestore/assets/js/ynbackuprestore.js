/**
 * Created by huydnt on 05/01/2017.
 */
$Core.BackupRestore = {
    changeDestinationType: function () {
        $('#form-add-destination').submit();
    },
    deleteDestination: function (iId) {
        if (confirm("Are you sure you want to delete this destination?")) {
            $.ajaxCall('ynbackuprestore.deleteDestination', 'id=' + iId);
        }
    },
    checkAllDestination: function () {
        var checked = document.getElementById('destinations_check_all').checked;
        $('.destination_row_checkbox').each(function (index, element) {
            element.checked = checked;
        });
        $Core.BackupRestore.checkEnabled();
    },
    checkAllSchedule: function () {
        var checked = document.getElementById('schedules_check_all').checked;
        $('.schedule_row_checkbox').each(function (index, element) {
            element.checked = checked;
        });
        $Core.BackupRestore.scheduleCheckEnabled();
    },
    setButtonEnabled: function (enabled) {
        var delete_selected = $('.delete_selected');
        if (enabled) {
            delete_selected.removeClass('disabled');
            delete_selected.attr('disabled', false);
        }
        else {
            delete_selected.addClass('disabled');
            delete_selected.attr('disabled', true);
        }
    },
    checkEnabled: function () {
        var enabled = false;
        $('.destination_row_checkbox').each(function (index, element) {
            if (element.checked) {
                enabled = true;
                return false;
            }
        });
        $Core.BackupRestore.setButtonEnabled(enabled);
    },
    scheduleCheckEnabled: function () {
        var enabled = false;
        $('.schedule_row_checkbox').each(function (index, element) {
            if (element.checked) {
                enabled = true;
                return false;
            }
        });
        $Core.BackupRestore.setButtonEnabled(enabled);
    },
    deleteSelected: function (obj) {
        $.ajaxCall('ynbackuprestore.deleteSelected', $(obj).serialize(), 'post');
        return false;
    },
    deleteSelectedSchedules: function (obj) {
        $.ajaxCall('ynbackuprestore.deleteSelectedSchedules', $(obj).serialize(), 'post');
        return false;
    },
    validateBackupForm: function () {
        var error_include =$("#error_include");
        var error_prefix =$("#error_prefix");
        var error_plugin =$("#error_plugin");
        var error_theme =$("#error_theme");
        var error_upload =$("#error_upload");
        var error_database =$("#error_database");
        error_include.hide();
        error_prefix.hide();
        error_plugin.hide();
        error_theme.hide();
        error_upload.hide();
        error_database.hide();
        var isValid = true;
        var isIncluded = false;

        if ($('#backuprestore_plugin').is(':checked')) {
            isIncluded = true;
            var pluginChecked = false;
            $('#table_plugin').find('input').each(function () {
                if ($(this).is(':checked')) {
                    pluginChecked = true;
                    return false;
                }
            });
            if (!pluginChecked) {
                error_plugin.show();
                isValid = false;
            }
        }
        if ($('#backuprestore_theme').is(':checked')) {
            isIncluded = true;
            var themeChecked = false;
            $('#table_theme').find('input').each(function () {
                if ($(this).is(':checked')) {
                    themeChecked = true;
                    return false;
                }
            });
            if (!themeChecked) {
                error_theme.show();
                isValid = false;
            }
        }
        if ($('#backuprestore_upload').is(':checked')) {
            isIncluded = true;
            var uploadChecked = false;
            $('#table_upload').find('input').each(function () {
                if ($(this).is(':checked')) {
                    uploadChecked = true;
                    return false;
                }
            });
            if (!uploadChecked) {
                error_upload.show();
                isValid = false;
            }
        }
        if ($('#backuprestore_database').is(':checked')) {
            isIncluded = true;
            var databaseChecked = false;
            $('#table_database').find('input').each(function () {
                if ($(this).is(':checked')) {
                    databaseChecked = true;
                    return false;
                }
            });
            if (!databaseChecked) {
                error_database.show();
                isValid = false;
            }
        }
        if (!isIncluded) {
            error_include.show();
            isValid = false;
        }

        if ($("#prefix").val() == "") {
            error_prefix.show();
            isValid = false;
        }

        return isValid;
    },
    updateProgress: function (percent, status, id) {
        var progress = setInterval(frame, 1);

        function frame() {
            var myBar = $('#myBar');
            var currentWidth = ( 100 * parseFloat(myBar.width()) / parseFloat(myBar.parent().width()) );
            if (currentWidth > percent) {
                // update status
                $('#process_status').text(status);
                switch (percent) {
                    case 5:
                        // process backup
                        $.ajaxCall('ynbackuprestore.backupProcess', 'id=' + id);
                        break;
                    case 45:
                        // finish backup
                        $.ajaxCall('ynbackuprestore.finishBackup', 'id=' + id);
                        break;
                    case 75:
                        // process backup
                        $.ajaxCall('ynbackuprestore.transferProcess', 'id=' + id);
                        break;
                    case 100:
                        clearInterval(timer);
                        break;
                    default:
                        break;
                }
                clearInterval(progress);
            } else {
                currentWidth += 0.25;
                myBar.width(currentWidth + '%');
                $("#label").text(parseInt(currentWidth) + '%');
            }
        }
    },
    backupProcess: function (id, status) {
        // update progress
        $Core.BackupRestore.updateProgress(5, status, id);
    },
    finishBackup: function (id, status) {
        // update progress
        $Core.BackupRestore.updateProgress(45, status, id);
    },
    transferProcess: function (id, status) {
        // update progress
        $Core.BackupRestore.updateProgress(75, status, id);
    },
    doneBackup: function (id, status, size, title) {
        // update progress
        $Core.BackupRestore.updateProgress(100, status, id);
        // show info
        var backup_message =$("#backup_message");
        backup_message.css("background-color", "#4CAF50");
        backup_message.text("The backup completed successfully!");
        $("#file_size").text(size);
        $("#backup_file").text(title);
        $("#backup_info").show();
    },
    deleteSchedule: function (id) {
        if (confirm("Are you sure you want to delete this schedule?")) {
            $.ajaxCall('ynbackuprestore.deleteSchedule', 'id=' + id);
        }
    },
    updateRestoreProgress: function (percent, status, filename) {
        var progress = setInterval(frame, 1);

        function frame() {
            var myBar = $('#myBar');
            var currentWidth = ( 100 * parseFloat(myBar.width()) / parseFloat(myBar.parent().width()) );
            if (currentWidth > percent) {
                // update status
                $('#process_status').text(status);
                switch (percent) {
                    case 15:
                        $.ajaxCall('ynbackuprestore.restorePlugins', 'file=' + filename);
                        break;
                    case 35:
                        $.ajaxCall('ynbackuprestore.restoreThemes', 'file=' + filename);
                        break;
                    case 55:
                        $.ajaxCall('ynbackuprestore.restoreUploads', 'file=' + filename);
                        break;
                    case 75:
                        $.ajaxCall('ynbackuprestore.restoreDatabase', 'file=' + filename);
                        break;
                    case 90:
                        $.ajaxCall('ynbackuprestore.cleanup', 'file=' + filename);
                        break;
                    case 100:
                        clearInterval(timer);
                        break;
                    default:
                        break;
                }
                clearInterval(progress);
            } else {
                currentWidth += 0.25;
                $("#myBar").width(currentWidth + '%');
                $("#label").text(parseInt(currentWidth) + '%');
            }
        }
    },
    restorePlugins: function (fileName, status) {
        $Core.BackupRestore.updateRestoreProgress(15, status, fileName);
    },
    restoreThemes: function (fileName, status) {
        $Core.BackupRestore.updateRestoreProgress(35, status, fileName);
    },
    restoreUploads: function (fileName, status) {
        $Core.BackupRestore.updateRestoreProgress(55, status, fileName);
    },
    restoreDatabase: function (fileName, status) {
        $Core.BackupRestore.updateRestoreProgress(75, status, fileName);
    },
    cleanup: function (fileName, status) {
        $Core.BackupRestore.updateRestoreProgress(90, status, fileName);
    },
    finishRestore: function (fileName, status) {
        $Core.BackupRestore.updateRestoreProgress(100, status, fileName);
        // show info
        var backup_message = $("#backup_message");
        backup_message.css("background-color", "#4CAF50");
        backup_message.text("The restore completed successfully!");
        $("#backup_info").show();
    },
    validateScheduleForm: function () {
        var error_include =$("#error_include");
        var error_prefix =$("#error_prefix");
        var error_plugin =$("#error_plugin");
        var error_theme =$("#error_theme");
        var error_upload =$("#error_upload");
        var error_database =$("#error_database");
        error_include.hide();
        error_prefix.hide();
        error_plugin.hide();
        error_theme.hide();
        error_upload.hide();
        error_database.hide();
        var isValid = true;
        var isIncluded = false;

        if ($('#backuprestore_plugin').is(':checked')) {
            isIncluded = true;
            var pluginChecked = false;
            $('#table_plugin').find('input').each(function () {
                if ($(this).is(':checked')) {
                    pluginChecked = true;
                    return false;
                }
            });
            if (!pluginChecked) {
                error_plugin.show();
                isValid = false;
            }
        }
        if ($('#backuprestore_theme').is(':checked')) {
            isIncluded = true;
            var themeChecked = false;
            $('#table_theme').find('input').each(function () {
                if ($(this).is(':checked')) {
                    themeChecked = true;
                    return false;
                }
            });
            if (!themeChecked) {
                error_theme.show();
                isValid = false;
            }
        }
        if ($('#backuprestore_upload').is(':checked')) {
            isIncluded = true;
            var uploadChecked = false;
            $('#table_upload').find('input').each(function () {
                if ($(this).is(':checked')) {
                    uploadChecked = true;
                    return false;
                }
            });
            if (!uploadChecked) {
                error_upload.show();
                isValid = false;
            }
        }
        if ($('#backuprestore_database').is(':checked')) {
            isIncluded = true;
            var databaseChecked = false;
            $('#table_database').find('input').each(function () {
                if ($(this).is(':checked')) {
                    databaseChecked = true;
                    return false;
                }
            });
            if (!databaseChecked) {
                error_database.show();
                isValid = false;
            }
        }
        if (!isIncluded) {
            error_include.show();
            isValid = false;
        }

        if ($("#prefix").val() == "") {
            error_prefix.show();
            isValid = false;
        }
        if ($("#schedule_name").val() == "") {
            $("#error_name").show();
            isValid = false;
        }
        if ($("#datetime").val() == "") {
            $("#error_datetime").show();
            isValid = false;
        }

        return isValid;
    },
    getListBuckets: function () {
        var s3_access = $("#s3_access").val();
        var s3_secret = $("#s3_secret").val();
        if (s3_access != "" && s3_secret != "") {
            $("#amazon_no_bucket").hide();
            var s3_get_list_bucket = $("#s3_get_list_bucket");
            s3_get_list_bucket.prop('disabled', true);
            s3_get_list_bucket.val('Waiting...');
            $.ajaxCall('ynbackuprestore.getListBuckets', "access=" + s3_access + "&secret=" + s3_secret);
        } else {
            $Core.BackupRestore.addAmazonError('Please enter both Access Key and Secret Key before get list buckets');
        }
    },
    addListBuckets: function (buckets) {
        if (buckets.length == 0) {
            $("#amazon_no_bucket").show();
        } else {
            $('#s3_bucket').empty();
            for (var i = 0; i < buckets.length; i++) {
                $('#s3_bucket')
                    .append($("<option></option>")
                        .attr("value", buckets[i])
                        .text(buckets[i]));
            }
            var s3_get_list_bucket = $("#s3_get_list_bucket");
            s3_get_list_bucket.prop('disabled', false);
            s3_get_list_bucket.val('Get List Buckets');
        }
    },
    addAmazonError: function (error) {
        $("#amazon_no_bucket").text(error).show();
        var s3_get_list_bucket = $("#s3_get_list_bucket");
        s3_get_list_bucket.prop('disabled', false);
        s3_get_list_bucket.val('Get List Buckets');
    },
    wrongRestoreFile: function () {
        $("#myBar").css('background-color', '#d9534f');
        $("#process_status").html('<strong>' + oTranslations['FAILED! WRONG RESTORE FILE!'] + '</strong>');
        clearInterval(timer);
    },
    validateFolderPermission: function(filename) {
        $().ajaxCall('ynbackuprestore.validateFolderPermission', 'file=' + filename);
    }
};