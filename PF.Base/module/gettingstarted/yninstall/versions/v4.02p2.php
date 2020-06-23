<?php
$settingTable = Phpfox::getT('setting');
$languagePhrase = Phpfox::getT('language_phrase');
//Change wrong phrase text
$phrases = db()->select('phrase_id, text, text_default')
            ->from($languagePhrase)
            ->where('var_name = "setting_active_email_remainder"')
            ->execute('getSlaveRows');
if(!empty($phrases)) {
    foreach($phrases as $phrase) {
        $update = [
            'text_default' => "<title>Active Email Reminder</title><info>Set to True in order to enable Email Reminder.</info>"
        ];
        if(strcmp($phrase['text_default'], $phrase['text']) === 0) {
            $update['text'] = $update['text_default'];
        }
        db()->update($languagePhrase, $update, 'phrase_id = ' . (int)$phrase['phrase_id']);
    }
}