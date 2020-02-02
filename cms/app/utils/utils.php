<?php

include("backup.php");

function checkLog(){
          $size = filesize("log.txt");
          $MB = 1048576;  // number of bytes in 1M
          $K64 = 65536;    // number of bytes in 64K
          $d = date("Y-m-d G");
          
          if($size>$MB){
             copy("log.txt","logs/log".$d.".txt");
	     $f = fopen("log.txt","w");
	     fwrite($f,"see logs/ for more info\n");  
	     fclose($f);
	  }
          deleteOld(15); // in days

}

function deleteOld($days){
       $folders = Array();
       $folders1 = Array();
       $d = opendir("logs");
       $i=0;
	$t = time();
       $period = $days*24*60*60;	
       while ($file = readdir($d))
       {       //echo $file."<br>".is_dir($file);
               if ($file == '.' || $file == '..') continue;
                    //echo stat($file);
                    
		    $folders[filemtime("logs/".$file)] = $file;
		    $folders1[$i] = filemtime("logs/".$file);
		    $i++;
               
	}
	sort($folders1,SORT_NUMERIC);
	//echo "$t---------<br>";
	//echo "$period---------<br>";

	while(list($k,$v) = each($folders1)){
	  //echo ($t - $v)." ";
	  //echo $v." ".$k." / ".$folders[$v]."<br>";
	  if(($t - $v)>$period){
	      //echo $v." ".$k." / ".$folders[$v]."<br>";
	      @unlink("logs/".$folders[$v]);
	  }
	}
        for($i=0;$i<(count($folders1)-2);$i++){
           //echo $folders[$folders1[$i]]."<br>";
           
	}
}

//deleteOld(24);
checkLog();
?>