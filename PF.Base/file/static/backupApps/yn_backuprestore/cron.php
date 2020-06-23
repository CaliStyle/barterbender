<?php
/**
 * This cron have 2 jobs
 * 1. Delete old backups
 * 2. Auto backup by schedule
 */
include 'cli.php';

/**
 * 1. Delete old backups
 */
$service = Phpfox::getService("ynbackuprestore.backup");
$service->cronDeleteBackups();

/**
 * 2. Auto backup by schedule
 */
$oScheduleService = Phpfox::getService("ynbackuprestore.schedule");
$schedules = $oScheduleService->getSchedules(['to_date' => date('Y-m-d H:i:s')]);

// backup
foreach ($schedules as $schedule) {
    // turn site offline
    $oScheduleService->turnSiteOffline();

    // get all destinations of schedule
    $destinations = Phpfox::getService('ynbackuprestore.destination')->getDestinationIdsByScheduleId($schedule['schedule_id']);
    $destination_ids = array();
    foreach ($destinations as $destination) {
        $destination_ids[] = $destination;
    }

    // add row to backup table
    $id = $service->addBackup([
        'prefix'           => $schedule['prefix'],
        'maintenance_mode' => $schedule['maintenance_mode'],
        'archive_format'   => $schedule['archive_format'],
        'destination_ids'  => $destination_ids
    ], json_decode($schedule['plugins_included'], true), json_decode($schedule['themes_included'], true),
        json_decode($schedule['uploads_included'], true), json_decode($schedule['database_included'], true),
        'automatic');

    // renew token if needed
    $service->renewToken($id);
    // process backup and tranfer backup to destinaion
    $service->processBackup($id);
    $service->finishBackup($id);
    $service->transferProcess($id);

    // update start_date to next time
    $oScheduleService->updateStartDate($schedule['schedule_id']);
}

// turn site online
$oScheduleService->turnSiteOffline(false);

echo "\nYn-BackupRestore: Cron run successfully";