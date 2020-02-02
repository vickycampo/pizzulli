<?php
 $lockError = "The CMS is being used by someone else<br>{geo}<br>Please try again later.";

include("geoip.php");

function checkUser($mode,$period){
    
 global $lockError; 
 $int = 60*$period;//60*3; //15 min
 $userok = false;
 $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];  
 //if(!file_exists("currentuser.txt")){}
 addTime('open currentuser.txt');
 $fp = fopen("utils/currentuser.txt","a+"); 
 $data = fread($fp,100);
 //echo $data; die;

 $c = strlen($data);
 $tm = time();

 $data = explode("|",$data);
 fclose($fp);
 addTime('close currentuser.txt');

 if($c==0){
  $userok = true;
 }else{
  $t = time();
  $dt = $t - $data[1];
  //echo $dt." ".$int; die;
  if($data[0]==$ip){
  
   if($dt<=$int){
     $userok = true;
   }else{
     //echo $mode=="start";
     if($mode=="start"){
       $userok = true;
     }else{
       $userok = true;
       //$userok = false;
       $lockError = str_replace("",$data[0],$lockError);

     }
   }
  
  }else{ //ip
   if($dt>$int){
     $userok = true;
   }else{
     $userok = false;
     $lockError = str_replace("{ip}"," (".$data[0].")",$lockError);
   }   
  }
 }//$c==0

 if($mode=="working"){
      //echo $userok;

 	if($userok){
  		lockCms($mode);
	}else{
                addTime('open checkuserlog.txt');

		$f = fopen("utils/checkuserlog.txt","a+");
		fwrite($f,date("H:i:s")." ".$_SERVER['HTTP_CF_CONNECTING_IP']." error"."\n");  
		fclose($f);  
                addTime('close checkuserlog.txt');

	}
 }

 addTime('start geo');
 $lockError = str_replace("{geo}",getLocationByIP($data[0]),$lockError);
 addTime('end geo');


 return ["result" => $userok, "error" => $lockError];
}

 function lockCms($mode){       
 	$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];  

        addTime('start lock');
 	$fp = fopen("utils/currentuser.txt","w"); 
 	//$data = fread($fp,20);
 	$tm = time();
 	fwrite($fp,$ip."|".$tm);

 	fclose($fp);

	if(file_exists("utils/checkuserlog.txt") && filesize("utils/checkuserlog.txt")>2048){
          unlink("utils/checkuserlog.txt");
	}
	if($mode=="start"){
	   $a = " log in";
        }
	$f = fopen("utils/checkuserlog.txt","a+");
	fwrite($f,date("H:i:s")." ".$_SERVER['HTTP_CF_CONNECTING_IP'].$a."\n");  
	fclose($f);  
        addTime('end lock');
	
 	return true;
 }
 function unlockCms(){       
 	$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];  

 	$fp = fopen("utils/currentuser.txt","w"); 
 	//$data = fread($fp,20);
 	$tm = 0;
 	fwrite($fp,$ip."|".$tm);

 	fclose($fp);

	$f = fopen("utils/checkuserlog.txt","a+");
	fwrite($f,date("H:i:s")." ".$_SERVER['HTTP_CF_CONNECTING_IP']." log out\n");  
	fclose($f);  

 	return true;
 }

?>