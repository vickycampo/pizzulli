<?php
ini_set('display_errors', true);error_reporting(E_ALL ^ E_NOTICE);$where1 = "";
$post = array();
$date = '';

if($_POST["date"]){
  $post["date"] = 'date='.'"'.addslashes($_POST["date"]).'"';
  $date = $_POST["date"];
}
if($_POST["action"]){
  $post["action"] = 'action like "%'.addslashes($_POST["action"]).'%"';
}
if($_POST["sub_action"]){
  $post["sub_action"] = 'sub_action like "%'.addslashes($_POST["sub_action"]).'%"';
}
if($_POST["name"]){
  $post["name"] = 'first_name like "%'.addslashes($_POST["name"]).'%" or last_name like "%'.addslashes($_POST["name"]).'%"';
}
if($_POST["ip"]){
  $post["ip"] = 'ip like "%'.$_POST["ip"].'%"';
}
if($_POST["urlo"]){
  $post["urlo"] = 'urlo like "%'.$_POST["urlo"].'%"';
}
if($_POST["post"]){
  $post["post"] = 'post like "%'.$_POST["post"].'%"';
}
if(count($post)>0){
 $where1 = implode(" and ",$post);
}
$where = "WHERE ua.user_id=".$user_id." and u.userId=ua.user_id ";


if($where1 == ""){
 $where .= "  ORDER BY time DESC LIMIT 100";
}else{
 $where .= " and ".$where1." ORDER BY time DESC  LIMIT 200";
}


$files1 = scandir("../user_log",1);
$files = [];

foreach($files1 as $file){
   if (strpos($file, "full")===false){
      array_push($files, $file);
   }
}

if ($date==''){
  $date = $files[0];
}


$text = file_get_contents("../user_log/".$date);

$arr = explode("<->\n", $text);
$arr = array_reverse($arr);

$actions = [];
foreach ($arr as $vv) {	 
      $v = explode("\n", $vv);

   if (!in_array($v[3], $actions)){
      $actions[] = $v[3]; 
   }   
}


?>
<head>
<style>
body {
  margin:30px;
}
table {
  padding:0;  
}
td {
  font-family: Verdana, Arial, Helvetica, sans-serif; 
  font-size: 10px;
  padding:0;
}
.select {
    width: 230px; /* Ширина списка в пикселах */
   }
</style>
</head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<script>
function empty(){
  location.href = "log.php";
}

function getIpInfo(k,ip){
   $.post("http://ip-api.com/json/"+ip, {},function(data) {
       $("#ip"+k).html(data.country+", "+data.city);
       //console.log(data);
   } );
 
}
function showDetails(k,time){
   $.post("log_details.php?time="+time, {},function(data) {
       $("#details"+k).html(data);
       
   } );
 
}
</script>
<body>
<form action="" method="post">
<table border=0 cellspacing="10px" iwidth="1440px">
<tr>
<td width="100px"><b>DATE </b>
</td>       
<td width="100px"><b>USER</b>
</td>
<td width="100px"><b>IP </b>
</td>
<td width="230px"><b>ACTION </b>
</td>
<!--<td  width="100px"><b>SUB-ACTION</b>
</td>-->
<td width="300px"><b>SECTION</b>
</td>
<td width="180px"><b>SEARCH</b>
</td>
<td width="150px">
</td> 
</tr>
<tr>
<td><select name="date">
<?php
  foreach ($files as $file){
     echo '<option value="'.$file.'"';
     if ($file==$_POST["date"]) {echo " selected ";}
     echo '>'.$file.'</option>';
  }
?>

</select>
</td>
<td><input size=10 type="text" name="name" value="<?php echo $_POST["name"];?>"></td>
<td><input size=10 type="text" name="ip" value="<?php echo $_POST["ip"];?>"></td>
<td>
<select name="action">
<option value="">-</option>
<?php
for($i=0;$i<count($actions);$i++){
  echo '<option value="'.$actions[$i].'">'.$actions[$i].'</option>';
}
?>
</select>
</td>
<!--
<td>
<select name="sub_action">
<?php
for($i=0;$i<count($subactions);$i++){
  echo '<option value="'.$subactions[$i]["sub_action"].'">'.$subactions[$i]["sub_action"].'</option>';
}
//<input type="text" name="sub_action" value="<?php echo $_POST["sub_action"];?>">
?>
</select>
</td>
-->
<td width="300px">
<input size="22" type="text" name="urlo" value="<?php echo $_POST["urlo"];?>"></td>
<td width="180px">
<input size="22" type="text" name="post" value="<?php echo $_POST["post"];?>"></td>
<td width="150px">
<input type="submit" name="search" value="SEARCH" disabled>&nbsp;&nbsp;&nbsp;
<input type="button" name="clear" value="CLEAR" onClick="empty();return false;" disabled>
</td>
</form>
</tr>
<?php
        $k=0;
	foreach ($arr as $vv) {	 
	$v = explode("\n", $vv);

	echo "<tr>";
	echo'<td colspan="8" height="1px" bgcolor="#c9c9c9"></td></tr><tr>';
        echo "<td height='16px' width='100px'>\n";
	echo $v[0];
	echo "</td><td width='100px'>\n";
	echo "<b>".$v[2]."</b>\n";
	echo "</td><td width='100px'>";
	echo "<a href='#' onClick='getIpInfo(".$k.",\"".$v[1]."\");return false;'>".$v[1]."</a>\n";
        echo "<div id='ip".$k."'></div>\n";
	echo "</td><td>\n";
	echo $v[3];
	echo "</td><td>\n";
	echo $v[5];
	echo "</td><td width='230px'>\n";
	//echo "<a href='#' onClick='showDetails(".$k.",\"".$v[3]."\");return false;'>details</a>";
	echo $v[4];
	echo "<br>";
        echo "<div id='details".$k."' style='width:100%;border: 0;'>".$v[6]."</div>";
	echo "</td><td></td></tr>\n";

             $k++;
        }


?>
</table>
<div id="hint" style="width:200px;height:50px;border: 0;position:fixed;"></div>