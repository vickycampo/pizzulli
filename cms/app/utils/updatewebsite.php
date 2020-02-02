<?php
//echo "start ".date("H:i:s")."<br>";

include("../config.php");
include("../settings.php");

session_start();

ini_set("max_execution_time", 0);

include("backupfuncs.php");
include("backupcleaner.php");


$settings = getSettings();

$news = [];

//include("checkuser.php");

//checkUser("working",$settings["logout_time"]);


if (!isset($_SESSION['login'])) {
    die;
}

spl_autoload_register(function ($class_name) {
    include '../classes/' . $class_name . '.class.php';
});


$t = microtime();
$f = fopen("log.txt","a+");
$mtimes = file("update_report.txt");
$fr = fopen("update_report.txt","w");

for($i=0;$i<count($mtimes);$i++){
  $mt = explode("|||",$mtimes[$i]);
  //echo $mt[0]." ".$mt[1]."-----------<br>";
  $folders_modtime[$mt[0]] = $mt[1];
}

fwrite($f,date("H:i:s")." ".$_SERVER["PHP_SELF"]."\n");  
fwrite($f,$_SERVER["HTTP_USER_AGENT"]."\n");  
$action = $_POST["action"];

$folder_id = $_POST["folder_id"];

//$folder_id = 'c3c2800790e8a4b8c905a85c222b3a7d';$action = 'update';//TEST

$path = "";
if ($folder_id!=-1){
    $cms_xml = new CMS_XML();
    $path = $cms_xml->getPathString($folder_id, array());
}
//echo $path; die;

if(!$action){
  $action = $_GET["action"];
}
fwrite($f,"action=".$action."\n");  

fwrite($f,"start time = ".date("H:i:s")."\n");  
$t = microtime();

$needreload = "no";

//$action = "update"; //debug

if($action=="update"){
  if(!is_dir("../../data")){mkdir("../../data");}

  if ($path!=''){
      $tmp = (explode("/data_cms/", $path));
      $path1 = $tmp[1];
      compare_folders("../../data_cms/".$path1,"../../data/".$path1);
      compare_files("../../data_cms/".$path1,"../../data/".$path1);
      compare_files("../../data_cms","../../data");
  } else {
      compare_folders("../../data_cms","../../data");
      compare_files("../../data_cms","../../data");
  }

   $result = 1;

  
}else if($action=="undo"){

  $needreload = "yes";
  compare_folders("../../data","../../data_cms");
  compare_files("../../data","../../data_cms");
  

   $result = 1;
}

 //echo "end ".date("H:i:s")."<br>";
 fwrite($f,"end time = ".date("H:i:s")."\n");  

 if($result){
   $answ = "a=1&phpresult=ok&needreload=".$needreload."&action=".$action."&nocache=".time()."&b=1";
   fwrite($f,$answ."\n");
   echo $answ;
 }else{
   fwrite($f,"a=1&phpresult=error&b=1"."\n");
   echo "a=1&phpresult=error&nocache=".time()."&b=1";
 }
fclose($f);
fclose($fr);

$subfolder = explode('/cms/',$_SERVER["REQUEST_URI"]);
$folder = '';
if($subfolder[0]!=''){
  $folder = $subfolder[0]."/";
}
$update_url = $settings["run_script_during_update_action"];
//$update_url = str_replace('../','http://'.$_SERVER['SERVER_NAME']."/".$folder,$update_url);
//echo $update_url."<br><br>";
file_get_contents($update_url);


function addTime($action){
}


//-------------------------------
function compare_folders($src,$dest){
  global $fr;
  global $folders_modtime;
  global $news;

  $folders_src = listDir($src);
  $folders_dest = listDir($dest);
  // удалить отсу папки
  for($i=0;$i<count($folders_dest);$i++){
    $f = $folders_dest[$i];
    if(!in_array($f,$folders_src)){
       //echo "<b>remove</b> ".$dest."/".$f."<br>";
       @advancedRmdir($dest."/".$f);
    }
  }
  //читаем опять
  $folders_dest = listDir($dest);

  for($i=0;$i<count($folders_src);$i++){
    $f = $folders_src[$i];
    if(!in_array($f,$folders_dest)){
       // папки нет, добовляем
       //echo "<b>add</b> ".$dest."/".$f."<br>";
       copydata($src."/".$f,$dest."/".$f);
    }else{
      
      //папка есть, сравниваем
      $currtime = filemtime($src."/".$f);
      //echo $folders_modtime[$src."/".$f]."!=".$currtime."<br>";
      //echo $f." ".in_array($f,$news)."<br>";

      if($folders_modtime[$src."/".$f]!=$currtime){
         //echo "<b>compare</b> ".$src."/".$f."<br>";
         compare_files($src."/".$f,$dest."/".$f);
       
      }else if (in_array($f,$news)){
         //echo $f."<br>";
         compare_files($src."/".$f,$dest."/".$f);
      }

      $currtime = filemtime($src."/".$f);
      fwrite($fr,$src."/".$f."|||".$currtime."\n");
      compare_folders($src."/".$f,$dest."/".$f);

    }
  }
   //echo $src."<br>";
   return true;
}
function compare_files($src,$dest){
  $folders_src = listDirF($src);
  $folders_dest = listDirF($dest);
  // удалить отсу файлы
  for($i=0;$i<count($folders_dest);$i++){
    $f = $folders_dest[$i];
    if(!in_array($f,$folders_src)){
       //echo "<b>remove file </b> ".$dest."/".$f."<br>";
       @unlink($dest."/".$f);
    }
  }
  //читаем опять
  $folders_dest = listDirF($dest);

  for($i=0;$i<count($folders_src);$i++){
    $f = $folders_src[$i];
    if(!in_array($f,$folders_dest)){
       // файла нет, добовляем
       //echo "<b>add file</b> ".$dest."/".$f."<br>";
       copydata($src."/".$f,$dest."/".$f);
    }else{
      //файл есть, сравниваем
      $f1s = filesize($src."/".$f);
      $f2s = filesize($dest."/".$f);
      $update = false;

      if($f1s!=$f2s){
        $update = true;
      }else{
        $f1t = filemtime($src."/".$f);
        $f2t = filemtime($dest."/".$f);
        if($f1t>$f2t){
         //echo $f1t."-".$f2t."<br>";
          $update = true;
        }
      }
      if($update){

        //echo "<b>update file</b> ".$dest."/".$f."<br>";
        copy($src."/".$f,$dest."/".$f);
      }
    }
  }

}
function listDir($dir)
{
       $i = 0;
       $folders = Array();
       $d = opendir($dir);
       while ($file = readdir($d))
       {       
               if ($file == '.' || $file == '..' || $file == 'javaUpload') continue;
               if (is_dir($dir.'/'.$file))
               {    
		    $folders[$i++] = $file;
		    
                    continue;
               }
		}
	return 	$folders;
}
function listDirF($dir)
{
       $i = 0;
       $files = Array();
       $d = opendir($dir);
       while ($file = readdir($d))
       {       
               if ($file == '.' || $file == '..') continue;
               if (is_file($dir.'/'.$file))
               {    
		    $files[$i++] = $file;
                    continue;
               }
		}
	return 	$files;
}

?>	