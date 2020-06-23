<?php

if($sClass == 'user.welcome') {
    $content = storage()->get('flavor/content/yncfbclone');
    if (empty($content->value)) {
        storage()->del('flavor/content/yncfbclone');
        storage()->set('flavor/content/yncfbclone', 'Welcome to our new released template');
    }
}
