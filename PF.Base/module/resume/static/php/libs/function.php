<?php

function curlPost($postUrl, $postField)
{
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $postUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postField);
    //curl_setopt($ch, CURLOPT_RETURNTRANSFER, false); // receive server response ...
    
    curl_exec($ch);
    curl_close($ch);
}

function download($file, $name, $sMimeType = '')
{
    if (!$sMimeType)
    {
        $sMimeType = 'application/force-download';
    }
    
    header("Content-type: ".$sMimeType);
    header("Content-Disposition: attachment; filename=".$name);
    header("Content-Transfer-Encoding: binary");
    header('Pragma: no-cache');
    header('Expires: 0');
    
    set_time_limit(0);
    
    readfile($file);
}

/* creates a compressed zip file */
function create_zip($files = array(),$destination = '',$overwrite = false)
{
	//if the zip file already exists and overwrite is false, return false
	if(file_exists($destination) && !$overwrite)
    {
        return false;
    }
	//vars
	$valid_files = array();
	//if files were passed in...
	if(is_array($files))
    {
		//cycle through each file
		foreach($files as $file)
        {
			//make sure the file exists
			if(file_exists($file['filename']))
            {
				$valid_files[] = $file;
			}
		}
	}
	//if we have good files...
	if(count($valid_files))
    {
		//create the archive
		$zip = new ZipArchive();
		if($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true)
        {
			return false;
		}
		//add the files
		foreach($valid_files as $file)
        {
			$zip->addFile($file['filename'], $file['localname']);
		}
		//debug
		//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
		
		//close the zip -- done!
		$zip->close();
		
		//check to make sure the file exists
		return file_exists($destination);
	}
	else
	{
		return false;
	}
}

function clean($string)
{
    $string = htmlspecialchars_decode($string);
    $string = str_replace(" ", "-", $string); // Replaces all spaces with hyphens.
    $string = preg_replace('/[^A-Za-z0-9\-\_\.]/', '', $string); // Removes special chars.
    
    return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
}