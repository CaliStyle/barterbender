<?php
\Core\Queue\Manager::instance()->addHandler('event_convert_old_location', '\Apps\Core_Events\Job\ConvertOldLocation');

