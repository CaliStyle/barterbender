<?php
if ($this->request()->get('req2') == 'ynbackuprestore' && in_array($this->request()->get('req3'),['manage-schedule', 'manage-backup'])) {
    $this->template()->setHeader([
        'datetimepicker/jquery.datetimepicker.full.js' => 'app_yn_backuprestore'
    ]);
}
