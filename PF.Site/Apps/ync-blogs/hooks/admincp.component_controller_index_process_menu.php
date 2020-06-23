<?php
if ($this->request()->get('req2') == 'block' && $this->request()->get('req3') == 'setting') {
    $this->template()->setHeader([
        'jscript/admin.js' => 'app_ync-blogs'
    ]);
}
