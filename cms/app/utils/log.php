<?php

if ($_POST["action"]!="getContent" && $_POST["action"]!="getNews" && $_POST["action"]!="getFolderId"){
 

$file = ''; 
if (count($_FILES)){
    $file = (trim($_FILES['Filedata']['name']));
} else if ($_POST["file"]){
    $file = $_POST["file"];
} else if ($_POST["aim"]){
    $file = $_POST["aim"];
}

$iid = $_POST["id"];
if (!$iid) {$iid = $_POST["parent_id"];}

$f = fopen("user_log/".date("Y-m-d").".txt","a+");
fwrite($f, date("H:m:s")."\n");
fwrite($f, getUserIP()."\n");
fwrite($f, $_SESSION["user"]."\n");
fwrite($f, $_POST["action"]."\n");
fwrite($f, "section_id=".$iid.", ".$file.", ".$_POST["name"].", ".implode(",",$_POST["files"]).", ".implode(",",$_POST["folders"]).", ".$_POST["type"]."\n");
fwrite($f, $parent_path.' '.$path."\n");
fwrite($f, json_encode($_POST) ."\n");
fwrite($f, "<->"."\n");

fclose($f);

$f = fopen("user_log/".date("Y-m-d")."_full.txt","a+");
fwrite($f, json_encode($_POST) ."\n");
fwrite($f, "<->"."\n");
fclose($f);

}

    function getUserIP() 
    { 

        $client = @$_SERVER['HTTP_CLIENT_IP']; 
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR']; 
        return filter_var($client, FILTER_VALIDATE_IP) ? $client : filter_var($forward, FILTER_VALIDATE_IP) ? $forward : $_SERVER['REMOTE_ADDR']; 
    }

?>