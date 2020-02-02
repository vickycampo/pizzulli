<?php

set_time_limit(0);
ini_set("display_errors","1");
error_reporting(1);
ini_set('memory_limit', '1024M' );

include("backupfuncs.php");

include("backupcleaner.php");

include("../settings.php");

$settings = getSettings();

$bk_count = $settings["backup_count"];

function checkBackup($bk_count){
 if($bk_count==0){echo "0 backups for that system"; return;}

 echo "Backup count :".$bk_count."<br>";
 $updateBackup = false;
 $fp = fopen("backuptime.txt","r"); 
 $data = fread($fp,20);
 fclose($fp);

 $data1 = date("d");

 echo $data."<br>";
 echo $data1."<br>";

 if ($data!=$data1){	
  $updateBackup = true;
 }


 if ($updateBackup){
  clearGarbage();


   $dir = "../../data_cms";
   $bk = "../../data".date("dMy");  

  @copydata($dir,$bk);
  $fp = fopen("backuptime.txt","w"); 
  fwrite($fp,date("d"));
  fclose($fp);
  $fp = fopen("backupname.txt","w"); 
  fwrite($fp,$bk);
  fclose($fp);
 }else{
  //echo "backup already done";
 }

}



checkBackup($bk_count);
deleteOldBK($bk_count);

?>
