<?php

ini_set('display_errors', true);error_reporting(E_ALL ^ E_NOTICE);

	//include('../config.php');


	$path = "../../".urldecode($_POST["path"]);
	$file = urldecode($_POST["txtfile"]);
	echo $path;
	
	$xml = '<?xml version="1.0" encoding="utf-8"?>'."\n";
	$xml .= '<cms><record>'."\n";

	$xml1 = '<?xml version="1.0" encoding="utf-8"?>'."\n";
	$xml1 .= '<cms><record>'."\n";
	
	        $cdata = trim($_POST['content']);	
	        $ctitle = trim($_POST['title']);	
	        $cdate = trim($_POST['date']);	
	        
		$cdata1 = str_replace("../data_cms/", "cms/data/", $cdata);

		$xml .= ""."".'<notes>'."";
		$xml .= "".""."".str_replace("&","&amp;",$ctitle)."";
		$xml .= ""."".'</notes>'."\n";
		$xml .= ""."".'<date>'."";
		$xml .= "".""."".$cdate."";
		$xml .= ""."".'</date>'."\n";
		$xml .= ""."".'<content>'."\n";
		$xml .= "".""."".'<![CDATA['."";
		$xml .= "".stripslashes($cdata) ."";
		$xml .= "".']]>'."\n";
		$xml .= ""."".'</content>'."\n";

		$xml1 .= ""."".'<notes>'."";
		$xml1 .= "".""."".$ctitle."";
		$xml1 .= ""."".'</notes>'."\n";
		$xml1 .= ""."".'<date>'."";
		$xml1 .= "".""."".$cdate."";
		$xml1 .= ""."".'</date>'."\n";
		$xml1 .= ""."".'<content>'."\n";
		$xml1 .= "".""."".'<![CDATA['."";
		$xml1 .= "".stripslashes($cdata1) ."";
		$xml1 .= "".']]>'."\n";
		$xml1 .= ""."".'</content>'."\n";


	
	$xml .= '</record></cms>';
	$xml1 .= '</record></cms>';
	
	$fp = fopen($path.$file.'_cms.xml', 'w');
	fclose($fp);
	file_put_contents($path.$file.'_cms.xml', $xml);

	$fp = fopen($path.'text.xml', 'w');
	fclose($fp);
	file_put_contents($path.'text.xml', $xml);

	$fp1 = fopen($path.$file.'.xml', 'w');
	fclose($fp1);
	file_put_contents($path.$file.'.xml', $xml1);

	$fp = fopen($path.'text.txt', 'w');
	fclose($fp);
	file_put_contents($path.'text.txt', $cdata1);
	
   // чтоб время папки обновилось
   fclose(fopen($path."_tmp","a")); 
   unlink($path."_tmp");
   //

	//echo '<script>alert(\'xml is saved in /data.xml\')</script>';
	//echo $xml; exit;
	
	
	
?>