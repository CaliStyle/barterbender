<?php


function ynfe_install402p1()
{
    db()->delete(':block', ['component' => 'applyforrepeatevent', 'product_id' => 'younet_advevent4']);
    db()->delete(':block', ['component' => 'rsvp', 'product_id' => 'younet_advevent4']);
    db()->update(':block', ['ordering' => 10], ['module_id' => 'fevent', 'm_connection' => 'fevent.view', 'component' => 'category', 'location' => 1, 'ordering' => 7]);
    db()->delete(':block', ['component' => 'image', 'product_id' => 'younet_advevent4']);
    db()->delete(':block', ['component' => 'upcoming', 'product_id' => 'younet_advevent4']);
    db()->update(':setting', array('value_actual' => 'F j, Y', 'value_default' => 'F j, Y'),'module_id=\'fevent\' AND var_name=\'fevent_browse_time_stamp\'');
}

ynfe_install402p1();