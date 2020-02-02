<html>
<head>
<title>CMS</title>
<style> 
<!--
 body{
      background-color: #FFFFFF; 
      font-family: Verdana, Arial; 
      font-size: 10px;
	margin: 0 auto;
      } 
 td{
      background-color: #FFFFFF; 
      font-family: Verdana, Arial; 
      font-size: 10px;
      } 
-->
 </style>

</head>

<body scroll="no" bottommargin="0" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table border=0 width="100%" height="100%">
<tr><td height="15">
<?php
$h = $_GET["h"];
$w = $_GET["w"];

$file = stripslashes($_GET["file"]);

$file_ex = file_exists($file);
$info = pathinfo($file);
$type = $info["extention"];

if($file_ex){
  $z = explode("/",$file);
  //echo $h."x".$w;
  //echo "<br>";

  echo "<b>".$z[count($z)-1]."</b> exists. Size is ".filesize($file)." bytes. (".$h."x".$w.")";
  //echo "<br>";
  //echo $file;
  //echo "<br>";
  $t1 = true;
  $src = $file;
}else{
  echo "Here is no $file.";
  echo "<br>";
}
if($type=="ogg" || $type=="ogv"){
  $html5 = true;
  $codec = 'type="video/ogg;codecs=theora, vorbis"';
}else if($type=="webm"){
  $html5 = true;
  $codec = 'type="video/webm;vorbis,vp8"';
}else if($type=="mp4"){
  $html5 = true;
  $codec = 'type="video/mp4;codecs=avc1.42E01E, mp4a.40.2"';
}else if($type=="mov"){
  $html5 = false;

}
?>
</td></tr>
<tr><td align="center" valign="center">

<?php
if($html5){
?>
<video
  src="<?php echo $src;?>"
  <?php echo $codec;?>
  autoplay
  controls>
  Your Browser does not support the video tag, upgrade to Firefox 3.5+
</video>
<?php
}else{
?>

<embed id="qmovie" name="qmovie" src="<?php echo $src;?>"  width="<?php echo $w;?>"  height="<?php echo $h+15;?>" autoplay="false" loop="false" bgcolor="#FFFFFF" controller="true" kioskmode="true" cache="false" pluginspage="http://www.apple.com/quicktime/" ></embed>

<?php
}
?>
</td></tr>
</table>
</body>
</html>