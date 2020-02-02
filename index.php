<?php

ini_set('display_errors', true);error_reporting(E_ALL ^ E_NOTICE);


//for the href tag 
$folder = '';

$ver = time();

$module = "main";

$item = false;



include("data.php");



if (isset($_GET["module"])){

  $module = addslashes($_GET["module"]);

  

  $tmp = explode("/", $module);

  if (count($tmp)>1){

     $module = $tmp[0];

     $item = $tmp[1];

     if ($item='') {$item = false;}

  } 



  if (validModule($module, $links)) {

    //ok

  } else {

   $module = "main";

  }

} else {

  $module = "main";

}







echo "<!--\n";

echo $_GET["module"];

echo "\n";

echo $module;

echo "\n";

echo $item;

echo "\n";

echo "\n";

echo "-->";







$section = 0;

$portfolio = $links[0]["link"];



if ($module=='contact' || $module=='cv'){

  $portfolio = '';

}



foreach($links as $key=>$link){

   if ($module==$link["link"]){

      $section = $key; 

      $portfolio = $module;

      $module = "portfolio";

   }

}



$_seo = getSeo($module);

//var_dump($_seo);die;



include("header.php");



switch($module) {

    case "main":

      include("portfolio.php");

    break;

    case "portfolio":

      include("portfolio.php");

    break;

    case "contact":

       include("contact.php");

    break;

    case "cv":

       include("cv.php");

    break;

    

}





include("footer.php");







function validModule($module, $links) {



  $modules = [];

  $modules[] = "contact";

  $modules[] = "cv";

  foreach($links as $link){

   $modules[] = $link["link"];

  }



  return in_array($module, $modules);



}



?>