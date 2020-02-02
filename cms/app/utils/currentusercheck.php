<?php
 $fp = fopen("currentuser.txt","a+"); 
 $data = fread($fp,100);
 
 $c = strlen($data);
 $tm = time();

 $data = explode("|",$data);

 fclose($fp);
 $d = time();
 $dt = $d - $data[1];
 $m = round($dt/60);
 //echo $d." ".$date[1];
 
 echo "User IP: ".$data[0]."<br>";
 echo "Last action: ".date('Y-m-d H:i:s',$data[1])." ($m minutes ago)<br>";
 echo 'Now:       '. date('Y-m-d H:i:s') ."<br>";
?>