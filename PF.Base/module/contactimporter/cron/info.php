<?php
$file_path = dirname(__FILE__);
$pos = strpos($file_path,DIRECTORY_SEPARATOR.'module'.DIRECTORY_SEPARATOR);
if( $pos!== false)
{
    $path1 = (dirname(__FILE__)).DIRECTORY_SEPARATOR.'sendinvite.php';    
    $path2 = (dirname(__FILE__)).DIRECTORY_SEPARATOR.'sendmail.php';    
}
else
{
    $path1 = ((dirname(__FILE__))).DIRECTORY_SEPARATOR.'sendinvite.php.php';
    $path2 = (dirname(__FILE__)).DIRECTORY_SEPARATOR.'sendmail.php';  
}
echo "php ".$path1;
echo '<br/>';
echo "php ".$path2;
?>
