<?php

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
// ��� �-�� �������� ��� ������� ������������ ����

}

function characterData($parser, $data)
{
// ��� �-�� �������� � ������� ������ ����

}

$menu_temp = array();

function getSettings(){
	$src = dirname(__FILE__)."/settings/settings.xml";
    
	$file = $src;
	$menu_arr = array();
	global $menu_temp;
	
	// ������� ������
	$xml_parser = xml_parser_create();

	// ����������� ��� (�� ������������ � ��������)
	xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, true);

	// ��������� �-�� ��������� ���������� ���� � ��������� ���� (��. ����)
	xml_set_element_handler($xml_parser, "startElement", "endElement");

	// �-�� ��������� ������ ������ ���� (��. ����)
	xml_set_character_data_handler($xml_parser, "characterData");

	// ��������� ����
	if ( !($fp = fopen($file, "r")) )
	{ die("could not open XML input"); }

	// ������ ���� � ����������
	while ($data = fread($fp, 4096))
	{
	if ( !xml_parse($xml_parser, $data, feof($fp)) )
	{
	// ��������� ���� �������� ������ ��������
             $result['status'] = 'error';
             $result['error'] = sprintf("settings.xml error: %s at line %d",
	xml_error_string(xml_get_error_code($xml_parser)),
	xml_get_current_line_number($xml_parser)); 

             echo json_encode($result);
             die;

	}
	}
	// ������� ������, �� ������ ���� ���� ;0)
	xml_parser_free($xml_parser);
	
	
	
	//}
	
	$j = 0;
	for ($i=0; $i<(count($menu_temp)/2); $i++)
	{
		$menu_arr[$i] = array("name"=>$menu_temp[$j]["variable"],"variable"=>$menu_temp[++$j]["variable"]);
		$j++;	
	}
	

        foreach ($menu_arr as $section)
        {	
           $settings[$section["name"]] = $section["variable"];
        }

        $settings["instagram_height"] = 293;
        $settings["instagram_width"] = 293;

 return $settings;
}

$settings = getSettings();

?>