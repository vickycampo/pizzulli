<?php
//sleep(1);
include("config.php");
require ("settings.php");
set_time_limit (360);

if (!DEVELOPING) {
    ini_set('display_errors', 0);error_reporting(0);
} else {
    ini_set('display_errors', true);error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
}

$time_debug = [];

function addTime($action){
   global $time_debug;
   $time_debug[] = ["action"=>$action, "time"=>date("i:s")];
}

addTime('start');

include("utils/checkuser.php");

$check = checkUser("working",$settings["logout_time"]);

addTime('user checked');

if (!$check["result"]) {
    $result['status'] = 'error';
    $result['error'] = $check["error"];
    echo json_encode($result);
    die;
}

//var_dump($check); die;

addTime('start session');



session_start();

addTime('end session');

if (!isset($_SESSION['login'])) {
    $result['status'] = 'error';
    $result['error'] = 'Not authorized'; 

    echo json_encode($result);
    die;
}

addTime('start load class');

spl_autoload_register(function ($class_name) {
    include 'classes/'.$class_name . '.class.php';
});

addTime('end load class');

$action = addslashes($_REQUEST["action"]);
$id = addslashes($_REQUEST["id"]);
$parent_id = addslashes($_POST["parent_id"]);

 // echo "action=".$action; die;

//TEST
$fromdir = true;
$action = 'regenerate';
$prefolder = '/images';
$id = '11';
$predir = 'PARTNERS/Angry Natives';
$_POST["target"] = 'thumbnails';
//TEST
     

                 
if (checkAction($action)) {
    $result['status'] = 'error';
    $result['error'] = "Illegal operation";
    echo json_encode($result);
    die;
}

function checkAction($action) 
{
    $actions = [];
    return in_array($action, $actions);
}

$cms_xml = new CMS_XML();

if ($cms_xml->arr_flat) {

   makeXmlBackup();

} else {
    $result['status'] = 'error';
    $result['error'] = "Data broken. Connect developer.";
    echo json_encode($result);
    die;
}

$filesystem = new FileSystem();

   


$result = array();
$result['status'] = 'ok';
$result['cms_path'] = CMS_PATH;

$path = $cms_xml->getPathString($id, array());
$parent_path = $cms_xml->getPathString($parent_id, array());

 

addTime('start log');

include("utils/log.php");

addTime('end log');

        

if($action == "cropImage"){

    $coords = $_POST["coords"];

    $name = $_POST["name"];

    $target = $_POST["target"];

    $images = new Images($settings);   	
    
    $res = $images->cropImage($path, $name, $target, $coords);
    
    if (!$res) {  

        $result['status'] = 'error';
        $result['error'] = "Images class: ".$images->error;   

    } else {
         
	$cms_xml = new CMS_XML();
        $result['file'] = $cms_xml->updateContent($id, $res, $name);
    
    }


}


if($action == "regenerate"){
    $target =  $_POST["target"];
    
    $list = $cms_xml->listContent($id);
    //var_dump($list);
  
  if ($fromdir){
      $dir = "../data_cms/".$predir.$prefolder;
      $list = [];
      $cdir = scandir($dir); 
      foreach ($cdir as $key => $value) 
      { 
         if (!in_array($value,array(".",".."))) 
         { 
            if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) 
            { 
            } 
            else 
            { 
               //echo $dir . DIRECTORY_SEPARATOR . $value;
               //echo "<br>";
               if (!file_exists($dir."/thumbnails_cms/".$value)){
                $list[] = ["name"=>$value];
               }
            } 
         } 
      } 
    //echo count($list); die;
   }
    $images = new Images($settings);   	

    foreach($list as $key=>$value){
        $res = $images->regenerate($value["name"], $path, $target);

        if ($res || 1) {
           echo $value["name"]." ".$res["name"]." ".$res["th"];
           //echo $value["name"]." ".$res;
           echo "<br>";
           $cms_xml->updateContent($id, $res, $value["name"]);
        }
    }
    $cms_xml->saveXml();

    return true;
}

if($action == "storeVideoSize"){

    $width = $_POST["width"];

    $height = $_POST["height"];

    $name = $_POST["name"];

    $result['file'] = $cms_xml->storeVideoSize($id, $name, $width, $height);


}
if($action == "screenshot"){
    $img = $_POST["imgBase64"];
    $img = str_replace('data:image/png;base64,', '', $img);
    $img = str_replace(' ', '+', $img);
    $data = base64_decode($img);
    $filename = $_POST["file"];
    $name = substr($filename,0,strrpos($filename, "."));   

    $success = file_put_contents("uploads/".$name.".png", $data);

    if (!$success) {
        $result['status'] = 'error';
        $result['error'] = 'Error saving screenshot';   
        return;
    }
    
    $images = new Images($settings);   	
    
    $res = $images->addScreenshot($name.".png", $path);

    if (!$res) {  

        $result['status'] = 'error';
        $result['error'] = "Images class: ".$images->error;   

    } else {
         
	$cms_xml = new CMS_XML();
        $result['file'] = $cms_xml->addScreenshot($id, $filename, $res);
    
    }

    @unlink("uploads/".$name.".png");
    
}

if($action == "updateNews"){

    $xml = $_POST["news_xml"];

    if ($filesystem->saveNewsFile($path, $xml)){

    } else {
        $result['status'] = 'error';
        $result['error'] = 'Error saving xml';   
    }

}

if($action == "updateFile"){
    $name = trim($_POST["name"]);
    $caption1 = trim($_POST["caption1"]);
    $caption2 = trim($_POST["caption2"]);
    $caption3 = trim($_POST["caption3"]);
    $caption4 = trim($_POST["caption4"]);

    $result['file'] = $cms_xml->updateFile($id, $name, $caption1, $caption2, $caption3, $caption4);
}

if($action == "moveFolderToPosition"){
    $old_pos = trim($_POST["old_position"]);
    $new_pos = trim($_POST["new_position"]);

    $aim = trim($_POST["aim"]);
    $target = trim($_POST["target"]);

    $res = $cms_xml->moveSection($parent_id, $aim, $target, $old_pos, $new_pos);

    if (!$res) {  
        $result['status'] = 'error';
        $result['error'] = "XML class: ".$cms_xml->error;   
    }
}

if($action == "moveFileToPosition"){
    $old_pos = trim($_POST["old_position"]);
    $new_pos = trim($_POST["new_position"]);

    $aim = trim($_POST["aim"]);
    $target = trim($_POST["target"]);

    $res = $cms_xml->moveFileToPos($id, $aim, $target, $old_pos, $new_pos);

    if (!$res) {  
        $result['status'] = 'error';
        $result['error'] = "XML class: ".$cms_xml->error;   
    }
}

if($action == "updateFolder"){
    $new_name = trim($_POST["name"]);
    $descr1 = trim($_POST["descr1"]);
    $descr2 = trim($_POST["descr2"]);
    $descr3 = trim($_POST["descr3"]);
    $descr4 = trim($_POST["descr4"]);

    $old_name = $cms_xml->getSectionName($id);

    if ($old_name != $new_name){
        $tmp = explode("/",$path);
        array_pop($tmp);
        array_push($tmp, $new_name);     
        $filesystem->renameSection($path, implode("/", $tmp));
    }

    $result['section'] = $cms_xml->updateSection($id, $new_name, $descr1, $descr2, $descr3, $descr4);


}
if($action == "createFolder"){

    $type = $_POST["type"];
    $name = $_POST["name"];

    if($cms_xml->existsSection($name, $parent_id)){

       $name = $name."_".rand(111,999);

    }

    $path = $cms_xml->getPathString($parent_id, array());

    $filesystem->addSection($path."/".$name);

    $result['section'] = $cms_xml->addSection($name, $type, $parent_id);

}



if($action == "getFolderId"){

   $name = $_POST["name"];

   $section = $cms_xml->getSectionId($name, $parent_id);

   if ($section === false){

        $result['status'] = 'error';
        $result['error'] = "Folder not found";   
   } else {
        $result['section'] = $section;
   }

}

if($action == "deleteFolder"){

    checkPath($path);

    $filesystem->deleteSection($path);

    $result['section'] = $cms_xml->deleteSection($id);

}

if($action == "deleteFiles"){

    $names = $_POST["names"];

    checkPath($path);
            
    $filesystem->deleteFiles($path, $names);

    $cms_xml->deleteFiles($id, $names);

}

if($action == "moveToFolder"){

    $to_id = $_POST["to_id"];

    $path_to = $cms_xml->getPathString($to_id, array());

    $files = $_POST["files"];

    $folders_ids = $_POST["folders"];

    $folders = [];

    foreach($folders_ids as $folder_id) {

        $folders[] = $cms_xml->getSectionName($folder_id);

    }

    checkPath($path);
            
    $filesystem->moveFilesToSection($path, $path_to, $files);

    $filesystem->moveFoldersToSection($path, $path_to, $folders);

    if ($cms_xml->moveToSection($id, $to_id, $files, $folders_ids)){

    } else {

        $result['status'] = 'error';
        $result['error'] = $cms_xml->error;   

    }

}

if($action == "replaceFile"){

  if (isset($_FILES['Filedata'])) {
    
    $file = (trim($_FILES['Filedata']['name']));

    $name = $_POST["name"];

    $res = move_uploaded_file($_FILES['Filedata']['tmp_name'], "uploads/" . $file);  
    
    if (!$res) {

        $result['status'] = 'error';
        $result['error'] = error_get_last();   


    } else {
                  
        $images = new Images($settings);   	
        
        $filesystem->deleteFiles($path, [$name]);

        $res = $images->replaceFile($file, $path);
        
        if (!$res) {  
        
            $result['status'] = 'error';
            $result['error'] = "Images class: ".$images->error;   
        
        } else {
             
	    $cms_xml = new CMS_XML();

            $result['file'] = $cms_xml->replaceContent($id, $res, $name);
        
        }
        
        @unlink("uploads/" . $file);
    }
  } else {
        $result['status'] = 'error';
        $result['error'] = 'No filedata';   

  }


}


if($action == "addFile"){
                          
  if (isset($_FILES['Filedata'])) {
    
    $file = (trim($_FILES['Filedata']['name']));

    $res = move_uploaded_file($_FILES['Filedata']['tmp_name'], "uploads/" . $file);
    
    if (!$res) {

        $result['status'] = 'error';
        $result['error'] = error_get_last();   


    } else {
                  
        $images = new Images($settings);   	
        
        $res = $images->addFile($file, $path);
        
        if (!$res) {  
        
            $result['status'] = 'error';
            $result['error'] = $images->error;   
        
        } else {
             
	$cms_xml = new CMS_XML();
            $result['file'] = $cms_xml->addFile($id, $res);
        
        }
        
        @unlink("uploads/" . $file);
    }
  } else {
        $result['status'] = 'error';
        $result['error'] = 'No filedata';   

  }
}

if($action == "onoffFolder"){

    $result['obj'] = $cms_xml->onoffSection($id);

}

if($action == "onoffFile"){

    $name = $_POST["name"];

    $result['obj'] = $cms_xml->onoffFile($id, $name);

}

if($action == "optionFile"){

    $name = $_POST["name"];

    $result['obj'] = $cms_xml->optionFile($id, $name);

}

if($action == "getNews"){

    $result['section'] = $cms_xml->getPathArray($id, array());
    $result['section'][] = ['name' => 'HOME', 'id' => -1];


    $path = $cms_xml->getPathString($id, array());

    $result['content'] = $filesystem->getNewsXml($path);


    $result['section'] = array_reverse($result['section']);

}


function countContent($id, $arr_flat)
{
    $count = 0;
    for($i=0;$i<count($arr_flat);$i++){
       
       if($arr_flat[$i]["parent_id"]==$id){
          $count += count($arr_flat[$i]["content"]);
       }

       if($arr_flat[$i]["id"]==$id){
          $count += count($arr_flat[$i]["content"]);
       }
    }
    return $count;
}

if($action == "getStructure"){

   $result['structure'] =  $cms_xml->arr_flat;

}

if($action == "getContent"){

    addTime('start action');

    $result['content'] = [];

    $result['section'] = $cms_xml->getPathArray($id, array());
    $result['section'][] = ['name' => 'HOME', 'id' => -1];


    $arr_flat = $cms_xml->arr_flat;
    //var_dump ($arr_flat);


    for($i=0;$i<count($arr_flat);$i++){
       //echo $i." ".$arr_flat[$i]["name"]." ".$arr_flat[$i]["parent_id"]."==".$id."<br>";
       if($arr_flat[$i]["parent_id"]==$id){
          $arr_flat[$i]["count"] = countContent($arr_flat[$i]["id"], $arr_flat);
          $arr_flat[$i]["content"] = [];
          $result['content'][] = $arr_flat[$i];
       } 
    }

    for($i=0;$i<count($arr_flat);$i++){
       if($arr_flat[$i]["id"]==$id){
          for($j=0;$j<count($arr_flat[$i]["content"]);$j++){
              $result['content'][] = $arr_flat[$i]["content"][$j];
          }
       } 
    }

    
    $result['section'] = array_reverse($result['section']);

    addTime('end action');

}


addTime('end');
$result['time_debug'] = $time_debug;

$result['action'] = $action;

echo json_encode($result);


//==========================================

function showPath($path){
   for ($i=0;$i<count($path);$i++){
       echo $path[$i]["name"]."/";
   }
   echo "<hr>";
}

function checkPath($path) {

    $result = [];
 
    if ($path === false || is_null($path)) {

        $result['status'] = 'error';
        $result['error'] = 'No such folder'; 

        echo json_encode($result);

        die;

        
    } else if ($path == CMS_PATH) {

        $result['status'] = 'error';
        $result['error'] = 'Trying to delete data_cms';

        echo json_encode($result);
 
        die;
    }


}

function makeXmlBackup(){
   
   if(!file_exists("xml_backups")){
      mkdir("xml_backups",0755);
   }
   copy("../data_cms/info.xml","xml_backups/".date("d M G").".xml");

}