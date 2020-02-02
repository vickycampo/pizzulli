<?php

  ini_set('display_errors', true);error_reporting(E_ALL ^ E_NOTICE);

  $path = "cms/data/";

  if($prefix){}

  $xml = simplexml_load_file($path."info.xml");



  $sitemap = [];





  $data = parseXml($xml,"");

  $seo = [];

  $root = getSection($data, "");

  $root_info = getSectionInfo($data, "");



  for($i=0;$i<count($root);$i++){

     

      if($root_info[$i]["name"] == "PORTFOLIO"){

         $portfolio_section_name = $root[$i]["name"];

      }

      if($root_info[$i]["name"] == "INFO SECTIONS"){

         $info_section_name = $root[$i]["name"];

      }

      if($root_info[$i]["name"] == "META"){

         

         $seo = (array) @json_decode(file_get_contents($path.$root_info[$i]["name"]."/seo.json"));

      }

  }



  $menu = getSection($data, $portfolio_section_name);

  $menu_info = getSectionInfo($menu, $portfolio_section_name);



  $links = [];  $k = 0;



  for($i=0;$i<count($menu);$i++){

    //echo $menu[$i]["name"]."<br>";

    if($menu[$i]["status"]==1){

         $links[$k] = [];

         $links[$k]["name"] = $menu[$i]["name"];

         $links[$k]["link"] = getLink($menu[$i]["name"]);

         $links[$k]["ID"] = $menu_info[$i]["description4"];

         $links[$k]["seo"] = (array) @json_decode(file_get_contents($path.$portfolio_section_name."/".$menu[$i]["name"]."/seo.json"));

         //show($links[$k]["seo"]);

         $links[$k]["inquire"] = false;

         if($menu[$i]["description"] != ''){

            $links[$k]["inquire"] = true;

         }



         $content = getContent($menu, $menu[$i]["name"]);

         $links[$k]["images"] = $content;

         $links[$k]["path"] = "".$path.$portfolio_section_name."/".$menu[$i]["name"]."/images/";

         $sitemap[] = ["link"=>$links[$k]["link"], "priority"=>"1.0"];

         for($j=0;$j<count($links[$k]["images"]);$j++){

            $sitemap[] = ["link"=>$links[$k]["link"]."/#".($j+1), "priority"=>"0.8"];

         }



         $sub = getSection($menu, $menu[$i]["name"]);

         for($j=0;$j<count($sub);$j++){

           //echo $sub[$j]["name"]."<br>";

           if($sub[$j]["status"]==1){

              

            if($sub[$j]["name"] == "DESCRIPTION"){

                   $txt = simplexml_load_file($path.$portfolio_section_name."/".$menu[$i]["name"]."/".$sub[$j]["name"]."/data.xml");

                   $links[$k]["description"] = (string) $txt->record->content;

                   //echo $links[$k]["description"];

            }

           }

         }

         $k++;



    }

  }

  //show($links);



  $info = getSection($data, $info_section_name);

  $info_info = getSectionInfo($info, $info_section_name);





  for($i=0;$i<count($info);$i++){

    //echo $info[$i]["name"]."<br>";

    if($info[$i]["status"]==1){



      if($info[$i]["name"] == "contact"){

             $txt = simplexml_load_file($path.$info_section_name."/".$info[$i]["name"]."/data.xml");

             $contact = (string) $txt->record->content;

             $sitemap[] = ["link"=>"contact", "priority"=>"1.0"];

      }

      if($info[$i]["name"] == "cv"){

             $txt = simplexml_load_file($path.$info_section_name."/".$info[$i]["name"]."/data.xml");

             $cv = (string) $txt->record->content;

             $sitemap[] = ["link"=>"cv", "priority"=>"1.0"];

       

      }

    }

  }

  //show($cv);



$sitemap_xml = '<?xml version="1.0" encoding="utf-8"?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';

$sitemap_xml .= "\n";



for($i=0;$i<count($sitemap);$i++){

   $sitemap_xml .= " <url>

   \t<loc>http://localhost:8085/pizulli/".$sitemap[$i]["link"]."</loc>
   
   \t<changefreq>monthly</changefreq>
   
   \t<priority>".$sitemap[$i]["priority"]."</priority>
   
   </url>\n";



   /*
$sitemap_xml .= " <url>

\t<loc>https://www.xavieravila.com/".$sitemap[$i]["link"]."</loc>

\t<changefreq>monthly</changefreq>

\t<priority>".$sitemap[$i]["priority"]."</priority>

</url>\n";

*/

}

$sitemap_xml .= '</urlset>';

$filelastmodified = 0;

if (file_exists("sitemap.xml")){

   $filelastmodified = filemtime("sitemap.xml");

}

//24 hours in a day * 3600 seconds per hour

if((time() - $filelastmodified) > 7*24*3600)

{

   file_put_contents("sitemap.xml", $sitemap_xml); 

}









  function getSeo($page){

     global $links, $seo;

     if (isset($seo[$page])){

        return $seo[$page];

     } 

     for($i=0;$i<count($links);$i++){

       if (strtolower($links[$i]["ID"]) == $page){

          return $links[$i]["seo"];

       }

     }

     return $seo;

  }





  function getLink($str){

     $str = strtolower($str);

     $str = str_replace(" ","-",$str);

     return $str;

  }





  function getSection($data,$name){

     for($i=0;$i<count($data);$i++){

        if ($data[$i]["name"]==$name) {

	   $data = $data[$i]["sections"];

	   break;

        }

     }

     return $data;

  }



  function getSectionInfo($data,$name){

     for($i=0;$i<count($data);$i++){

        if ($data[$i]["name"]==$name) {

	   $data = $data[$i];

	   break;

        }

     }

     return $data;

  }



  function getContent($data,$name){

     for($i=0;$i<count($data);$i++){

        if ($data[$i]["name"]==$name) {

	   $data = $data[$i]["content"];

        }

     }

     $arr = array();

     for($i=0;$i<count($data);$i++){

        if($data[$i]["status"]){

	   $arr[] = $data[$i]["name"];

	}

     }

     return $arr;

  }



  function getContentInfo($data,$name){

     for($i=0;$i<count($data);$i++){

        if ($data[$i]["name"]==$name) {

	   $data = $data[$i]["content"];

        }

     }

     $arr = array();

     for($i=0;$i<count($data);$i++){

        if($data[$i]["status"]){

	   $arr[] = $data[$i];

	}

     }

     return $arr;

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



function isImage($filePath){

   $type = substr($filePath,strrpos($filePath,'.')+0);

   

   return $type=='.jpg' ? true : false;

}



function show($obj){

   echo "<pre>";var_dump($obj);echo "</pre>";

}



?>