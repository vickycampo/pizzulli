<?php

include("config.php");

include("settings.php");



session_cache_expire(30);

session_start();



ini_set('allow_url_fopen',1);



if (DEVELOPING) {

    error_reporting(E_ERROR);ini_set('display_errors','On');

}

    //error_reporting(E_ALL);ini_set('display_errors','On');





if (isset($_SESSION['login']) && $_SESSION['login']) {

   

    header('Location: '.BASE_PATH);

    die;

}



function checkDomain() {    

        //$allow = simplexml_load_file("http://www.ihousedesign.com/authorize/cms_allow.xml?".time());
        $allow = simplexml_load_file("local.xml");
        echo ("<pre>");
        print_r($allow );
        echo ("</pre>");
        if ($allow===false) {return true;}

        foreach($allow->site as $site){

          $domain = $_SERVER['SERVER_NAME'];

          $allowed1 = str_replace("http://","",$site->attributes()->name);

          $allowed2 = str_replace("www.","",$allowed1);

          echo ($_SERVER['SERVER_NAME']." ".$site->attributes()->name.""."<br>");

          if ($domain == $allowed1 || $domain == $allowed2) {

             return true;

          }

        }

        return false;

}





$error = '';



if ($_POST['password']) {

  

    if (psw($_POST['password']) == $settings['admin_password']) {





    if (checkDomain()){



            $_SESSION['login'] = 1;

            $_SESSION['user'] = $_POST['login'];

            $adds = '';

            if (isset($_POST["module"])){

               $adds = "/".$_POST["module"];

            }

           

            header('Location: '.BASE_PATH.$adds);

            die;

        } else {

            include("errors.php");

            $error = ERROR19;

        }



    } else {

        include("errors.php");

        $error = ERROR20;



        if (isset($_SESSION['attemps'])){

            $_SESSION['attemps']++;

        } else {

            $_SESSION['attemps'] = 1;

        }

    }

}



function psw($a) {



  if($a){

     for($i=0;$i<strlen($a);$i++){

        $b[$i] = substr($a,$i,1);

     }

     for($i=0;$i<count($b);$i++){

        $c[$i] = chr(ord($b[$i])+1);

     }

     return implode("",$c);

 }

}

?>

<html>

<head>

    <style>

        html, body {

            font-family: Arial;

            font-size: 12px;

        }

        .login_form {

            position: absolute;

            top: 50%;

            left: 50%;

            margin-left: -125px;

            margin-top: -30px;



        }



        .login_form_input {

            background-color: #bcbcbc;

            color: #fff;

            border: none;

            width: 250px;

            height: 18px;

            padding: 4px;

            margin-bottom: 10px;

            font-family: Arial;

            font-size: 12px;



        }

        .login_form_submit {

            background-color: #bcbcbc;

            color: #fff;

            border: none;

            width: 250px;

            height: 18px;

            text-align: left;

            font-family: Arial;

            font-size: 12px;



        }

        .version {

            color: #bbb;

            height: 20px;

        }

        .error {

            color: #ff0000;

        }

    </style>

</head>

<body>

<form action="" method="POST">

<div class="login_form">

    <input type="hidden" name="module" value="<?=$_GET["module"]?>">

    <div class="version">cms v0.2 <span class="error"><?=$error?></span></div>

    <!--<input type="text" class="login_form_input" placeholder="" name="login" autocomplete="on" value="">

    <br>-->

    <input type="hidden" name="login" value="user">

    <input type="password" class="login_form_input" placeholder="ENTER PASSWORD" name="password" autocomplete="on">

    <br>

    <input type="submit" class="login_form_submit" value="&lt; ENTER">

</div>

</form>

</body>

</html>