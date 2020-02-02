<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=$_seo["title"]?></title>
    <meta name="description" CONTENT="<?=$_seo["description"]?>">

<link href="<?=$folder?>main.css?<?=$ver?>" rel="stylesheet">


</head>
<script>
  var module = '<?=$module?>';
  var portfolio = '<?=$portfolio?>';
  var count = '<?=count($links[$section]["images"])?>';
  </script>
<body>
<div class="wrapper">
 <div class="menu">
   <div class="logo">
     <a href="">
      <img src="assets/images/01_logo_main.svg">
     </a>
   </div>
   <div class="nav">
    <p>hi there pink</p>
     <?php foreach($links as $link){?>
     <div class="nav_item"><a href="<?=$link["link"]?>" class="<?=($portfolio==$link["link"] ? "active" : "") ?>"">
     <?=$link["name"]?></a></div>
     <?php } ?>
     <div class="separator separator_left">
     </div>
     <div class="nav_item"><a href="contact" class="<?=($module=="contact" ? "active" : "") ?>"">contact</a></div>
     <div class="nav_item"><a href="cv" class="<?=($module=="cv" ? "active" : "") ?>"">cv</a></div>

   </div>
 </div>

 <div class="mobile_burger"></div>
    <div class="mobile_menu">
      <div class="mobile_menu_close"></div>
      <div class="mobile_menu_content">
      <?php foreach($links as $link){?>
        <div class="nav_item"><a href="<?=$link["link"]?>" class="<?=($portfolio==$link["link"] ? "active" : "") ?>"">
        <?=$link["name"]?></a></div>
        <?php } ?>
        <div class="mobile_separator">
        </div>
        <div class="nav_item"><a href="contact" class="<?=($module=="contact" ? "active" : "") ?>"">contact</a></div>
        <div class="nav_item"><a href="cv" class="<?=($module=="cv" ? "active" : "") ?>"">cv</a></div>

      </div>
    </div>

 <div class="content">
  <div class="container">
    <div class="mobile_header">
     <a href="/2020/">
      <img class="mobile_img" src="assets/images/05_mobile_logo.svg">
     </a>
    </div>