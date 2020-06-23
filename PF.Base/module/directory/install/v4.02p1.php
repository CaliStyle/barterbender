<?php


function ynd_install402p1()
{
    db()->delete(':block', ['component' => 'detailcheckinherelist', 'product_id' => 'younet_directory4']);
    db()->delete(':block', ['component' => 'detaillikelist', 'product_id' => 'younet_directory4']);
    db()->delete(':block', ['component' => 'detailphotomenu', 'product_id' => 'younet_directory4']);
    db()->delete(':block', ['component' => 'detaillikeshare', 'product_id' => 'younet_directory4']);
    db()->delete(':block', ['component' => 'detailcheckinlist', 'product_id' => 'younet_directory4']);
}

ynd_install402p1();