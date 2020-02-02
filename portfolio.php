<?php if (isset($links[$section]["description"])){ ?>
<div class="description">
  <?=($links[$section]["description"])?>
</div>
<?php } ?>


<div class="media">
  <?php foreach($links[$section]["images"] as $key=>$image) { 
    if (isImage($image)) { ?>
      <div><img id="pic<?=$key+1?>" class="nolazy" src="<?=($links[$section]["path"].$image)?>" alt=""></div>
  <?php
  } else { ?>
    <div class="video">
      <video id="pic<?=$key+1?>"
      poster="" playsinline="" controlslist="nodownload">
      <source src="<?=($links[$section]["path"].$image)?>" type="video/mp4">
  </video>
      <button type="button" class="singleVideo__playBtn">
         <img src="assets/images/big_play_button.svg" alt="" class="singleVideo__playBtnImg">
      </button>
    </div>
    <?php
  }
  
  if ($links[$section]["inquire"]){ ?>
    <div class="separator separator_center"></div>
    <div class="inquire"><a href="#" data-id="" class="inquire_me"  data-image="<?=$image?>" data-key="<?=$key?>" onclick="return false;">inquire</a></div>
  <?php } ?>
    <div class="space"></div>
  <?php } ?>
</div>

<div class="mobile_menu_double">
<div class="mobile_menu_double_content">
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

<div id="popup">
  <div class="popup_back"></div>
  
    <div class="popup_inner">
    <div class="popup_shadow"></div>
    <div class="popup_loader"></div>
      <div class="popup_close"></div>
      <div class="form">
        <div class="input">
          <input type="text" id="fullname" placeholder="Fullname">
        </div>
        <div class="input">
          <input type="text" id="email" placeholder="Email">
        </div>
        <div class="textarea">
          <textarea id="message" name="body">Message</textarea>
        </div>
        <div class="submit"><a id="send" href="#" onclick="return false;">&gt; Send</a></div>
      </div>
    </div>
<div>