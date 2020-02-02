<?php
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
echo encodeName('BOB/BILL 19');
echo chr(0)." ".chr(9);
function encodeName($nm){
 $z = explode("",$nm);
 for($i=1;$i<count($z);$i++){
     echo $z[$i]." ".chr($z[$i])."<br>";
 } 
}

function decode($nm){
	$nm1="";
	$z = explode("[",$nm);
	$nm1 = $z[0];
	$w;
	for($i=1;$i<count($z);$i++){
		$w = explode("]",$z[$i]);
		$nm1 .=  chr($w[0]).$w[1];
	}
	return $nm1;
}
?>