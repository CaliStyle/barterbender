<?php

$file_path = dirname(__file__);
$pos = strpos($file_path, DIRECTORY_SEPARATOR.'module'.DIRECTORY_SEPARATOR);

if ($pos !== false)
{
    $path = (dirname(__file__)).DIRECTORY_SEPARATOR.'cron.php';
}
else
{
    $path = ((dirname(__file__))).DIRECTORY_SEPARATOR.'cron.php';
}

echo "php ".$path;

?>
