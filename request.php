<?php



$fullname = addslashes($_POST["fullname"]);

$email = addslashes($_POST["email"]);

$message = addslashes($_POST["message"]);

$p = addslashes($_POST["portfolio"]);

$i = addslashes($_POST["image"]);

$n = addslashes($_POST["image_num"]);





file_put_contents("log.txt", $fullname." ".$email." ".$message);

    /* Для отправки HTML-почты вы можете установить шапку Content-type. */

    $headers= "MIME-Version: 1.0\r\n";

    $headers .= "Content-type: text/html; charset=utf-8\r\n";

    
    $hostname = $_SERVER['http://localhost:8085/']
    //$hostname = $_SERVER['SERVER_NAME'];

    //$hostname = str_replace("www.","",$hostname);

    

    /* дополнительные шапки */

    $headers .= "From: no-reply <no-reply@".$hostname.">\r\n";

    $headers .= "Reply-To: no-reply <no-reply@".$hostname.">\r\n";

    //$headers .= 'Cc: info@adaveo.com' . "\r\n";

    

    /* и теперь отправим из */

    //echo $headers;

    //echo $message;

    $to = 'ivan@ihousedesign.com';

    //$to = 'arkady.hramov@ihousedesign.com';

    //$to = 'test-6t5wy@mail-tester.com';

    //$to = 'archi.khramov@gmail.com';

    $to = 'ultra798@gmail.com';

    $subject = 'Request from localhost:8085/pizulli'.$p.'/#'.$n;
    //$subject = 'Request from xavieravila.com/'.$p.'/#'.$n;

    $body = $fullname."<br><br>\n".$email."<br><br>\n".$message;



    $res = mail($to, $subject, $body, $headers);

    var_dump($res);

?>