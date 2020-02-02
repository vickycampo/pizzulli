(function() {

    'use strict';

    var image = '';
    var image_num = '';
    
    $(document).ready(function () {
      $("img.lazy").lazyload({
         effect : "fadeIn"
       });
       $('.popup_close').on('click', closePopup);
       $('.popup_back').on('click', closePopup);
       $('.inquire').on('click', openPopup);
       $('#message').focus(textIn);
       $('input').focus(inputIn);
       $('#send').focus(submitForm);
       if (module=='main'){}
          $('.menu').animate({'opacity':1});
          $('.content').delay(500).animate({'opacity':1});
       $('.singleVideo__playBtn').on('click',playVideo);

       if (location.hash){
          console.log("pic"+(location.hash).replace("#",""));
         scroll_to_elem("pic"+(location.hash).replace("#",""),1000);
       }

       $('.mobile_burger').on('click', openMenu);
       $('.mobile_menu_close').on('click', closeMenu);

   });
})();

function openMenu() {
   //console.log"openMenu");
   
   $('.mobile_menu').animate({top: 0}, 500);
   $('.mobile_burger').stop().animate({opacity: 0}, 500, function(){$(this).hide();});
}
function closeMenu() {
   $('.mobile_menu').animate({top: -1*($('.mobile_menu').height()+100)},500);
   $('.mobile_burger').stop().show().fadeTo(500, 1);
}

   
function playVideo(e){
   $(e.target).hide();
   var obj = $(e.target).closest('.video').find('video')
   obj.prop("controls",true); 
   obj.trigger('play');
}

var error_text = 'required field';
var error_text1 = 'not a valid e-mail address';
var old_email = '';

function submitForm(e){
   
   var res = true;
   var fullname = $('#fullname').val().trim();
   var email = $('#email').val().trim();
   var message = $('#message').val().trim();
   if (fullname=='') {$('#fullname').val(error_text).addClass('error');res = false;}
   if (email=='') {$('#email').val(error_text).addClass('error');res = false;}
   if (email!='' && !looksLikeMail(email)) {old_email = email; $('#email').val(error_text1).addClass('error');res = false;}
   if (message=='' || message=='Message') {$('#message').val(error_text).addClass('error');res = false;}
   if (!res) return false;
   $('.popup_shadow').show();
   $('.popup_loader').show();
   $.post( "request.php", { fullname: fullname, email: email, message: message, portfolio: portfolio, image: image, image_num:image_num })
  .done(function( data ) {
    $('.popup_shadow').hide();
    $('.popup_loader').hide();
    $('#message').val('Message').removeClass('normal');
    $('#fullname').val('');
    $('#email').val('');
    closePopup();
  });
}

function looksLikeMail(str) {
   var lastAtPos = str.lastIndexOf('@');
   var lastDotPos = str.lastIndexOf('.');
   return (lastAtPos < lastDotPos && lastAtPos > 0 && str.indexOf('@@') == -1 && lastDotPos > 2 && (str.length - lastDotPos) > 2);
}

function closePopup(e){

   $('#popup').fadeOut();
}
function openPopup(e){
   image = $(e.target).data('image');
   image_num = $(e.target).data('key');
   $('#popup').fadeIn();
}

function textIn(e){
   if ($('#message').text()=='Message' || $('#message').text()==error_text) $('#message').val('');
   $('#message').removeClass('error').addClass('normal');
}
function inputIn(e){
   if ($(e.target).val()==error_text) $(e.target).val('');
   if ($(e.target).val()==error_text1) {$(e.target).val(old_email);}

   $(e.target).removeClass('error');
}

function scroll_to_elem(elem,speed) {
   
	if(document.getElementById(elem)) {
               
                t = 150;

		var destination = jQuery('#'+elem).offset().top-t;
	        console.log("destination = "+destination);
		jQuery("html,body").animate({scrollTop: destination}, speed, null ,fineScroll());
	}
}

var tempScrollTop,currentScrollTop = 0;
var way = "";
   $(window).scroll(function(){ 
     var st = $(document).scrollTop();
 	   currentScrollTop = $(window).scrollTop();
        
            if (tempScrollTop < currentScrollTop)
           { 
               way= "down"; // крутнули вниз колесо
               checkScrollDown();
           } 
           else if (tempScrollTop > currentScrollTop)
            {
                way= "up"; // крутнули вверх колесо
                checkScrollUp();
            }
            
            tempScrollTop = currentScrollTop;
            
            //console.log(way);
   });

function checkScrollUp(){
   for(i=count;i>0;i--){
    
     t = 150;
     var destination = jQuery('#pic'+(i)).offset().top-t;
     
     if(tempScrollTop>(destination)){
       
       setHash(i,count);
       i = 0;
     }
  }
}
function checkScrollDown(){


   for(i=count;i>0;i--){
   
    
     t = 150;
     
     var destination = $('#pic'+(i)).offset().top-t;
     
     if(tempScrollTop>=(destination)){
       
       setHash(i,count);
       i = 0;
     }
  }
}
function fineScroll(){

  setTimeout(checkScrollDown,1500);
}

var current_pic = 1;
function setHash(i,count){
  if(i!=current_pic){
    current_pic = i;    
  }else{
    return;
  }
  
  location.hash = i;
  
}
