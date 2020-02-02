<?php
include("config.php");


session_start();

include("utils/checkuser.php");


if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    die;
}
 
 
function SXml($xmlstr)
{
	if (strlen($xmlstr)<=1) return false;
	$xml =  new SimpleXMLElement($xmlstr);
	return $xml;
}
function _toUTF($text)
{
	//$text = mb_convert_encoding($text, "auto", "utf-8");
	return $text;
}

function HeaderHTML()
{
?>
<html>
<head>

<script>
    $(document).ready(function () {

        $( "#settings_form" ).submit(function( event ) {
          console.log( "Handler for .submit() called." );
          event.preventDefault();
          formSubmit();
        });


function formSubmit(form) {
    var btn = $("#settings_form").find('[type=submit]');
    btn.attr('disabled', 'disabled');
    $('#result').html('Saving...');
    $.ajax({
        url: $("#settings_form").attr('action'),
        type: 'post',
        data: $("#settings_form").serialize(),
        //dataType: 'json',
        success: function (response) {
            $('#result').html('Saved.');
            console.log("res="+response);
            if (response.status === 'error') {
            } else if (response.status === 'ok') {

            }
            if (response.redirect !== undefined) {
                //location.href = response.redirect;
            }
            btn.removeAttr('disabled');
        }
    });	 // /ajax
}

});

</script>
</head>
<body>
    <div id="settings_top" style="width:100%;height:20px;" align="right">
        <!--<a href="#" onClick="updateWebsite();return false;" class="link">UPDATE WEBSITE</a>&nbsp;&nbsp;&nbsp;-->
        <a href="#" onClick="closeSettings();return false;" class="link">CLOSE</a>&nbsp;
    </div>
    <div style="display:block" align="left">
     <a href="<?=BASE_PATH?>/utils/regenerate.php" target="_blank">REGENERATE UTIL</a>
     <br><br>
     <a href="<?=BASE_PATH?>/utils/log_viewer.php" target="_blank">USAGE LOG</a>
    </div>



<?php
}

function FooterHTML()
{
?>
    <div id="settings_bottom" style="width:100%;height:20px;" align="right">
        <a href="#" onClick="closeSettings();return false;" class="link">CLOSE</a>&nbsp;
    </div>

</body>
</html>
<?php
}
$adds = " Can't be 0.";
$comments = array();
$comments["vert_image_height"] = "Height for vertical images.".$adds;
$comments["hor_image_height"] = "Height for horizontal images.".$adds;
$comments["hor_image_max_width"] = "Max width for horizontal images.".$adds;
$comments["small_vert_image_height"] = "Height for vertical images smaller version.";
$comments["small_hor_image_height"] = "Height for horizontal images smaller version.";
$comments["small_hor_image_max_width"] = "Max width for horizontal images smaller version.";
$comments["thumbnail_height"] = "Height for thumbnail image.";
$comments["thumbnail_width"] = "Width for thumbnail image.";
$comments["smallthumbnail_height"] = "-- for small thumbnail";
$comments["smallthumbnail_width"] = "-- for small thumbnail";
$comments["image_quality"] = "Quality for images.  0-100";
$comments["smallimage_quality"] = "Quality for images smaller version.  0-100";
$comments["largeimage_quality"] = "Quality for images large version.  0-100";
$comments["thumbnail_quality"] = "Quality for thumbnails/smallthumbnails/thumbnails140.  0-100";
$comments["thumbnail_background"] = "Format 0x000000. Background color to display in cms.";
$comments["use_thumbnails140_folder"] = "0/1. Use thumbnails140 folder instead of thumbnails to display thumbnails in cms.";
$comments["without_originals"] = "1/0. Keep originals or not in system.";
$comments["join_images_enabled"] = "1/0. Join images functionality.";
$comments["make_smallthumbnail_bw"] = "1/0. Should system make black&white version on smallthumbnails.";
$comments["make_image_bw"] = "1/0. Should system make black&white version on images.";
$comments[""] = "";
$comments[""] = "";
$comments[""] = "";
$comments[""] = "";
$comments["additional_cms_btn"] = "Name for additional btn at right bottom.";
$comments["additional_cms_link"] = "Link for this btn. Starts with http://";
$comments["news_image_width"] = "0-400";

$comments["special_describe_button1_for"] = "Section name.";
$comments["special_describe_button1_name"] = "";
$comments["upload_extra_file_for_image_btn_name"] = "Button name. If not empty you will see button to upload extra file for image.";
$comments["external_editme_section"] = "Section name. On click EDIT that section you go url (see next setting)";
$comments["external_editme_section_url"] = "URL for special sction (see previous setting) Format: www....";
$comments["bugreport_url"] = "url to report bugs.";
$comments["update_website_option"] = "0/1. disable/enable update through cms.";


$notes = array();
$notes["Notes 1"] = "IMAGES";
$notes["Notes 2"] = "LARGE&nbsp;IMAGES";
$notes["Notes 3"] = "SMALL&nbsp;IMAGES";
$notes["Notes 4"] = "THUMBNAILS";
$notes["Notes 5"] = "SMALLTHUMBNAILS";
$notes["Notes 6"] = "IMAGES&nbsp;FILES&nbsp;OPTIONS";
$notes["Notes 7"] = "ADDITIONAL&nbsp;BUTTONS&nbsp;SETTINGS";
$notes["Notes 8"] = "GENERAL&nbsp;SETTINGS";
$notes["Notes 9"] = "SOE&nbsp;SETTINGS";


$src = "settings/settings.xml";
//$menu_arr = ParceXML($src);
/*if (version_compare(PHP_VERSION , "5.0.0", ">")) echo "1"; else echo "*";

if (version_compare(PHP_VERSION , "5.0.0", ">"))
	{
		$xmlstr = _toUTF(file_get_contents($src));	
		$xml = SXml($xmlstr);
		$menu_arr = array();
		$i = 0;
		foreach ($xml->section as $section)
		{
			$i++;
			$menu_arr[$i] = array("name"=>(string)$section->attributes()->name,"variable"=>(string)$section->attributes()->variable);
		}
	}
	else
	{*/
	$file = $src;
	$menu_arr = array();
	$menu_temp = array();
	function startElement($parser, $name, $attrs)
	{
			global $menu_temp;
			foreach ( $attrs as $n => $v)
			{	
				$menu_temp[] = array("name"=>$n,"variable"=>$v);		
				
			}
			
			
	}

	function endElement($parser, $name)
	{
	// Эта ф-ия работает при встрече закрывающего тега

	}

	function characterData($parser, $data)
	{
	// Эта ф-ия работает с данными внутри тега
	
	}

	// Создаем парсер
	$xml_parser = xml_parser_create();

	// Настраиваем его (не чувствителен к регистру)
	xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, true);

	// Указываем ф-ии обработки начального тега и конечного тега (см. выше)
	xml_set_element_handler($xml_parser, "startElement", "endElement");

	// Ф-ия обработки данных внутри тега (см. выше)
	xml_set_character_data_handler($xml_parser, "characterData");

	// Открываем файл
	if ( !($fp = fopen($file, "r")) )
	{ die("could not open XML input"); }

	// Читаем файл и парсингуем
	while ($data = fread($fp, 4096))
	{
	if ( !xml_parse($xml_parser, $data, feof($fp)) )
	{
	// Сообщение если возникла ошибка парсинга
	die( 
	sprintf("XML error: %s at line %d",
	xml_error_string(xml_get_error_code($xml_parser)),
	xml_get_current_line_number($xml_parser)) 
	);
	}
	}
	// удаляем парсер, он сделал свое дело ;0)
	xml_parser_free($xml_parser);
	
	
	
	//}
	
	$j = 0;
	for ($i=0; $i<(count($menu_temp)/2); $i++)
	{
		$menu_arr[$i] = array("name"=>$menu_temp[$j]["variable"],"variable"=>$menu_temp[++$j]["variable"]);
		$j++;	
	}
	
##############################

#############################
if ($_REQUEST["action"] == "save")
{
	$save_xml = "<sections>\r";	
	foreach ($_POST["s"] as $name=>$variable)
	{      
                $var = addslashes(str_replace("&","&amp;",$variable));
                $var = str_replace("\"","&quot;",$variable);
		$save_xml .= "\t<section name=\"".$name."\" variable=\"".$var."\" />\r";		
		
	}
	$save_xml .= "</sections>";	
	
	
	$handle = fopen($src, "w+");
	fwrite($handle,_toUTF($save_xml));
	fclose($handle);
	echo "ok";die;
	//Header("Location: editsettings.php");	
}
else
{
HeaderHTML();
echo "<form method='post' action='".BASE_PATH."/editsettings.php' id='settings_form'>
<input type='hidden' name='action' value='save'>
<table cellpadding=0 cellspacing=0 border=0 style='border-collapse: collapse'>";
$flag = true;
foreach ($menu_arr as $section)
{	$c = "";
        if($flag){$c = "bgcolor='#f7f7f7'";$flag = false;}else{$flag = true;}
        //if(strpos($section["name"],"Notes")!==false){$c = "bgcolor='#bbbbbb'";}
        if(strpos($section["name"],"Notes")!==false){$s = "Notes";}else{$s = $section["name"];}
        if(strpos($section["name"],"Notes")!==false){
          
          echo "<tr><td height='25'></td><td></td></tr>";
          echo "<tr><td height=30 colspan=2><table border=0 cellspacing=0><tr><td width=5>".$notes[$section["name"]]."&nbsp;&nbsp;&nbsp;</td><td><div style='width:100%;height:1px;background-color:#000000'></div></td></tr></table></td></tr>";
        }
	echo "<tr ".$c."><td width=200>".$s."</td>";
	echo "<td width=100%><input type='text' name='s[".$section["name"]."]' value='".$section["variable"]."'</td>";
        echo "</tr>";
        echo "<tr  ".$c."><td></td><td>".$comments[$section["name"]]."</td></tr>";
        echo "<tr  ".$c."><td height='15'></td><td></td></tr>";
}	

echo "</table>";
echo "<input type='submit' id='submit' value='save' class='btn' style='width:70px'><br><br>";
echo "<span id='result'></span>";
echo "</form>";
}
FooterHTML();
