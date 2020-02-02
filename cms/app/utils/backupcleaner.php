<?php
ini_set("display_errors","1");
error_reporting(1);

function clearGarbage(){
       $d = opendir("data_cms");
       $i=0;	
       while ($file = readdir($d))
       {       //echo $file."<br>".is_dir($file);
           if ($file == '.' || $file == '..' || $file == 'ajusted.jpg') continue;
           $type = substr($file,strlen($file)-3,3);
           //echo $file." ".$type."<br>";
           if(is_file("data_cms/".$file) && $type!="xml" && $type!="son" && $type!="txt"){
                //echo "remove "."data_cms/".$file."<br>";
                @unlink("data_cms/".$file);
	   }

       }
}
function deleteOldBK($leave_num){
         
       $folders = Array();
	$folders1 = Array();
        $path = "../../";	
       $d = opendir($path);
       $i=0;	
       while ($file = readdir($d))
       {       echo $file."<br>".is_dir($file);
               if ($file == '.' || $file == '..') continue;
	       

               if (is_dir($path.$file) && substr($file,0,4)=="data" && $file!="data" && $file!="data_cms" && $file!="data_last" && substr($file,0,9)!="dataError")
               {    //$folders[$i] = Array();
		    //$folders[$i]["name"] = $file;
		    //$folders[$i]["date"] = filemtime($file);
		    $folders[filemtime($file)] = $file;
		    $folders1[$i] = filemtime($file);
		    //echo $file." ".filemtime($file)."<br>";
		    $i++;
                    //continue;
               }
	}   
	sort($folders1,SORT_NUMERIC);
	//echo "---------<br>";
	//for($i=0;$i<count($folders);$i++){
	  //echo $folders[$i]["name"]." ".$folders[$i]["date"]."<br>";
 
	//}
	while(list($k,$v) = each($folders1)){
	  //echo $v." ".$k." ".$folders[$v]."<br>";
	}    
 
        if(count($folders1)>=$leave_num){
         $f = fopen("log.txt","a+");
           fwrite($f,"total backups=".count($folders1)." > ".$leave_num."\n");  
         for($i=0;$i<(count($folders1)-$leave_num);$i++){
          //echo $folders[$folders1[$i]]."<br>";
           fwrite($f,"delete folder ".$folders[$folders1[$i]]."\n");  

           delTree($path.$folders[$folders1[$i]]);
	  }
	  fclose($f);
	}
}

function delTree($dir) { 
   $files = array_diff(scandir($dir), array('.','..')); 
    foreach ($files as $file) { 
      (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file"); 
    } 
    return rmdir($dir); 
  } 

function advancedRmdir($path) { //mappat torol akkor is, ha nem ures
    //echo "delete ".$path."<br>";return;
    $origipath = $path;
    $handler = opendir($path);
    while (true) {
        $item = readdir($handler);
        if ($item == "." or $item == "..") {
            continue;
        } elseif (gettype($item) == "boolean") {
            closedir($handler);
            if (!@rmdir($path)) {
                return false;
            }
            if ($path == $origipath) {
                break;
            }
            $path = substr($path, 0, strrpos($path, "/"));
            $handler = opendir($path);
        } elseif (is_dir($path."/".$item)) {
            closedir($handler);
            $path = $path."/".$item;
            $handler = opendir($path);
        } else {
            unlink($path."/".$item);
        }
    }
    return true;
}


?>