<?php
ini_set('display_errors', true);error_reporting(E_ALL ^ E_NOTICE);



$result = Array("error" => "", "rows" => Array());
if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];

//------------Settings-----------------------------------

    $possibleFiles = Array(
        'audio/mpeg',
        'audio/wav',
        'video/mp4',
        'video/quicktime',
        'video/webm',
        'video/ogg',
        'application/x-shockwave-flash',
        'image/jpeg',
        'image/png',
        'image/gif'
    );
    $imgFiles = Array(
        'image/jpeg',
        'image/png',
        'image/gif'
    );

//------------Settings-----------------------------------



    $dir = "";
    if (isset($_REQUEST['s3dir']) && $_REQUEST['s3dir'])
        $dir = "" . $_REQUEST['s3dir'];
        $dir = urldecode($dir);

    if ($action == "clean") {
        $files = Array();
         if (isset($_REQUEST['files']) && $_REQUEST['files']) $files =  $_REQUEST['files'];
         else{
             $result['error'] = "Files not set.\n";
         }
         
            $removeFiles = scandir($dir);
		//echo $i." of ".count($files)."";
		$removeFiles = array_slice($removeFiles, 2);

            
            foreach ($removeFiles AS $key => $object) {
                foreach ($files AS $file) {
                    if (stristr($file, $object)) {
                        unset($removeFiles[$key]);
                    }
                }
            }
           //here delete files

    }
    else
    if ($action == "list") {

            $files = scandir($dir);
	    $files = array_slice($files, 2);

    } else if ($action == "upload") {

        try {

            if (isset($_FILES['file'])) {
                $video = "";
                if (isset($_POST['videoInfo']))
                    $video = $_POST['videoInfo'];

                foreach ($_FILES['file']['name'] AS $key => $value) {
                    $fileName = $value;
                    $width = "";
                    $height = "";
                    if (in_array($_FILES['file']['type'][$key], $possibleFiles)) {
                        if ($video) {
                            foreach ($video AS $val) {
                                if ($value == $val['0']) {
                                    $fileName = $val['1'];
                                    $width = $val['2'];
                                    $height = $val['3'];
                                }
                            }
                        }
                        if(in_array($_FILES['file']['type'][$key], $imgFiles)){
                            list($widthtemp, $heighttemp, $type, $attr) = getimagesize($_FILES['file']['tmp_name'][$key]);
                            $width = $widthtemp;
                            $height = $heighttemp;
                        }
                             if(!is_dir($dir)){mkdir($dir,0777);}
                             $tmp_name = $_FILES["file"]["tmp_name"][$key];
                             $name = $_FILES["file"]["name"][$key];
                             //echo $dir."/".$name;
                             //echo  $tmp_name;
                             move_uploaded_file($tmp_name, $dir."/".$name);

                        $result['rows'][] = Array("name" => $name, "url" => $dir."/".$name, "width" => $width, "height" => $height);
                    }
                }
            }
        } catch (Aws\Exception\S3Exception $e) {
            $result['error'] = "There was an error uploading the file.\n";
        }
    } else {
        $result['error'] = "Action can be 'list' or 'upload'!";
    }
} else {
    $result['error'] = "Action not set!";
}

echo json_encode($result);
?>