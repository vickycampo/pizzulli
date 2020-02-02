<?php
 $settings = getSettings();

$allowed_alt = explode(" ",$settings["allowed_alt_vd"]);

//$GLOBALS["xml_arr"];
//$xml_arr = Array();
/*
$f = fopen("data_cms/info.xml","r");
$e = fread($f,sizeof($f));
echo strpos($e,"\r")."<br>";
fclose($f); 
exit;
*/
$v = explode(".",phpversion());

if(intval($v[0])<5){
  echo "phpresult=error&descr=CMS requires PHP5!\nCURRENT PHP VERSION IS ".implode(".",$v)."&errorCode=0";
  exit;
}

$sxe = simplexml_load_file("data_cms/info.xml");

$xml_arr = parseXml($sxe,"");

//echo $xml_arr;

//$action="addsection";
//$param="HTML SEARCH ENGINE KEYWORDS/m";
//$details="description=|status=1|type=portfolio|name=m";
//$delimiter=",,";

//$action="deletesection";
//$param="PEOPLE/girl1/subgirl1";
//$details="";

//$action="renamesection";
//$param="PEOPLE/girl1/subgirl1,,PEOPLE/girl1/subgirl1-g";
//$details="";

//$action="savedescription";
//$param="PEOPLE";
//$details="people";

//$action="onoffsection";
//$param="PEOPLE";
//$details="";

//$action="movesection";
//$param="PEOPLE/girl1";
//$details="id=0|newid=1";

//$action="addContent";
//$param="bob,,0";
//$details="01_1833.jpg,,01_1833.jpg,,254,,140,,254,,140|04_3013.jpg,,04_3013.jpg,,108,,140,,108,,140";
//$delimiter=",,";

//$action="renamethumb";
//$param="bob,,009_pics1_6050.jpg,,009_pic_6050.jpg";
//$details="";

 /*
$action="addContent";

$param="test/p1,,0";

$details="07_dontfeed_61746_3110.jpg,,07_dontfeed_61746_3110.jpg,,115,,150,,383,,500";


$param = "PEOPLE/girl1";
$action="infoflags";
$details="0|1";
$delimiter=",,";
*/
/*
include("settings.php");

//modSection($action,$param,$details,$delimiter);

$xml = buildXml($xml_arr,"\t","data_cms");
$f = fopen("data_cms/test2.xml","w");
fwrite($f,"<sections>\n".$xml[2]."</sections>");
fclose($f);
*/ 

function unique_id($l = 8) {
    return substr(md5(uniqid(mt_rand(), true)), 0, $l);
}

//--------------------------------------------
function modSection($action,$path,$details,$delimiter,$result_data){
   $settings = getSettings();
   $res = true;
   $fld = explode($delimiter,$path);
   if(count($fld)==1){
     $level = explode("/",$path);
   }else{
     $level = explode("/",$fld[0]);
   }
   $tmp = explode("!|",$details);

   for($i=0;$i<count($tmp);$i++){
     $kv = explode("=",$tmp[$i]);
     //$sec[$kv[0]] = $kv[1];

     $key = $kv[0];
     if(count($kv)>2){
       array_splice($kv,0,1);
       $kk = implode("=",$kv);        
     }else{
       $kk = $kv[1];
     }
     
     $sec[$key] = $kk;

   }
   for($i=0;$i<count($tmp);$i++){
     $kv = explode($delimiter,$tmp[$i]);
     $content[$i] = $kv;
   }
   $sec["sections"] = Array();
   $sec["content"] = Array();

   $link = &$GLOBALS["xml_arr"];
   $link1 = &$GLOBALS["xml_arr"];
   $gc = count($link);

   for($i=0;$i<count($level)-1;$i++){
      $nm = $level[$i];  
      $l = count($link);
      
      for($j=0;$j<$l;$j++){
        //echo $link[$j]["name"]."-".$nm."<br>";
        if($link[$j]["name"]==$nm) {
         $ind = $j;        
         $link = &$link[$j]["sections"];    
         $j = $l;
        }     
      }
   }
   for($i=0;$i<count($level)-3;$i++){
      $nm = $level[$i];  
      $l = count($link1);
      
      for($j=0;$j<$l;$j++){
        //echo $link1[$j]["name"]."-".$nm."<br>";
        if($link1[$j]["name"]==$nm) {
         $ind = $j;        
         $link1 = &$link1[$j]["sections"];    
         $j = $l;
        }     
      }
   }
   
   $l = count($link);
   $l1 = count($link1);

   $nm = $level[count($level)-1];

   $small_vert_image_height = $settings["small_vert_image_height"];
   if($small_vert_image_height==""){$small_vert_image_height = 0;}

   $large_vert_image_height = $settings["large_vert_image_height"];
   if($large_vert_image_height==""){$large_vert_image_height = 0;}
   
  if($action=="addsection"){
    $link[$l]["id"] = unique_id(32);
    $link[$l]["name"] = $sec["name"];
    $link[$l]["type"] = $sec["type"];
    $link[$l]["status"] = $sec["status"];
    $link[$l]["description"] = $sec["description"];

  }else if($action=="deletesection"){ 
    for($i=0;$i<count($link);$i++){
     if($link[$i]["name"]==$nm){
       $todel = $i;
       $i = count($link);
     }
    }
    array_splice($link,$todel,1);
  }else if($action=="renamesection"){
    $tmp = explode("/",$fld[1]);
    $newname = trim($tmp[count($tmp)-1]);

    for($i=0;$i<count($link);$i++){
     if($link[$i]["name"]==$nm){
       $link[$i]["name"] = $newname;
       $i = count($link);
     }
    }
  }else if($action=="infoflags"){

    for($i=0;$i<count($link);$i++){
     if($link[$i]["name"]==$nm){
       //echo $link[$i]["info"]."<br>";
       //echo $tmp[0]." ".$tmp[1]."<br>"; 
       $info = explode(",",$link[$i]["info"]);
       $info[$tmp[0]] = $tmp[1];
       //echo implode(",",$info); 
       $link[$i]["info"] = implode(",",$info);
       $i = count($link);
     }
    }   
  }else if($action=="savedescription"){
    for($i=0;$i<count($link);$i++){
     if($link[$i]["name"]==$nm){

       $link[$i]["description"] = str_replace("\n","___",$sec["descr"]);
       $i = count($link);
     }
    }   
  }else if($action=="savedescription2"){
    for($i=0;$i<count($link);$i++){
     if($link[$i]["name"]==$nm){

       $link[$i]["description2"] = str_replace("\n","___",$sec["descr"]);
       $i = count($link);
     }
    }   
  }else if($action=="onoffsection"){
    for($i=0;$i<count($link);$i++){
     if($link[$i]["name"]==$nm){
       //echo $link[$i]["status"];
 
       $link[$i]["status"] = 1-$link[$i]["status"];
       $i = count($link);
     }
    }   
  }else if($action=="movesection"){
    $tmp_arr = $link[$sec["id"]];
    //echo $sec["id"]."->".$sec["newid"]."<br>";
    array_splice($link,$sec["id"],1);
    //$l = count($link);
    array_splice($link,$sec["newid"],0,"");
    //for($i=$l;$i>$sec["newid"];$i--){
      //$link[$i] = $link[$i-1];
    //}
    $link[$sec["newid"]] = $tmp_arr;

  }else if($action=="moveContent"){

    for($i=0;$i<count($link);$i++){
     if($link[$i]["name"]==$nm){
       $tosec = $i;
       $i = count($link);
     }
    }
    $tmp_arr = $link[$tosec]["content"][$sec["id"]];
    array_splice($link[$tosec]["content"],$sec["id"],1);
    array_splice($link[$tosec]["content"],$sec["newid"],0,"");
    $link[$tosec]["content"][$sec["newid"]] = $tmp_arr;

  }else if($action=="movesectioninside"){
    $nm1 = $sec["newsec"];
     
    for($i=0;$i<count($link);$i++){
     if($link[$i]["name"]==$nm){
       $fromsec = $i;
       //$i = count($link);
     }
     if($link[$i]["name"]==$nm1){
       $tosec = $i;
       //$i1 = count($link);
     }
    }
    $arr["name"] = "nnn";
    $arr["type"] = "P";
    $arr["status"] = "1";
    $arr["description"] = "";

    //echo $fromsec."->".$tosec."<br>";  
    //echo count($link[$tosec]["sections"])."<br>";
    //echo $link[$fromsec]["name"]."<br>";       //$link[$fromsec]
    $c = count($link[$tosec]["sections"]);
 
    $link[$tosec]["sections"][$c] = $link[$fromsec];
    //array_splice($link[$tosec]["sections"],-1,0,$arr);
    array_splice($link,$fromsec,1);


  }else if($action=="movesectionoutside"){
    $nm1 = $sec["newsec"];
     
    for($i=0;$i<count($link);$i++){
     if($link[$i]["name"]==$nm){
       $fromsec = $i;
       //$i = count($link);
     }
    }
    //echo count($link);
    for($i=0;$i<count($link1);$i++){
    //echo $link1[$i]["name"];
    //echo "<br>";
     if($link1[$i]["name"]==$nm1){
       $tosec = $i;
       //$i1 = count($link);
     }
    }

   if (count($level)<3){
      // into root
    //echo $fromsec."->"."root"."<br>";  
    
    $link1[$l1] = $link[$fromsec];

   }else{

    //echo $fromsec."->".$tosec."<br>";  
    $c = count($link1[$tosec]["sections"]);
    $link1[$tosec]["sections"][$c] = $link[$fromsec];
   }
    array_splice($link,$fromsec,1);
    


  }else if($action=="moveContentToSection"){
    $nm1 = $sec["newfld"];

    for($i=0;$i<count($link);$i++){
     if($link[$i]["name"]==$nm){
       $fromsec = $i;
       //$i = count($link);
     }
     if($link[$i]["name"]==$nm1){
       $tosec = $i;
       //$i1 = count($link);
     }
    }
    //echo $fromsec." ".$tosec;
    if(!is_array($link[$tosec]["content"])){
        $link[$tosec]["content"] = Array();

    }

    $tmp_arr = $link[$fromsec]["content"][$sec["id"]];
    array_splice($link[$fromsec]["content"],$sec["id"],1);
    array_splice($link[$tosec]["content"],$sec["newid"],0,"");
    $link[$tosec]["content"][$sec["newid"]] = $tmp_arr;

  }else if($action=="renamethumb"){
   
    for($i=0;$i<count($link);$i++){
     if($link[$i]["name"]==$nm){
       $tosec = $i;
       $i = count($link);
     }
    }
    for($i=0;$i<count($link[$tosec]["content"]);$i++){
        //echo $link[$tosec]["content"][$i]["thmbname"]."==".$fld[1]."<br>"; 
       if($link[$tosec]["content"][$i]["thmbname"]==$fld[1]){
          
          $link[$tosec]["content"][$i]["thmbname"]=$fld[2];
       } 
    }
  }else if($action=="setVideoSize"){
   
    for($i=0;$i<count($link);$i++){
     if($link[$i]["name"]==$nm){
       $tosec = $i;
       $i = count($link);
     }
    }
    for($i=0;$i<count($link[$tosec]["content"]);$i++){ 
       if($link[$tosec]["content"][$i]["name"]==$sec["filename"]){ 
          $link[$tosec]["content"][$i]["iw"]=($sec["iw"]);
          $link[$tosec]["content"][$i]["ih"]=($sec["ih"]);
       } 
    }

  }else if($action=="saveNewName"){    
    for($i=0;$i<count($link);$i++){
     if($link[$i]["name"]==$nm){
       $tosec = $i;
       $i = count($link);
     }
    }
    for($i=0;$i<count($link[$tosec]["content"]);$i++){ 
       if($link[$tosec]["content"][$i]["name"]==$sec["oldname"]){ 
          $link[$tosec]["content"][$i]["name"]=($sec["newname"]);
          $link[$tosec]["content"][$i]["thmbname"]=($sec["tnewname"]);
       } 
    }

  }else if($action=="saveCaption"){
   
    for($i=0;$i<count($link);$i++){
     if($link[$i]["name"]==$nm){
       $tosec = $i;
       $i = count($link);
     }
    }
    for($i=0;$i<count($link[$tosec]["content"]);$i++){ 
       if($link[$tosec]["content"][$i]["name"]==$sec["filename"]){ 
          $link[$tosec]["content"][$i]["caption"]=($sec["caption"]);
       } 
    }
  }else if($action=="saveCaption2"){
   
    for($i=0;$i<count($link);$i++){
     if($link[$i]["name"]==$nm){
       $tosec = $i;
       $i = count($link);
     }
    }
    for($i=0;$i<count($link[$tosec]["content"]);$i++){ 
       if($link[$tosec]["content"][$i]["name"]==$sec["filename"]){ 
          $link[$tosec]["content"][$i]["caption2"]=$sec["caption"];
       } 
    }
  }else if($action=="updateThumb"){
   
    for($i=0;$i<count($link);$i++){
     if($link[$i]["name"]==$nm){
       $tosec = $i;
       $i = count($link);
     }
    }
    for($i=0;$i<count($link[$tosec]["content"]);$i++){ 
       if($link[$tosec]["content"][$i]["name"]==$sec["filename"]){ 
          $link[$tosec]["content"][$i]["tw"]=$sec["tw"];
          $link[$tosec]["content"][$i]["th"]=$sec["th"];
       } 
    }
  }else if($action=="updateImage"){
   
    for($i=0;$i<count($link);$i++){
     if($link[$i]["name"]==$nm){
       $tosec = $i;
       $i = count($link);
     }
    }
    for($i=0;$i<count($link[$tosec]["content"]);$i++){ 
       if($link[$tosec]["content"][$i]["name"]==$sec["filename"]){ 
          $link[$tosec]["content"][$i]["iw"]=$sec["iw"];
          $link[$tosec]["content"][$i]["ih"]=$sec["ih"];
          $link[$tosec]["content"][$i]["cropped"]=$sec["cropped"];
          if($small_vert_image_height!=0){
             $iratio = $settings["vert_image_height"]/$small_vert_image_height;
             $link[$tosec]["content"][$i]["sw"]=$sec["sw"];
             $link[$tosec]["content"][$i]["sh"]=$sec["sh"];

          }
       } 
    }
  }else if($action=="updateVideo"){
   
    for($i=0;$i<count($link);$i++){
     if($link[$i]["name"]==$nm){
       $tosec = $i;
       $i = count($link);
     }
    }
    for($i=0;$i<count($link[$tosec]["content"]);$i++){ 
       if($link[$tosec]["content"][$i]["name"]==$sec["oldname"]){ 
          $link[$tosec]["content"][$i]["name"]=$sec["filename"];
       } 
    }
  }else if($action=="setOption"){
   
    for($i=0;$i<count($link);$i++){
     if($link[$i]["name"]==$nm){
       $tosec = $i;
       $i = count($link);
     }
    }
    for($i=0;$i<count($link[$tosec]["content"]);$i++){ 
       if($link[$tosec]["content"][$i]["name"]==$sec["filename"]){ 
          $link[$tosec]["content"][$i]["option"]=$sec["option"];
       } 
    }
  }else if($action=="setStatus"){
   
    for($i=0;$i<count($link);$i++){
     if($link[$i]["name"]==$nm){
       $tosec = $i;
       $i = count($link);
     }
    }
    for($i=0;$i<count($link[$tosec]["content"]);$i++){ 
       if($link[$tosec]["content"][$i]["name"]==$sec["filename"]){ 
          $link[$tosec]["content"][$i]["status"]=$sec["status"];
       } 
    }
  }else if($action=="deletefiles"){

  }else if($action=="clearsection"){
    
    for($i=0;$i<count($link);$i++){
     if($link[$i]["name"]==$nm){
       $link[$i]["content"] = Array();
       $i = count($link);
     }
    }

  }else if($action=="joinfiles"){

    for($i=0;$i<count($link);$i++){
     if($link[$i]["name"]==$nm){
       $tosec = $i;
       $i = count($link);
     }
    }
    for($i=0;$i<count($link[$tosec]["content"]);$i++){ 
       if($link[$tosec]["content"][$i]["name"]==$fld[2]){
          $pic1_id = $i; 
       } 
       if($link[$tosec]["content"][$i]["name"]==$fld[4]){
          $pic2_id = $i; 
       } 
    }
    $link[$tosec]["content"][$pic2_id]["caption"] = $link[$tosec]["content"][$pic1_id]["caption"];
    list($width1, $height1) = getimagesize("data_cms/".$fld[0]."/thumbnails/".$fld[3]);
    list($width2, $height2) = getimagesize("data_cms/".$fld[0]."/images/".$fld[4]);
    
    if($settings["join_thmbs"]=="1" || $settings["join_thmbs"]=="on"){
	$link[$tosec]["content"][$pic2_id]["tw"] = $result_data["tw"];
	$link[$tosec]["content"][$pic2_id]["th"] = $result_data["th"];
    }
    $link[$tosec]["content"][$pic2_id]["iw"] = $result_data["iw"];
    $link[$tosec]["content"][$pic2_id]["ih"] = $result_data["ih"];


          if($small_vert_image_height!=0){
             $iratio = $settings["vert_image_height"]/$small_vert_image_height;
    		$link[$tosec]["content"][$pic2_id]["sw"] = $result_data["siw"];
    		$link[$tosec]["content"][$pic2_id]["sh"] = $result_data["sih"];

          }
    array_splice($link[$tosec]["content"],$pic1_id,1);

  }else if($action=="removeContent"){
    $ind = $fld[1];
    for($i=0;$i<count($link);$i++){
     if($link[$i]["name"]==$nm){
       $tosec = $i;
       $i = count($link);
     }
    }
    for($i=0;$i<count($link[$tosec]["content"]);$i++){
       if($link[$tosec]["content"][$i]["name"]==$sec["filename"]){
         /*
         $delstring = $fld[0].",,"."/".$link[$tosec]["content"][$i]["name"];
         $delstring .= ",,"."/images/".$link[$tosec]["content"][$i]["name"];
         $delstring .= ",,"."/smallimages/".$link[$tosec]["content"][$i]["name"];
         $delstring .= ",,"."/thumbnails/".$link[$tosec]["content"][$i]["thmbname"];
         $delstring .= ",,"."/thumbnails140/".$link[$tosec]["content"][$i]["thmbname"];
         $delstring .= ",,"."/smallthumbnails/".$link[$tosec]["content"][$i]["thmbname"];
         //$delstring .= ",,"."/smallthumbnails/".$link[$tosec]["content"][$i]["thmbname"];
         */
         $dname = $link[$tosec]["content"][$i]["name"];
         $todel = $i;
         $i = count($link[$tosec]["content"]);
       }
    }
    if($ind==$todel){
     deleteFiles($fld[0],$dname);
     array_splice($link[$tosec]["content"],$ind,1);
    }else{
      $res = false;
    }
  }else if($action=="addContent"){
    //<content status="1" name="06_dontfeed_85102_2113.jpg" thmbname="06_dontfeed_85102_2113.jpg" tw="91" th="150" iw="450.7614213198" ih="740" option="1" caption=""/>

    $ind = $fld[1];
    for($i=0;$i<count($link);$i++){
     if($link[$i]["name"]==$nm){
       $toinsert = $i;
       $i = count($link);
     }
    }
    //echo "-".!is_array($link[$toinsert]["content"]);
    if(!is_array($link[$toinsert]["content"])){
        $link[$toinsert]["content"] = Array();

    }
    //echo "-".!is_array($link[$toinsert]["content"]);
    //echo "-".count($link[$toinsert]["content"])."<br>";
    for($i=0;$i<count($content);$i++){
     $arr = Array();
     $arr["status"] = "1";
     $arr["name"] = $content[$i][1];
     $arr["thmbname"] = $content[$i][0];
     $arr["tw"] = $content[$i][2];
     $arr["th"] = $content[$i][3];
     $arr["iw"] = $content[$i][4];
     $arr["ih"] = $content[$i][5];

     if($small_vert_image_height!=0){
       $arr["sw"] = $content[$i][7];
       $arr["sh"] = $content[$i][8];
     }

     if($large_vert_image_height!=0){

       if(file_exists("data_cms/".$fld[0]."/largeimages/".$content[$i][0])){
         list($width, $height) = getimagesize("data_cms/".$fld[0]."/largeimages/".$content[$i][0]);
         $arr["liw"] = $width;
         $arr["lih"] = $height;
       }
     }

     $arr["option"] = "0";
     $arr["caption"] = $content[$i][6];
     $arr["cropped"] = "0";
     //echo $arr["name"]."-".($ind+$i)."<br>";
 
     array_splice($link[$toinsert]["content"],$ind+$i,0,"");
     $link[$toinsert]["content"][$ind+$i]=$arr;
    }
    //echo "-".count($link[$toinsert]["content"])."<br>";

  }
  
  return $res;
}
//--------------------------------------------
function escape($pstr){
  $str = str_replace("&amp;","&",$pstr);
  $str = str_replace("&apos;","'",$str);
  $str = str_replace("&quot;","\"",$str);

  return $str;
}
function escapeAmp($pstr){
  $str = str_replace("&amp;","&",$pstr);

  return $str;
}

function unescape1($pstr){
  $str = unescape($pstr);
  if($str=="portfolio"){$str="p";}
  if($str=="text"){$str="t";}
  //$str = addslashes(decode($str));
  return $str;
}
function unescape($pstr){
  $str = str_replace("&","&amp;",$pstr);
  $str = str_replace("'","&apos;",$str);
  $str = str_replace("\"","&quot;",$str);

  return $str;
}
function unescapeAmp($pstr){
  $str = $pstr;
  $str = str_replace("'","&apos;",$str);
  $str = str_replace("\"","&quot;",$str);

  return $str;
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

function buildXmlOnlyInfo($arr,$pre,$path){
 $newxml = "";
 $newxmlFull = "";
 $newxmlLight = "";
 $newxmlShort = "";
 $json = "";
 $jsonshort = "";

 global $allowed_alt;
 global $settings;
 $pre1 = str_replace("\t"," ",$pre);

 for($i=0;$i<count($arr);$i++){
  if($arr[$i]["name"]!="MODELS" && $pre==""){continue;}
  $folder = $path."/".$arr[$i]["name"];

  $info1 = $folder."/info1.txt";
  $info2 = $folder."/info2.txt";
  $text =  $folder."/text.txt";
  $infoxml =  $folder."/data.xml";
  //echo $text."<br>";

  $newxml .= $pre."<section";
  $newxmlFull .= $pre."<section";
  $newxmlLight .= $pre."<s";
  $newxmlShort .= $pre."<s";
  $json .= $pre1."{\n".$pre1;
  $jsonshort .= $pre1."{\n".$pre1;

  while(list($k,$v) = each($arr[$i])){
   if(!is_array($v) && $k!="extraFile"){
     //echo $k."=".$v." ";
     if($k=="status"){$key = "st";}else{$key= substr($k,0,1);}

     if($k=="id"){$key = "id";}

     $newxml .= " ".$k."=\"".unescape($v)."\"";
     $newxmlFull .= " ".$k."=\"".unescape($v)."\"";

     if($k!="description2"){
      $newxmlLight .= " ".$key."=\"".unescape1($v)."\"";
      $newxmlShort .= " ".$key."=\"".unescape1($v)."\"";

      $json .= "\"".$key."\":\"".$v."\",";
      $jsonshort .= "\"".$key."\":\"".unescape1($v)."\",";
     }

   }
  }
  //alt-source="ogv;webm;..."


  //echo $info1." ".file_exists($info1)." ".filesize($info1)."<br>";
  if(file_exists($info1) && filesize($info1)>0){
    $newxmlLight .= " i1=\""."1"."\"";
    //$newxmlShort .= " i1=\""."1"."\"";
  }else{
    $newxmlLight .= " i1=\""."0"."\"";
    //$newxmlShort .= " i1=\""."0"."\"";
  }
  if(file_exists($info2) && filesize($info2)>0){
    $newxmlLight .= " i2=\""."1"."\"";
    //$newxmlShort .= " i2=\""."1"."\"";
  }else{
    $newxmlLight .= " i2=\""."0"."\"";
    //$newxmlShort .= " i2=\""."0"."\"";
  }
  if(file_exists($text) && filesize($text)>0){

    $newxmlLight .= " ds=\""."1"."\"";
    //$newxmlShort .= " ds=\""."1"."\"";
  }else{
    $newxmlLight .= " ds=\""."0"."\"";
    //$newxmlShort .= " ds=\""."0"."\"";
  }


  $newxml .= ">\n";
  $newxmlFull .= ">\n";
  $newxmlLight .= ">\n";
  $newxmlShort .= ">\n";
  //$json .= $pre."},\n";

    //echo $infoxml."<br>\n";
  if(file_exists($infoxml) && filesize($infoxml)>0){
    
    $newxmlShort .= $pre."<![CDATA[";
    $tf = fopen($infoxml,"r");
    $txt = fread($tf,filesize($infoxml));  
    $newxmlShort .=$pre. $txt;
    $json .= '"info":"'.$txt.'"';    
    $jsonshort .= '"info":"'.$txt.'"';    
    fclose($tf);
    $newxmlShort .= $pre."]]>\n";
  }
 
  if(file_exists($text) && filesize($text)>0){
    //echo "text\n";
    $newxmlShort .= "<![CDATA[";
    $tf = fopen($text,"r");
    $txt = fread($tf,filesize($text));
    $newxmlShort .= $txt;    
    $json .= '"info":"'.$txt.'"';    
    $jsonshort .= '"info":"'.$txt.'"';    
    fclose($tf);
    $newxmlShort .= "]]>\n";
  }
  //echo "</b><br>";
  $tmp = buildXmlOnlyInfo($arr[$i]["sections"],$pre."\t",$path."/".$arr[$i]["name"]);
  $newxml .= $tmp[0];
  $newxmlFull .= $tmp[1];
  $newxmlLight .= $tmp[2];
  $newxmlShort .= $tmp[3];
  $json .= "\n".$pre1."\"sections\":\n".$pre1."[\n".$tmp[4]."".$pre1."],";
  $jsonshort .= "\n".$pre1."\"s\":\n".$pre1."[\n".$tmp[5]."".$pre1."],";


  $newxml .= $pre."</section>\n";
  $newxmlFull .= $pre."</section>\n";
  $newxmlLight .= $pre."</s>\n";
  $newxmlShort .= $pre."</s>\n";

  if($i<(count($arr)-1)){
    $json .= $pre1."},\n";
    $jsonshort .= $pre1."},\n";
  }else{
    $json .= $pre1."}\n";
    $jsonshort .= $pre1."}\n";
  }
 }// main for
 //$json = substr($json, 0, strlen($json)-1); 

 $resA = Array();
 $resA[0] = $newxml;
 $resA[1] = $newxmlFull;
 $resA[2] = $newxmlLight;
 $resA[3] = $newxmlShort;
 $resA[4] = $json;
 
 return $resA;
}

	
function buildXml($arr,$pre,$path){
 $newxml = "";
 $newxmlFull = "";
 $newxmlLight = "";
 $newxmlShort = "";
 $json = "";
 $jsonshort = "";
 $jsonlight = "";

 global $allowed_alt;
 global $settings;
 $pre1 = str_replace("\t"," ",$pre);

 for($i=0;$i<count($arr);$i++){
  //echo str_replace("\t","--",$pre).$arr[$i]["name"]."<br>";
  //echo str_replace("\t","--",$pre).$path."/".$arr[$i]["name"]."<br>";
  $folder = $path."/".$arr[$i]["name"];
  $eList = getExtrafiles($arr[$i]["name"]);

  $info1 = $folder."/info1.txt";
  $info2 = $folder."/info2.txt";
  $text =  $folder."/text.txt";
  //echo $info1."<br>";
  $newxml .= $pre."<section";
  $newxmlFull .= $pre."<section";
  $newxmlLight .= $pre."<s";
  $newxmlShort .= $pre."<s";
  $json .= $pre1."{\n".$pre1;
  $jsonshort .= $pre1."{\n".$pre1;
  $jsonlight .= $pre1."{\n".$pre1;

  while(list($k,$v) = each($arr[$i])){
   if(!is_array($v) && $k!="extraFile"){
     //echo $k."=".$v." ";
     if($k=="status"){$key = "st";}else{$key= substr($k,0,1);}
     if($k=="id"){$key = "id";}

     $newxml .= " ".$k."=\"".unescape($v)."\"";
     $newxmlFull .= " ".$k."=\"".unescape($v)."\"";

     if($k!="description2"){
      $newxmlLight .= " ".$key."=\"".unescape1($v)."\"";
      $newxmlShort .= " ".$key."=\"".unescape1($v)."\"";
      $jsonlight .= "\"".$key."\":\"".escapeAmp(unescape1($v))."\",";

      $jsonshort .= "\"".$key."\":\"".unescape1($v)."\",";
     }
      $json .= "\"".$k."\":\"".$v."\",";

   }
  }
  //alt-source="ogv;webm;..."


  //echo $info1." ".file_exists($info1)." ".filesize($info1)."<br>";
  if(file_exists($info1) && filesize($info1)>0){
    $newxmlLight .= " i1=\""."1"."\"";
    $newxmlShort .= " i1=\""."1"."\"";
    $jsonlight .= "\""."i1"."\":\""."1"."\",";
  }else{
    $newxmlLight .= " i1=\""."0"."\"";
    $newxmlShort .= " i1=\""."0"."\"";
    $jsonlight .= "\""."i1"."\":\""."0"."\",";
  }
  if(file_exists($info2) && filesize($info2)>0){
    $newxmlLight .= " i2=\""."1"."\"";
    $newxmlShort .= " i2=\""."1"."\"";
    $jsonlight .= "\""."i2"."\":\""."1"."\",";
  }else{
    $newxmlLight .= " i2=\""."0"."\"";
    $newxmlShort .= " i2=\""."0"."\"";
    $jsonlight .= "\""."i2"."\":\""."0"."\",";
  }
  if(file_exists($text) && filesize($text)>0){

    $newxmlLight .= " ds=\""."1"."\"";
    $newxmlShort .= " ds=\""."1"."\"";
    $jsonlight .= "\""."ds"."\":\""."1"."\",";
  }else{
    $newxmlLight .= " ds=\""."0"."\"";
    $newxmlShort .= " ds=\""."0"."\"";
    $jsonlight .= "\""."ds"."\":\""."0"."\",";
  }

   if(count($eList)>0){
      //echo $row->name ."==". substr($eList[$j],0,strrpos($eList[$j],"."))."<br>";
      $u =" extraFile=\"".implode(",",$eList)."\"";
      $newxml .= $u;
      $newxmlFull .= $u;
      $newxmlLight .= $u;
      $newxmlShort .= $u;
      $json .= " \"extraFile\":\"".implode(",",$eList)."\"";
     
   }

  $newxml .= ">\n";
  $newxmlFull .= ">\n";
  $newxmlLight .= ">\n";
  $newxmlShort .= ">\n";
  //$json .= $pre."},\n";

  if(file_exists($text) && filesize($text)>0){
    $newxmlFull .= "<![CDATA[";
    $tf = fopen($text,"r");
    $newxmlFull .= fread($tf,filesize($text));    
    fclose($tf);
    $newxmlFull .= "]]>\n";
  }
  //echo "</b><br>";
  $tmp = buildXml($arr[$i]["sections"],$pre."\t",$path."/".$arr[$i]["name"]);
  $newxml .= $tmp[0];
  $newxmlFull .= $tmp[1];
  $newxmlLight .= $tmp[2];
  $newxmlShort .= $tmp[3];
  $json .= "\n".$pre1."\"sections\":\n".$pre1."[\n".$tmp[4]."".$pre1."],";
  $jsonshort .= "\n".$pre1."\"ss\":\n".$pre1."[\n".$tmp[5]."".$pre1."],";
  $jsonlight .= "\n".$pre1."\"ss\":\n".$pre1."[\n".$tmp[6]."".$pre1."],";

  $include = ($arr[$i]["name"]=="INTRO" || $arr[$i]["name"]=="BACKGROUND");

  $json .= "\n".$pre1."\"content\":\n".$pre1."[\n";
  $jsonshort .= "\n".$pre1."\"c\":\n".$pre1."[\n";
  $jsonlight .= "\n".$pre1."\"c\":\n".$pre1."[\n";

  for($ii=0;$ii<count($arr[$i]["content"]);$ii++){
    //echo $arr[$i]["content"][$ii]["name"]."<br>";
   if($arr[$i]["content"][$ii]["name"]==""){
     continue;
   }
   $newxml .= $pre."\t<content";
   $newxmlFull .= $pre."\t<content";
   $newxmlLight .= $pre."\t<c";
   if($include){
    $newxmlShort .= $pre."\t<c";
   }
    $json .= $pre1."  {";
    $jsonlight .= $pre1."  {";

   if($include){
    $jsonshort .= $pre1."  {";
   }
   while(list($k,$v) = each($arr[$i]["content"][$ii])){
     //echo $k."=".$v." ";
    //if($k=="thmbname"){
     //$newxml .= " ".$k."=\"".unescape($arr[$i]["content"][$ii]["name"])."\"";
    //}else{ 
     if($k!="cofile" && $k!="alt-source"){
       $newxml .= " ".$k."=\"".unescape($v)."\"";
       $newxmlFull .= " ".$k."=\"".unescape($v)."\"";
       $json .= "\"".$k."\":\"".unescapeAmp($v)."\",";
     }

     if($k!="cofile" && $k!="alt-source" && $k!="caption" && $k!="caption2" && $k!="thmbname" && $k!="ih" && $k!="iw" && $k!="sh" && $k!="sw" && $k!="th" && $k!="tw" && $k!="sth" && $k!="stw" && $k!="lih" && $k!="liw"){
       if($k=="cropped"){
        //$newxmlLight .= " ".substr($k,0,2)."=\"".unescape($v)."\"";
       }else{
        $newxmlLight .= " ".substr($k,0,1)."=\"".unescape($v)."\"";
        $jsonlight .= "\"".substr($k,0,1)."\":\"".unescapeAmp($v)."\",";
       }

       if($include){
        if($k=="cropped"){
         //$newxmlShort .= " ".substr($k,0,2)."=\"".unescape($v)."\"";
        }else{
         $newxmlShort .= " ".substr($k,0,1)."=\"".unescape($v)."\"";
         $jsonshort .= "\"".substr($k,0,1)."\":\"".unescapeAmp($v)."\",";
        }
       }
     }

    
   } // while
    $json = substr($json, 0, strlen($json)-1); 
    //$jsonlight = substr($jsonlight, 0, strlen($jsonlight)-1); 

    if($include){
        $jsonshort = substr($jsonshort, 0, strlen($jsonshort)-1); 
    }

   $cg = $arr[$i]["content"][$ii]["name"];
   $alt = substr($cg,0,strrpos($cg,"."));
   
   $alt_arr = array();

   for($ai=0;$ai<count($allowed_alt);$ai++){
     //echo $folder."/images/".$alt.".".$allowed_alt[$ai]."<br>";
     if(file_exists($folder."/images/".$alt.".".$allowed_alt[$ai])){
      array_push($alt_arr,$allowed_alt[$ai]);
     }


   }
     if($folder=='data_cms/TOM' || $folder=='data_cms/TO'){
       //echo $folder." ".$alt." ".getImageExtrafile($folder."/cofiles/",$alt)."\n";
     }
   if(getImageExtrafile($folder."/cofiles/",$alt)){
     $e = " cofile=\"".getImageExtrafile($folder."/cofiles/",$alt)."\"";
     $ej = " ,\"cofile\":\"".getImageExtrafile($folder."/cofiles/",$alt)."\"";
     $ejj = " ,\"cf\":\"".getImageExtrafile($folder."/cofiles/",$alt)."\"";

       $newxml .= $e;
       $newxmlFull .= $e;
       $newxmlLight .= $e;
       if($include){$newxmlShort .= $e;}
       $json .= $ej;
       if($include){$jsonshort .= $ej;}
       $jsonlight .= $ejj;
    }

   if(count($alt_arr)>0){
       $o = " alt-source=\"".implode(";",$alt_arr)."\"";
       $oj = " ,\"alt-source\":\"".implode(";",$alt_arr)."\"";
       $ojj = " \"alt\":\"".implode(";",$alt_arr)."\",";
     

       $newxml .= $o;
       $newxmlFull .= $o;
       $newxmlLight .= $o;
       if($include){$newxmlShort .= $o;}
       $json .= $oj;
       if($include){$jsonshort .= $oj;}
       $jsonlight .= $ojj;
   }
   if($settings["caption1"]!=""){
      $newxmlLight .= " "."c"."=\"".unescape($arr[$i]["content"][$ii]["caption"])."\"";
      $jsonlight .= "\""."c"."\":\"".unescape($arr[$i]["content"][$ii]["caption"])."\",";

      if($include){
       $newxmlShort .= " "."c"."=\"".unescape($arr[$i]["content"][$ii]["caption"])."\"";}
   }
   if($settings["caption2"]!="" || $settings["caption2_enabled"]=="on"){
      $newxmlLight .= " "."c2"."=\"".unescape($arr[$i]["content"][$ii]["caption2"])."\"";
      $jsonlight .= "\""."c2"."\":\"".unescape($arr[$i]["content"][$ii]["caption2"])."\",";
      if($include){
       $newxmlShort .= " "."c2"."=\"".unescape($arr[$i]["content"][$ii]["caption2"])."\"";   
      }
   }

   //$newxmlFull .= " "."i"."=\"".$arr[$i]["content"][$ii]["iw"]."x".$arr[$i]["content"][$ii]["ih"]."\"";
   //$newxmlFull .= " "."t"."=\"".$arr[$i]["content"][$ii]["tw"]."x".$arr[$i]["content"][$ii]["th"]."\"";
   $newxmlLight .= " "."i"."=\"".$arr[$i]["content"][$ii]["iw"]."x".$arr[$i]["content"][$ii]["ih"]."\"";
   $newxmlLight .= " "."t"."=\"".$arr[$i]["content"][$ii]["tw"]."x".$arr[$i]["content"][$ii]["th"]."\"";
   $jsonlight .= "\""."i"."\":\"".$arr[$i]["content"][$ii]["iw"]."x".$arr[$i]["content"][$ii]["ih"]."\",";
   $jsonlight .= "\""."t"."\":\"".$arr[$i]["content"][$ii]["tw"]."x".$arr[$i]["content"][$ii]["th"]."\",";

     $small_vert_image_height = $settings["small_vert_image_height"];
     if($small_vert_image_height==""){$small_vert_image_height = 0;}
     if($small_vert_image_height!=0){
       $iratio = $settings["vert_image_height"]/$small_vert_image_height;
       $a = " "."si"."=\"".($arr[$i]["content"][$ii]["sw"])."x".($arr[$i]["content"][$ii]["sh"])."\""; 
       //$newxmlFull .= $a;
       $newxmlLight .= $a;
       $jsonlight .= "\""."si"."\":\"".($arr[$i]["content"][$ii]["sw"])."x".($arr[$i]["content"][$ii]["sh"])."\"";

     }

   if($include){
    $newxmlShort .= " "."i"."=\"".$arr[$i]["content"][$ii]["iw"]."x".$arr[$i]["content"][$ii]["ih"]."\"";
    $newxmlShort .= " "."t"."=\"".$arr[$i]["content"][$ii]["tw"]."x".$arr[$i]["content"][$ii]["th"]."\"";
     if($small_vert_image_height!=0){
       $newxmlShort .= $a;
     }
   }
   $newxml .= "/>\n";
   $newxmlFull .= "/>\n";
   $newxmlLight .= "/>\n";
   if($include){
     $newxmlShort .= "/>\n";
   }
   if($ii<(count($arr[$i]["content"])-1)){
      $json .= "},\n";
      $jsonlight .= "},\n";
      if($include){
         $jsonshort .= "},\n";
      }
   }else{
      $json .= "}\n";
      $jsonlight .= "}\n";
      if($include){
        $jsonshort .= "}\n";
      }
   }
   //echo "<br>";
  } // for content

  $json .= $pre1."]\n";
  $jsonshort .= $pre1."]\n";
  $jsonlight .= $pre1."]\n";

  $newxml .= $pre."</section>\n";
  $newxmlFull .= $pre."</section>\n";
  $newxmlLight .= $pre."</s>\n";
  $newxmlShort .= $pre."</s>\n";

  if($i<(count($arr)-1)){
    $json .= $pre1."},\n";
    $jsonshort .= $pre1."},\n";
    $jsonlight .= $pre1."},\n";
  }else{
    $json .= $pre1."}\n";
    $jsonshort .= $pre1."}\n";
    $jsonlight .= $pre1."}\n";
  }
 }// main for
 //$json = substr($json, 0, strlen($json)-1); 

 $resA = Array();
 $resA[0] = $newxml;
 $resA[1] = $newxmlFull;
 $resA[2] = $newxmlLight;
 $resA[3] = $newxmlShort;
 $resA[4] = $json;
 $resA[5] = $jsonshort;
 $resA[6] = $jsonlight;
 
 return $resA;
}

function parseXml($sxe,$pre){
 //var_dump($sxe);
 $k = 0;
 $arr = Array();
 foreach ($sxe->section as $section) {
     //echo $pre."<b>";
     //echo strpos($section["description"],"\n")."<br>";
     $arr[$k] = Array();

     foreach($section->attributes() as $a => $b) {
       $arr[$k][$a] = escape($b);
       //echo $a,'="',escape($b),"\"\n";
     }
     
     //echo "</b><br>";
     $j = 0;
     foreach ($section->content as $content) {
       //echo $pre.$content["name"]."<br>";
       $arr[$k]["content"][$j] = Array();
       //echo $pre;
       foreach($content->attributes() as $a => $b) {
         $arr[$k]["content"][$j][$a] = escape($b);
          //echo $a,'="',$arr[$k]["content"][$j][$a],"\"\n";

       }
       $j++;
       //echo "<br>";
     }

     $arr[$k]["sections"] = parseXml($section,$pre."--");
    
    $k++;
 }    
  return $arr;
}
function getImageExtrafile($fld,$nm){
   $dir = $fld;
   $res = false;
   //echo $fld." ".$nm."<br>";
 if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
            //echo "filename: $file : filetype: " . filetype($dir . $file) . "<br>";
            if(is_file($dir . $file)){
               
   	       $alt = substr($file,0,strrpos($file,"."));
               if($alt==$nm){  
                //echo $file."<br>";               
                 $res = $file;
               }
            }
        }
        closedir($dh);
    }
  }
  return $res;
}
function getExtrafiles($fld){
  
  $eList = array();
  $dir = "data_cms/".$fld."/extraFiles/";
  $i=0;
  if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
            //echo "filename: $file : filetype: " . filetype($dir . $file) . "\n";
            if(is_file($dir . $file)){
               $eList[$i++] = unescape($file);
            }
        }
        closedir($dh);
    }
  }
  return $eList;
}

?> 