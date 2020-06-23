<?php
if ($this->request()->get('req2') == 'yncstatusbg' || $this->request()->get('id') == 'YNC_StatusBg') {
    $this->template()->setHeader([
        'css/admin.css' => 'app_ync-statusbg',
        'jscript/admin.js' => 'app_ync-statusbg'
    ])->setPhrase([
        'error',
        'notice',
        'collection_updated_successfully',
        'collection_added_successfully',
        'please_remove_all_error_files_first',
        'title_of_collection_is_required'
    ]);
}
