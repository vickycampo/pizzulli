<?php


include("config.php");
include("settings.php");

session_start();

include("utils/checkuser.php");


$module = addslashes($_GET["module"]);

if ($module == "forcelogout") {
    unset($_SESSION['login']);
    unset($_SESSION['user']);

    $_SESSION = [];
    unlockCms();
    header('Location: '.BASE_PATH.'/login.php');
    die;
}


$check = checkUser("start",$settings["logout_time"]);
    //var_dump($check); die;
	
if (!$check["result"]){
  include("locked.php");
  die;
}


function addTime($action){
}

$ver = "?".time();


$log = fopen("logs/_log.txt", "a+");
fwrite($log, "module=".$module."\n");
fwrite($log, "path=".dirname($_SERVER['SCRIPT_NAME']) .'/'."\n");
fclose($log);

if ($module == "login") {
   header('Location: '.BASE_PATH.'/login.php');
   die;
}
if ($module == "logout") {
    unset($_SESSION['login']);
    unset($_SESSION['user']);

    $_SESSION = [];
    unlockCms();
    header('Location: '.BASE_PATH.'/login.php');
    die;
}


if (!isset($_SESSION['login'])) {
    header('Location: '.BASE_PATH.'/login.php'.'?module='.$module);
    die;
}

lockCms("start");


$options = [];
$options[] = 'video';
$options[] = 'images';

if ($settings["large_vert_image_height"]>0) {
    $options[] = 'largeimages';
}
if ($settings["large_vert_image_height"]>0) {
    $options[] = 'smallimages';
}
if ($settings["smallthumbnail_width"]>0 || $settings["smallthumbnail_height"]>0) {
    $options[] = 'smallthumbnails';
}
$options[] = 'thumbnails';




?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="<?=BASE_PATH?>/css/main.css<?=$ver?>">
    <link rel="stylesheet" href="<?=BASE_PATH?>/css/holder.css<?=$ver?>">
    <link rel="stylesheet" href="<?=BASE_PATH?>/css/viewer.css<?=$ver?>">
    <link rel="stylesheet" href="<?=BASE_PATH?>/css/news.css<?=$ver?>">
    <link rel="stylesheet" href="<?=BASE_PATH?>/css/fileicon.css<?=$ver?>">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="<?=BASE_PATH?>/js/lazyload.min.js?1"></script>
    <script type="text/javascript" src="<?=BASE_PATH?>/text_editor/tinymce/js/tinymce/jquery.tinymce.min.js"></script>


    <script src="<?=BASE_PATH?>/js/vimeo/player.js"></script>
    <script src="<?=BASE_PATH?>/js/iframe2image.withdomvas.js"></script>



    <script src="<?=BASE_PATH?>/assets/bootstrap/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="<?=BASE_PATH?>/assets/popper.min.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="<?=BASE_PATH?>/css/jquery-confirm.min.css">
    <script src="<?=BASE_PATH?>/js/jquery-confirm.min.js"></script>



    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote-lite.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote-lite.js"></script>


    <script>
        window.base_path = '<?=BASE_PATH?>/';
        window.cms_path = '<?=CMS_PATH?>/';
        window.dir = '/';
        window.data_folder = '<?=DATA_FOLDER?>/';
        window.apiURL = base_path + 'api.php';
        window.originals_folder = '<?=ORIGINALS_FOLDER?>';

        window.module_path = '<?=$module?>';
        if (module_path.length) {
          window.module = module_path.split("/");
        } else {
          window.module = [];
        }
        var $viewer = {};
        var $main = {};
        var $settings = {};
        <?php
          foreach($settings as $key=>$value){
            if ($key!='admin_value') {
              echo '$settings["'.$key.'"] = "'.addslashes($value).'"'.";\n";
            }
          }
        ?>
    </script>
    <script src="<?=BASE_PATH?>/js/main.js<?=$ver?>"></script>
    <script src="<?=BASE_PATH?>/js/viewer.js<?=$ver?>"></script>


    <script src="<?=BASE_PATH?>/js/cms.js<?=$ver?>"></script>
</head>
<body>

<div id="shadow">
</div>


<div id="confirmation" class="confirmation">
  <div class="confirmation_bg">
  </div>
  <div class="confirmation_center">
    <div class="confirmation_message">
      <div class="confirmation_text">
        Are you sure?
      </div>
      <div class="confirmation_line">
      </div>
      <div class="confirmation_buttons">
        <a href="#" id="btnYes">YES</a>&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;<a href="#" id="btnNo">NO</a>
      </div>
    </div>
  </div>
</div>


<div id="selection"></div>

<div id="add_vimeo_window">
  <div class="add_vimeo_window_bg">
  </div>
  <div class="add_vimeo_window_center">
   FILE NAME:<br>
   <input type="text" class="add_vimeo_input" id="vimeo_name"><br><br>
   VIMEO LINK:<br>
   <input type="text" class="add_vimeo_input" id="vimeo_link"><br><br>
   PREVIEW:<br>
   <div class="vimeo_preview" id="vimeo_preview">
   </div>
   <div id="vimeo_cancel_btn"><a href="#">CANCEL</a></div>
   <div id="vimeo_add_btn"><a href="#">ADD VIMEO</a></div>
  </div>
</div>


<div id="lock">
    <div class="lock_close"><a href="#" id="close_lock">&nbsp;&nbsp;</a></div>
    <div class="lock_bg"></div>
    <div class="lock_info"></div>
</div>

<div id="loading">
    <div class="loading_bg"></div>
    <div class="loading_debug"></div>
    <div class="loader loader--style1" title="0">
        <svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
           width="40px" height="40px" viewBox="0 0 40 40" enable-background="new 0 0 40 40" xml:space="preserve">
          <path opacity="0.2" fill="#000" d="M20.201,5.169c-8.254,0-14.946,6.692-14.946,14.946c0,8.255,6.692,14.946,14.946,14.946
            s14.946-6.691,14.946-14.946C35.146,11.861,28.455,5.169,20.201,5.169z M20.201,31.749c-6.425,0-11.634-5.208-11.634-11.634
            c0-6.425,5.209-11.634,11.634-11.634c6.425,0,11.633,5.209,11.633,11.634C31.834,26.541,26.626,31.749,20.201,31.749z"/>
          <path fill="#000" d="M26.013,10.047l1.654-2.866c-2.198-1.272-4.743-2.012-7.466-2.012h0v3.312h0
            C22.32,8.481,24.301,9.057,26.013,10.047z">
            <animateTransform attributeType="xml"
              attributeName="transform"
              type="rotate"
              from="0 20 20"
              to="360 20 20"
              dur="0.5s"
              repeatCount="indefinite"/>
            </path>
          </svg>
    </div>
</div>

<div id="uploader_progress">
</div>

<div id="section_settings">
 <div class="section_settings_block">
   <input type="hidden" id="section_id" value="">
   SECTION NAME:<br>
   <input type="text" id="section_name" class="section_settings_input">
   <div class="height30"></div>
   <a href="#" id="show_preview_btn">Show section preview</a>
   <input type="hidden" id="show_preview" name="show_preview" value="">
   <div class="height30"></div>
   <span id="describe_label1"></span>
   <input type="text" id="section_descr1" class="section_settings_input">
   <br><br>
   <span id="describe_label2"></span>
   <input type="text" id="section_descr2" class="section_settings_input">
   <br><br>
   <span id="describe_label3"></span>
   <input type="text" id="section_descr3" class="section_settings_input">
   <br><br>
   <span id="describe_label4"></span>
   <input type="text" id="section_descr4" class="section_settings_input">
   <div class="height30"></div>
   META - HTML TITLE<br>
   <input type="text" id="meta_title" class="section_settings_input">
   <br><br>
   META - HTML DESCRIPTION<br>
   <textarea id="meta_description" class="section_settings_textarea" rows="5"></textarea>
   <br><br>
   META - KEYWORDS<br>
   <textarea id="meta_keywords" class="section_settings_textarea" rows="5"></textarea>
   <br><br>
   <div id="ga_holder">
   GOOGLE ANALYTICS<br>
   <textarea id="ga" class="section_settings_textarea" rows="5"></textarea>
   <br><br>
   </div>
   <div id="gpixel_holder">
   GOOGLE PIXEL<br>
   <textarea id="gpixel" class="section_settings_textarea" rows="5"></textarea>
   <br><br>
   </div>
   <div id="fbpixel_holder">
   FACEBOOK PIXEL<br>
   <textarea id="fbpixel" class="section_settings_textarea" rows="5"></textarea>
   <br><br>
   </div>
   <div class="duplicate">
   <a href="#" id="duplicate">&gt; DUPLICATE STRUCTURE</a><br><br>
   This will duplicate folder
   </div>
   <div class="duplicate">
   <a href="#" id="duplicatewithdata">&gt; DUPLICATE STRUCTURE+DATA</a><br><br>
   This will duplicate folder with all content
   </div>

 </div>
 <!--<div class="section_settings_cancel">CANCEL</div>-->
 <div class="section_settings_save">SAVE AND CLOSE</div>
</div>

<div id="move_options">
 <div class="move_options_block">
  <div class="move_options_info"><div class="move_options_infotext"></div></div>
  <div class="move_options_info1"></div>
  <div class="move_options_to"></div>

 </div>
 <div class="move_options_cancel">CANCEL</div>
</div>

<div id="file_settings">
 <div class="file_settings_block">
   <input type="hidden" id="file" value="">
   FILE NAME:<br>
   <!--<input type="text" id="file_name" class="section_settings_input" disabled>-->
   <span id="file_name"></span>
   <div class="height30"></div>
   
   <?php echo $settings["caption1"]=='' ? 'CAPTION 1' : $settings["caption1"];?>:<br>
   <input type="text" id="file_caption1" class="section_settings_input">
   <br><br>
   <?php echo $settings["caption2"]=='' ? 'CAPTION 2' : $settings["caption2"];?>:<br>
   <input type="text" id="file_caption2" class="section_settings_input">
   <br><br>
   <?php echo $settings["caption3"]=='' ? 'CAPTION 3' : $settings["caption3"];?>:<br>
   <input type="text" id="file_caption3" class="section_settings_input">
   <br><br>
   <?php echo $settings["caption4"]=='' ? 'CAPTION 4' : $settings["caption4"];?>:<br>
   <input type="text" id="file_caption4" class="section_settings_input">
   <div class="height30"></div>
   TEXT<br>
   <textarea id="file_text" class="section_settings_textarea tinymce" rows="10"></textarea>
   <br><br>

 </div>
 <!--<div class="file_settings_cancel">CANCEL</div>-->
 <div class="file_settings_save">SAVE AND CLOSE</div>
 <div class="file_settings_navigation"><a href="#" id="s_prev">PREV</a>&nbsp;&nbsp;<span id="settings_counter"></span>&nbsp;&nbsp;<a href="#" id="s_next">NEXT</a></div>

</div>

<div id="content">
    <div id="scrollableContent" class="selectable-scrollable">
      <div id="paddingContent"></div>
    </div>
    
</div>

<div id="header">
    <div class="header1">
        <div id="header_left">
            <div id="header_left_path" class="header_elements_margin">
            </div>
        </div>
        <div id="header_right">
            <div id="header_logout" class="header_elements_margin">
                <a href="<?=BASE_PATH?>/logout">LOG&nbsp;OUT</a>
            </div>
            <div id="header_capacity" class="header_elements_margin">
                --- USED
            </div>
            <div id="header_user" class="header_elements_margin">
                My IP: <?=$_SERVER['REMOTE_ADDR']?>
            </div>
        </div>
    </div>
    <div class="header2">
        <div id="header_left">
            <div id="header_left_path" class="header_elements_margin">
                <a href="#" onclick="openSettings();return false;">SETTINGS</a>
            </div>
        </div>
        <div id="header_right">
            <div id="header_logout" class="header_elements_margin">
                <a href="#" class="update_link">UPDATE WEBSITE</a>
            </div>
          <?php if ($settings["menu_btn2_name"]!=''){ ?>
            <div id="header_btn1" class="header_elements_margin">
                <a href="<?=$settings["menu_btn2_link"]?>" class="" target="_blank"><?=$settings["menu_btn2_name"]?></a>
            </div>
          <?php } ?>
          <?php if ($settings["menu_btn1_name"]!=''){ ?>
            <div id="header_btn1" class="header_elements_margin">
                <a href="<?=$settings["menu_btn1_link"]?>" class="" target="_blank"><?=$settings["menu_btn1_name"]?></a>
            </div>
          <?php } ?>

        </div>
    </div>

</div>

<div id="footer">
    <div id="footer_left">
        <div class="footer_left_input footer_elements_margin">
            <span class="before_input">ADD&nbsp;MEDIA&nbsp;SECTION</span><input id="new_media_section" data-type="portfolio" class="new_section" type="text" value="" placeholder="ENTER NAME AND CLICK ENTER">
        </div>
        <div class=" footer_left_input footer_elements_margin">
            <span class="before_input">ADD&nbsp;TEXT&nbsp;SECTION</span><input id="new_text_section" data-type="text" class="new_section" type="text" value="" placeholder="ENTER NAME AND CLICK ENTER">
        </div>
        <div class=" footer_left_input footer_elements_margin">
            <span class="before_input">ADD&nbsp;NEWS&nbsp;SECTION</span><input id="new_news_section" data-type="news" class="new_section" type="text" value="" placeholder="ENTER NAME AND CLICK ENTER">
        </div>
        <div id="uploader">
            <input type="file" id="fileElem" multiple accept="image/*, video/*, audio/*, .svg" style="display:none" onchange="handleFiles(this.files)">
            <input type="file" id="oneFileElem" accept="image/*, video/*, audio/*, .svg" style="display:none" onchange="handleFiles(this.files)">
            <a href="#" id="fileSelect">UPLOADER</a>
        </div>
        <div id="add_vimeo">
            <a href="#" id="add_vimeo_btn"><?php if (!$vimeo) { } else { echo "ADD VIMEO";}?></a>
        </div>
    </div>
</div>


<!--------NEWS ---------->
<div id="news_content">
    <div id="news_scrollableContent" class="selectable-scrollable">
      <div id="news_paddingContent"></div>
    </div>
</div>

<div id="news_header">
    <div class="news_header1">
        <div id="header_left">
            <div id="news_left_path" class="header_elements_margin">
            </div>
        </div>
        <div id="header_right">
            <div id="news_update_and_close" class="header_elements_margin bold_link">
                <a href="#">UPDATE & CLOSE</a>
            </div>
            <div class="header_elements_margin">
               &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
            </div>
            <div id="news_save_and_close" class="header_elements_margin bold_link">
                <a href="#">SAVE & CLOSE</a>               
            </div>
        </div>
    </div>
</div>
<!--------NEWS ---------->


<div id="screenshot">
  <div id="screenshot_image">
  </div>
  <a href="#" title="cancel" id="crop_cancel">CANCEL</a>
  <a href="#" title="accept" id="crop_accept">ACCEPT</a>
</div>


<div id="viewer">
    <div class="viewer_bg"></div>
    <div class="viewer_content">
     <div class="viewer_window">
       <div class="viewer_content_size">
       </div>
       <div class="viewer_content_cropper">
       </div>
       <div class="viewer_content_media">
       </div>
     </div>
    </div>
    <div class="viewer_upline"></div>
    <div class="viewer_downline"></div>
    <div class="viewer_controls">

      <select id="content_type">
         <?php
             foreach($options as $option) {

                echo '<option value="'.$option.'">';
                if ($option!='video'){
                  echo substr($option,0,strlen($option)-1);
                } else {
                  echo $option;
                }
                echo '</option>';
             }

             if ($settings["cofile_enabled"]==1){ 
                echo '<option value="cofile">cofile</option>';
             }
         ?>
      </select>
      <span class="fa_space"></span>
      <a href="#" id="crop" class="fa_space" title="select image version for cropping">CROP THUMBNAIL</a>
      <a href="#" id="create_thumb" title="create thumbnail">CREATE THUMBNAIL</a>
      <span class="fa_space"></span>

    </div>
    <div class="viewer_content_name"></div>
    <!--<div class="viewer_content_caption"></div>-->

    <div class="viewer_controls_right">
       <?php if ($settings["cofile_enabled"]==1){ ?>
       <a href="#" id="cofile" title="download">UPLOAD COFILE</a>
       <span class="fa_space"></span>
       <?php } ?> 
       <a href="#" id="download" title="download">DOWNLOAD</a>
       <span class="fa_space"></span>
       <a href="#" id="open_original" title="open original">OPEN ORIGINAL</a>
       <span class="fa_space"></span>
      <a href="#" id="replace" title="replace">REPLACE ORIGINAL</a>
      <span class="fa_space"></span>
       <a href="#" id="file_settings_link" title="settings">SETTINGS</a> 
       <span class="fa_space"></span>
       <a href="#" id="close_viewer" title="back">CLOSE</a>
    </div>
    <div class="viwer_navigation"><a href="#" id="prev">PREV</a>&nbsp;&nbsp;<span id="counter"></span>&nbsp;&nbsp;<a href="#" id="next">NEXT</a></div>
</div>

<div id="avatar"><!--<img src="img/folder.png" width="50" height="50">--></div>

<div id="idiv" style="background-color: #ff0000;position:absolute;z-index:50;left:0px;top:0px;width:100%;height:100%;display:none;background-color:#FFFFFF;" align=center border=0>
    <div id="editor_top" style="width:100%;height:50px;text-align:right" align="right">
      <div style="padding: 20px;">
        <a href="#" class="link update_link">UPDATE WEBSITE</a>&nbsp;&nbsp;&nbsp;
        <a href="#" onClick="closeInfo();return false;" class="link">CLOSE</a>&nbsp;
      </div>
    </div>
    <div id="editor" style="width:80%;height:80%;" align="center"></div>
    <div id="editor_controls" style="width:80%;height:10%;" align="center"></div>
 
</div>

<div id="sdiv" style="background-color: #ff0000;position:absolute;z-index:51;left:0px;top:0px;width:100%;height:100%;display:none;background-color:#FFFFFF;overflow:scroll;" align=center border=0>
  <div id="sdiv_inside" style="padding: 20px;">
  </div>
</div>


</body>
</html>