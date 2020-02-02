<html>
<head>
<style> 
<!--
 body{
      background-color: #FFFFFF; 
      font-family: Verdana, Arial; 
      font-size: 10px;
      } 
 td,select{
      background-color: #FFFFFF; 
      font-family: Verdana, Arial; 
      font-size: 10px;
      } 
 a {
      text-decoration: none;
 }
-->
 </style>

</head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>


<body leftmargin=100 onLoad="selectS();">

<?php
include("../config.php");
include("../settings.php");

$sxe = simplexml_load_file("../../data_cms/info.xml");


$xml_arr = parseXml($sxe,"");


$settings = getSettings();
 
$large_vert_height= $settings["vert_image_height"]; 
$large_hor_height=  $settings["hor_image_height"]; 
$large_hor_width=   $settings["hor_image_max_width"]; 	 
$image_quality = $settings["image_quality"];

$original_max_height = $settings["resize_original_to_max_height"];
$original_max_width = $settings["resize_original_to_max_width"];
$original_quality = $settings["original_quality"];

$small_vert_image_height = $settings["small_vert_image_height"];
$small_hor_image_height = $settings["small_hor_image_height"];
$small_large_hor_width = $settings["small_hor_image_max_width"];

$large_vert_image_height = $settings["large_vert_image_height"];
$large_hor_image_height = $settings["large_hor_image_height"];
$large_large_hor_width = $settings["large_hor_image_max_width"];

$smallimage_quality = $settings["smallimage_quality"];
$largeimage_quality = $settings["largeimage_quality"];

$thumbheight = $settings["thumbnail_height"];
$thumbwidth = $settings["thumbnail_width"];

$thumb_quality = $settings["thumbnail_quality"];

$smallthumbheight = $settings["smallthumbnail_height"];
$smallthumbwidth = $settings["smallthumbnail_width"];

$instagramheight = $settings["instagram_height"];
$instagramwidth = $settings["instagram_width"];


$without_originals = $settings["without_originals"];

if($settings["use_thumbnails140_folder"]=="1" || $settings["use_thumbnails140_folder"]=="on"){
 $use140 = true;
}else{
 $use140 = false;
}


if($without_originals==1){
echo "Sorry, cms hasn't originals.";
exit;
}

?>
<script>
function selectS(){
  showSizes();
}
function showSizes(){
   d = document.getElementById("div_originals");
   d.style.visibility = "hidden";
   d = document.getElementById("div_images");
   d.style.visibility = "hidden";
   d = document.getElementById("div_smallimages");
   d.style.visibility = "hidden";
   d = document.getElementById("div_largeimages");
   d.style.visibility = "hidden";
   d = document.getElementById("div_thumbnails");
   d.style.visibility = "hidden";
   d = document.getElementById("div_smallthumbnails");
   d.style.visibility = "hidden";
   d = document.getElementById("div_instagram");
   d.style.visibility = "hidden";

   s = document.getElementById("target");

   d = document.getElementById("div_"+s.value);
   d.style.visibility = "visible";


}
</script>

<table border=0>
<tr><td>


<table border="0" style="border:1px">
<tr><td width="500" >

 <table border="1" cellspacing=0>
 <tr><td  width="500">
 <table border="0" cellspacing=0>
<?php $k = 0;
   $paths = array(); 
   $ids = array();
   showLevel("../../data_cms/",$xml_arr,"","<br>");?>
 </table>
 </td></tr>
 </table>


</td></tr>
<tr><td height=100 valign="top">
<select id="target" onChange="showSizes();">
<option value="images" selected>images</option>
<option value="smallimages">smallimages</option>
<option value="largeimages">largeimages</option>
<option value="thumbnails">thumbnails (+thumbnails_cms)</option>
<option value="smallthumbnails">smallthumbnails</option>
<option value="instagram">instagram</option>
<option value="originals">originals</option>
</select><input type="button" value="Resize" id="resize" onClick="start();"><input type="button" value="Cancel" onClick="stop();">
<!-- &nbsp;&nbsp;<input type='button' name = 'update' value='update website' onClick='updateWebsite()'><b>Before update login into cms!!!</b>-->

<div id="div_originals" style="position:absolute;visibility:visible;">
vertical images height = <?php echo $original_max_height;?><br>
horizontal images width = <?php echo $original_max_width;?><br>
quality <?php echo $original_quality;?>
</div>

<div id="div_images" style="position:absolute;visibility:visible;">
vertical images height = <?php echo $large_vert_height;?><br>
horizontal images height = <?php echo $large_hor_height;?><br>
maximum width = <?php echo $large_hor_width;?><br>
quality <?php echo $image_quality;?>
</div>
<div id="div_smallimages" style="position:absolute;visibility:hidden">
vertical images height = <?php echo $small_vert_image_height;?><br>
horizontal images height = <?php echo $small_hor_image_height;?><br>
maximum width = <?php echo $small_large_hor_width;?><br>
quality <?php echo $smallimage_quality;?>
</div>
<div id="div_largeimages" style="position:absolute;visibility:hidden">
vertical images height = <?php echo $large_vert_image_height;?><br>
horizontal images height = <?php echo $large_hor_image_height;?><br>
maximum width = <?php echo $large_large_hor_width;?><br>
quality <?php echo $largeimage_quality;?>
</div>
<div id="div_thumbnails" style="position:absolute;visibility:hidden">
thumbnails height x width  <?php echo $thumbheight." x ".$thumbwidth;?><br>
thumbnails140 height x width  <?php echo " 140x ";?><br>
quality <?php echo $thumb_quality;?>
</div>
<div id="div_smallthumbnails" style="position:absolute;visibility:hidden">
small thumbnails height x width  <?php echo $smallthumbheight." x ".$smallthumbwidth;?><br>
quality <?php echo $thumb_quality;?>
</div>
<div id="div_instagram" style="position:absolute;visibility:hidden">
instagram thumbnails height x width  <?php echo $instagramheight." x ".$instagramwidth;?><br>
quality <?php echo $thumb_quality;?>
</div>


</td>
</tr>
<tr>
 <td height=100>
	<div id="finish"></div>
	<div id="xml_d" style="visibility:hidden"><b>Updating all xml's result:</b></div>
	<div id="xml" ></div>

 </td>
</tr>
</table>

</td>
<td valign="top">
<div id="feeds" style="position:relative;float:left"></div>
</td>
</tr>
</table>


<script>
function updateWebsite(){
  document.location.href="updatewebsite.php?action=update";
}


paths = [];
ids = [];

<?php


for($i=0;$i<count($paths);$i++){
 $paths[$i] = str_replace("+","_plus_",$paths[$i]);
 echo "paths[$i]='".urlencode($paths[$i])."';\n";
 echo "ids[$i]='".$ids[$i]."';\n";
}
echo "l=".count($paths).";\n";
?>
function workPath(n, type){
 
 im = document.getElementById("im"+n);
 im.src = "lo.gif";
 s = document.getElementById("target");

 $("#feeds").load("../api.php", {action:'regenerate',path: (paths[n]) ,id: ids[n], target:s.value, type: type}, 
   function(){
    im.src = "ok.gif";
    if(n<(l-1) && type==1){
      workPath(n+1, type);
    }else{
      enable();
      
    }
 });
}
function correctXml(){
 $("#finish").load("xml_correct.php", {limit: 25}, 
   function(){
      enable();
      wsXml();
 });


}
function wsXml(){
 $("#xml").load("write_xml.php", {}, 
   function(){
      
   xd = document.getElementById("xml_d");
   xd.style.visibility = "visible";
   
 });


}

function start(){

 for(i=0;i<l;i++){
   im = document.getElementById("im"+i);
   im.src = "blank.gif";
 } 
 s = document.getElementById("target");
 s.disabled = true;
 r = document.getElementById("resize");
 r.disabled = true;
  workPath(0,1);
}
function enable(){
 s = document.getElementById("target");
 s.disabled = false;
 r = document.getElementById("resize");
 r.disabled = false;
}

function stop(){
 enable();
 workPath();
}


function onlyThis(link){
   console.log(link.html());
   console.log(link.data('n'));
   workPath(link.data('n'), 2);
}

</script>
<?php
function showLevel($path,$link,$pre,$nl){
   //$link =  &$GLOBALS["xml_arr"];
      global $k;
      global $paths, $ids;
      $l = count($link);
      
      for($j=0;$j<$l;$j++){
        $paths[$k] = $path.$link[$j]["name"];
        $ids[$k] = $link[$j]["id"];
        echo "<tr><td>";
        echo "<a href='#' data-n='".$k."' onclick='onlyThis($(this));return false;'>".$pre.$path.decode($link[$j]["name"])."</a>"."<br>";
        echo "</td>";echo "\n";

        echo "<td><img id='im".$k."' src='blank.gif' height=16></td>";
        echo "</tr>";echo "\n";
        $k++;
        showLevel($path.$link[$j]["name"]."/",$link[$j]["sections"],$pre."&nbsp;&nbsp;&nbsp;&nbsp;","");
        //echo $nl;
      }
   

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
function escape($pstr){
  $str = str_replace("&amp;","&",$pstr);
  $str = str_replace("&apos;","'",$str);
  $str = str_replace("&quot;","\"",$str);

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