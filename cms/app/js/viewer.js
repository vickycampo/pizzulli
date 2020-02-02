                                                        
(function() {

    'use strict';

    $(document).ready(function () {
        var wh = $(window).height();
        var ww = $(window).width();
        var current_file = '';
        var current_path = '';
        var current_type = '';
        var current_content_type = '';
        var current_pos = 0;
        var current_caption = '';
        var vimeo_id = '';
        var count = 0;
 	var image_h = 0;
  	var image_w = 0;
 	var video_h = 480;
  	var video_w = 640;
  	var crop_tool = '';
  	var crop_x0 = 0;
  	var crop_y0 = 0;
  	var crop_x1 = 0;
  	var crop_y1 = 0;
  	var crop_x2 = 0;
  	var crop_y2 = 0;
        var crop_ratio = false;
        var ih_current = 0;
        var iw_current = 0;
        var last_content = '';
        var ver = time();
        var dataURL = '';

        $( window ).resize(function() {
             resizeTextEditor();
             resizeAll();
        });

        $('#content_type').on('change', function(e) {
           current_content_type = $(this).val();
           showFile(current_path, current_file, current_pos, 0, count, current_caption, null);
           if (current_content_type!='originals') {
              $('#crop').html('CROP '+current_content_type.substr(0,current_content_type.length-1).toUpperCase());
           }
        });


        function reloadFile(file) {
           ver = time();
           current_file = file;
           showFile(current_path, current_file, current_pos, 0, count, current_caption, null);
           $main.need_content_reload = true;

        }

        function getCurrentFile() {
            return current_file;
        }

        function getCurrentPos() {
            return current_pos;
        }

        function removeCrop() {
            console.log("removeCrop");
            $('.viewer_content_cropper').empty();
            $('#content_type').removeAttr('disabled');
            $('#content_type').removeClass('disabled');
            if (crop_tool!=''){
               current_content_type = last_content;
            }
            if (current_content_type!='originals') {
              $('#crop').html('CROP '+current_content_type.substr(0,current_content_type.length-1).toUpperCase());
            }
            
            crop_tool = '';
        }

        function calc2(){
            if (crop_ratio) {
              var dx = Math.abs(crop_x2 - crop_x1);
              var alfa = (crop_y2 - crop_y1)/Math.abs((crop_y2 - crop_y1));
              crop_y2 = crop_y1 + alfa*dx/crop_ratio;
            }

            if (crop_y2 > ih_current) {
              crop_y2 = ih_current;
              var dy = Math.abs(crop_y2 - crop_y1);
              var alfa = (crop_x2 - crop_x1)/Math.abs((crop_x2 - crop_x1));
              crop_x2 = crop_x1 + alfa*dy*crop_ratio;
            }
            if (crop_y2 < 0) {        
              crop_y2 = 0;
              var dy = Math.abs(crop_y2 - crop_y1);
              var alfa = (crop_x2 - crop_x1)/Math.abs((crop_x2 - crop_x1));
              crop_x2 = crop_x1 + alfa*dy*crop_ratio;
            }

        }

        function checkCrop(){
            if (crop_x1 < 0) {crop_x1 = 0;}
            if (crop_y1 < 0) {crop_y1 = 0;}
            if (crop_x2 < 0) {crop_x2 = 0;}
            if (crop_y2 < 0) {crop_y2 = 0;}
            if (crop_x2 > iw_current) {crop_x2 = iw_current;}
            if (crop_y2 > ih_current) {crop_y2 = ih_current;}
            if (crop_x1 > iw_current) {crop_x1 = iw_current;}
            if (crop_y1 > ih_current) {crop_y1 = ih_current;}
        }

        function clickCrop() {


           last_content = $('#content_type').val();
            $('#content_type').addClass('disabled');
           $('#content_type').attr('disabled','disabled');
           $('#crop').html('CANCEL');

           current_content_type = 'originals';

           showFile(current_path, current_file, current_pos, 0, count, current_caption, initCrop)
        }

        function initCrop() {
           
            crop_y0 = $('.viewer_content_cropper').offset().top;
            crop_x0 = $('.viewer_content_cropper').offset().left;
            
            if (crop_tool=="thumbnails") {
                if (parseInt($settings["thumbnail_height"])>0 && parseInt($settings["thumbnail_width"])>0) {
                    crop_ratio = parseInt($settings["thumbnail_width"]) / parseInt($settings["thumbnail_height"]);
                }
            } else if (crop_tool=="smallthumbnails") {
                if (parseInt($settings["smallthumbnail_height"])>0 && parseInt($settings["smallthumbnail_width"])>0) {
                    crop_ratio = parseInt($settings["smallthumbnail_width"]) / parseInt($settings["smallthumbnail_height"]);
                }
            } else {
                crop_ratio = false;
            }

            console.log("initCrop "+crop_ratio);


            crop_x2 = iw_current/2;
            crop_y2 = ih_current/2;
            checkCrop();
            calc2();

            showField(crop_x1,crop_y1,crop_x2,crop_y2);
            initResizers();
           
            /*
            $(document).on('mousedown', function(e){
                 crop_x1 = crop_x2 = e.pageX - crop_x0;
                 crop_y1 = crop_y2 = e.pageY - crop_y0;
                 checkCrop();

                 $(document).on('mousemove', function(e){
                     crop_x2 = e.pageX - crop_x0;
                     crop_y2 = e.pageY - crop_y0;

                     checkCrop();
                     calc2();

                     showField('move', crop_x1,crop_y1,crop_x2,crop_y2);
                 });
            });
            

            $(document).on('mouseup', function(e){
                 crop_x2 = e.pageX - crop_x0;
                 crop_y2 = e.pageY - crop_y0;

                 checkCrop();
                 calc2();

                 showField('stop', crop_x1,crop_y1,crop_x2,crop_y2);

                 $(document).unbind('mousemove');
            });
            */
        }

        function doCrop(){
                var coords = {};
                coords["x1"] = crop_x1 ? iw_current/crop_x1 : 0;
                coords["x2"] = iw_current/crop_x2;
                coords["y1"] = crop_y1 ? ih_current/crop_y1 : 0;
                coords["y2"] = ih_current/crop_y2;
                $main.getData({action : 'cropImage', id : $main.getCurrentFolderId(), name: current_file, coords: coords, target: crop_tool}, $viewer.cropDone, "", 0);
                $('.viewer_window').stop().animate({ opacity: 0 }, 300);
        }


        $(document).on('keydown', function(e){
            
            if (e.key == 'Enter' && crop_tool!='') {
                
              doCrop();  
            }
            switch (event.key) {
                case "ArrowLeft":
                    // Left pressed
                    prev_obj();
                    break;
                case "ArrowRight":
                    // Right pressed
                    next_obj();
                    break;
                case "ArrowUp":
                    // Up pressed
                    break;
                case "ArrowDown":
                    // Down pressed
                    break;
            }
        });


         function initResizers(){   
            $(document).on('keydown', function(e){
               //console.log(e.keyCode);
            });


             $('.cropper_center').unbind('dblclick').on('dblclick', function(e){
                 
                 doCrop();  

             });

             $('.cropper_angle').on('mousedown', function(e){
                    

                var id = $(this).attr('id');

                if (id=='a0') {
                  var mouse_x0 = e.pageX - crop_x0;
                  var mouse_y0 = e.pageY - crop_y0;
                  var dx = mouse_x0 - crop_x1;
                  var dy = mouse_y0 - crop_y1;

                  var crop_h = crop_y2 - crop_y1;
                  var crop_w = crop_x2 - crop_x1;
                }
                
                $(document).on('mousemove', function(e){

                     if (id=='a0') {
                        mouse_x0 = e.pageX - crop_x0;
                        mouse_y0 = e.pageY - crop_y0;

                        crop_x1 = mouse_x0 - dx;
                        crop_y1 = mouse_y0 - dy;
                        crop_x2 = crop_x1 + crop_w;
                        crop_y2 = crop_y1 + crop_h;

                     }
                     if (id=='a1') {
                       crop_x1 = e.pageX - crop_x0;
                       crop_y1 = e.pageY - crop_y0;
                     }
                     if (id=='a2') {
                       crop_x2 = e.pageX - crop_x0;
                       crop_y2 = e.pageY - crop_y0;
                     }
                     if (id=='a3') {
                       crop_x2 = e.pageX - crop_x0;
                       crop_y1 = e.pageY - crop_y0;
                     }
                     if (id=='a4') {
                       crop_x1 = e.pageX - crop_x0;
                       crop_y2 = e.pageY - crop_y0;
                     }
                     checkCrop();
                     calc2();

                     showField(crop_x1,crop_y1,crop_x2,crop_y2);


                });

                $(document).unbind('mouseup').on('mouseup', function(e){
                    $(document).unbind('mousemove');
                    initResizers();
                });
            });

         }


        function storeVideoSize(file, video_h, video_w){
             $main.getData({action : 'storeVideoSize', id : $main.getCurrentFolderId(), name: file, width: video_w, height: video_h}, null, '', 1);
     
        }

        function time(){
           return parseInt(new Date().getTime()/1000)
        }

        function cropDone(data){
           ver = time();
           removeCrop();
           $('#crop_action').text('');
           $('#content_type').removeAttr('disabled');
           $('#content_type').val(last_content);

           current_content_type = last_content;
           showFile(current_path, current_file, current_pos, 0, count, current_caption, null);
           $main.need_thumb_reload = true;

        }

        $viewer.cropDone = cropDone;
        $viewer.getCropTool = getCropTool;
        $viewer.getCurrentFile = getCurrentFile;
        $viewer.getCurrentPos = getCurrentPos;
        $viewer.reloadFile = reloadFile;
        $viewer.hideCurrent = hideCurrent;


        function getCropTool(){
            return crop_tool;
        }
 
        function showField( _x1,_y1,_x2,_y2){
           var x1 = _x1; var x2 = _x2;
           if (_x1>_x2) {x1 = _x2; x2 = _x1;}

           var y1 = _y1; var y2 = _y2;
           if (_y1>_y2) {y1 = _y2; y2 = _y1;}

           $('.viewer_content_cropper').empty();

           if (y1==y2 || x1==x2) {
               return;
           }

           var w = $('.viewer_content_media').width();
           var h = $('.viewer_content_media').height();

           $('<div class="cropper_inner_shadow"></div>').css({'top':y1,'left':x1}).width(x2-x1).height(y2-y1).appendTo('.viewer_content_cropper'); 

           $('<div class="cropper_shadow"></div>').css({'top':0,'left':0}).width(w).height(y1).appendTo('.viewer_content_cropper'); 
           $('<div class="cropper_shadow"></div>').css({'top':y1,'left':0}).width(x1).height(y2-y1).appendTo('.viewer_content_cropper'); 
           $('<div class="cropper_shadow"></div>').css({'top':y1,'left':x2}).width(w-x2).height(y2-y1).appendTo('.viewer_content_cropper'); 
           $('<div class="cropper_shadow"></div>').css({'top':y2,'left':0}).width(w).height(h-y2).appendTo('.viewer_content_cropper'); 
           $('<div id="a0" class="cropper_center cropper_angle"></div>').css({'top':y1+5,'left':x1+5}).width(x2-x1-10).height(y2-y1-10).appendTo('.viewer_content_cropper'); 
           var ratio_h = image_h/ih_current;
           var ratio_w = image_w/iw_current;

           //$('<div id="a_debug" class="cropper_center">'+x1+'&nbsp;/&nbsp;'+y1+'<br>'+x2+'&nbsp;/&nbsp;'+y2+'<br>W: '+(x2-x1)*ratio_w+'<br>H: '+(y2-y1)*ratio_h+'</div>').css({'top':y1+5,'left':x1+5}).appendTo('.viewer_content_cropper'); 
           $('<div id="a_debug" class="cropper_center">W:&nbsp;'+Math.round((x2-x1)*ratio_w)+'<br>H:&nbsp;'+Math.round((y2-y1)*ratio_h)+'</div>').css({'top':y1+5,'left':x1+5}).appendTo('.viewer_content_cropper'); 

           $('<div id="a1" class="cropper_angle"></div>').css({'top':y1-5,'left':x1-5}).appendTo('.viewer_content_cropper'); 
           $('<div id="a2" class="cropper_angle"></div>').css({'top':y2-5,'left':x2-5}).appendTo('.viewer_content_cropper'); 
           $('<div id="a3" class="cropper_angle"></div>').css({'top':y1-5,'left':x2-5}).appendTo('.viewer_content_cropper'); 
           $('<div id="a4" class="cropper_angle"></div>').css({'top':y2-5,'left':x1-5}).appendTo('.viewer_content_cropper'); 
        }



  	function updateFile(data) {
            $('.viewer_content_caption').html(data["caption"]);
        }

        function addNoImageMessage(){ 
            console.log("addEmptyMessage");
            var div = document.createElement('div');
            div.className = "empty";
            div.innerHTML = '<div class="no_image">NO IMAGE</div>';
            $('.viewer_content_media').empty().append(div);
            $('.viewer_window').stop().animate({ opacity: 1 }, 300);
        }

        function showFile(path, file, pos, _vimeo_id, _count, caption, callback){
            console.log(file);
            console.log(path);
            //console.log("vimeo id "+_vimeo_id);
            console.log("current_content_type="+current_content_type);
            current_file = file;
            current_path = path;
            current_pos = pos;
            current_caption = caption;
            vimeo_id = _vimeo_id;

            count = _count;
            $('.viewer_content_name').html(file);
            $('.viewer_content_caption').html(caption);

            $('#counter').html((pos+1)+' / '+count);

            $("#open_original").unbind('click').on('click',function(){
               if (current_type == 'image'){

                   var arr_other = [];
                   if ($settings["allowed_other"]){
                      arr_other = ($settings["allowed_other"]).split(" ");
                   }
                   console.log("current_file "+current_file);
                   if (arr_other.indexOf(getExt(current_file))==-1){
                      window.open(current_path + originals_folder + current_file);
                   } else {
                      window.open(current_path + '/images/' + current_file);
                   }
               } else {
                  window.open(current_path + '/images/' + current_file);
                 //openVideo('../../../'+current_path+'/images/', current_file, video_w, video_h,'video');return;
               }
            });

            $("#download").unbind('click').on('click',function(){
                var window_location = '';
                if (current_content_type=='video') {
                    window_location = base_path+"utils/download.php?path="+current_path + "/images/" + current_file;
                } else {
                    window_location = base_path+"utils/download.php?path="+current_path + originals_folder + current_file;
                }
                window_location = window_location.split(" ").join("%20");
                console.log(window_location);
                window.location = window_location;
            });

            wh = $(window).height();
            ww = $(window).width();
            

            if (isImage(file)) {
                //$('#content_type option[value="video"]').attr('disabled','disabled');
                $('#content_type option[value="video"]').remove();

                if (current_content_type=='video') {
                   current_content_type = 'images';
                }

                if (current_content_type=='') {
                   current_content_type = 'images';
                }

                if (current_content_type!='originals') {
                   $('#crop').html('CROP '+current_content_type.substr(0,current_content_type.length-1).toUpperCase());
                }

                current_type = 'image';
                var img = new Image();
                
                img.onerror = function(){
                   addNoImageMessage();
                };
                
                img.onload = function(){
                  image_h = img.height;
                  image_w = img.width;
                  console.log("onload "+image_h+"x"+image_w);

                  resizeImage(img);
                 
                  $('.viewer_content_media').html(img);
                  $('.viewer_window').stop().animate({ opacity: 1 }, 300);

                  if(callback) {
                     callback();
                  }
                };

                if (current_content_type== 'originals') {
                    img.src = path + originals_folder + current_file+"?"+ver;
                } else if (current_content_type== 'images'){
                    img.src = path+'/'+current_content_type+'/'+current_file+"?"+ver;
                } else {
                    img.src = path+'/'+current_content_type+'/'+getJpg(current_file)+"?"+ver;
                }

                showFileWeight(file, img.src);


            } else {
                //$('#content_type option[value="video"]').removeAttr('disabled');
                if ($('#content_type option[value="video"]').length==0){
		  $('#content_type').append($("<option></option>").attr("value",'video').text('video'));
		}

                if (current_content_type=='') {
                   current_content_type = 'video';
                }

                current_type = 'video';

                if (current_content_type == 'video') {
                  
                  if (vimeo_id!='undefined' && vimeo_id!=0){
                      var str = '<div id="video_holder">'
                      + '<iframe src="https://player.vimeo.com/video/'+vimeo_id+'?color=f7e9f8&title=0&byline=0&portrait=0" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>'
                      +'</div>';
                      $('.viewer_content_media').html(str);
                      $('.viewer_window').stop().animate({ opacity: 1 }, 300);

                  } else {

		      var str = '<div id="video_holder"><video id="videoobj" class="video-js" controls preload="auto" width="640" height="480"'
                      + 'poster="" data-setup="{}">'
		      + '<source src="'+path+'/images/'+file+'?u" type="video/mp4">'
		      +' </video></div>';
                      $('.viewer_content_media').html(str);
		        var vid = document.getElementById("videoobj");
		        vid.onloadeddata = function() {
  			video_h = this.videoHeight;
  			video_w = this.videoWidth;
  			storeVideoSize(file, video_h, video_w);
  		    
  			$(this).attr('orig-width',video_w);
  			$(this).attr('orig-height',video_h);
			setVideoSize();
                            $('.viewer_content_size').html(video_w+' x '+video_h);
                            $('.viewer_window').stop().animate({ opacity: 1 }, 300);
		        };
                        showFileWeight(file, path+'/images/'+file);

		  }
		} else {
                    
                    var img = new Image();
                    img.onerror = function(){
                        addNoImageMessage();
                    }

                    img.onload = function(){
                      image_h = img.height;
                      image_w = img.width;
                      resizeImage(img);
                    
                      $('.viewer_content_media').html(img);
                      
                      $('.viewer_window').stop().animate({ opacity: 1 }, 300);
                    };
                    
                    img.src = path+'/'+current_content_type+'/'+getJpg(file);

                    if (current_content_type== 'originals') {
                        img.src = path + originals_folder + getJpg(file)+"?"+ver;
                    } else {
                        img.src = path+'/'+current_content_type+'/'+getJpg(file)+"?"+ver;
                    }
                    showFileWeight(file, img.src);
                }

            
	    }

	    if (current_content_type!='originals'){
               $('#content_type').val(current_content_type);
            }
	    initHandlers();
        }

        function showFileWeight(name, imgUrl){
               var bytes = getImageSizeInBytes(imgUrl);
                  
               $('.viewer_content_name').html(name+"&nbsp;&nbsp;&nbsp;&nbsp;("+formatFileSize(bytes,1)+")");
        }

        function formatFileSize(bytes,decimalPoint) {
           if(bytes === false) return '';
           if(bytes == 0) return '0 Bytes';
           var k = 1000,
               dm = decimalPoint || 2,
               sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
	       i = Math.floor(Math.log(bytes) / Math.log(k));
	       if (i<3) dm = 0;
	       var b = parseFloat((bytes / Math.pow(k, i)).toFixed(dm));

	       if (i == 1){
                  b = b - b%25 + 25;
	       }
	   return b + ' ' + sizes[i];
	}
        
        function getImageSizeInBytes(imgURL) {
            var request = new XMLHttpRequest();
            request.open("HEAD", imgURL, false);
            request.send(null);
            if(request.status==200){
              var headerText = request.getAllResponseHeaders();
	      var re = /Content\-Length\s*:\s*(\d+)/i;
	      re.exec(headerText);
	      return parseInt(RegExp.$1);
            } else {
               return false;
            }
        }
        
	function resizeAll(e) {
                     wh = $(window).height();
             ww = $(window).width();

             if (current_content_type=='video') {

                setVideoSize();

             } else {
                
                var img = $('.viewer_content_media').find('img');
                resizeImage(img.get(0));
             }
        }

        function resizeVideo() {
            //console.log("resizeVideo");
        }

        function resizeImage(img) {
            //console.log("resizeImage");
            //console.log(img);

            if (!img) return;
            var wratio = ww/wh;
            var ih = image_h;
            var iw = image_w;
            var iratio = 1;

                  iratio = iw/ih;


                  if (ih > (wh - 150)){
                    img.height = wh - 150;
                    img.width = img.height*iratio;
                  } else {
                    img.height = image_h;
                    img.width = image_h*iratio;

                  } 

                  if (img.width > ww) {
                    img.width = ww;
                    img.height = img.width/iratio;
                  }
                  ih_current = img.height;
                  iw_current = img.width;



                  $('.viewer_content_size').html(iw+' x '+ih);
                  

        }


        function disableCrop(el) {
              //$(el).unbind('click').css({'opacity':.3, 'cursor': 'default'}).attr('disabled','disabled');
              $(el).hide();
        }


        function showBtn(el) {
                  //$(el).css({'opacity':1, 'cursor': 'pointer'}).removeAttr('disabled');
                  $(el).show();
                  $(el).unbind('click').on('click', function(){  
                     drawImage();return;
                  });
        }
        function hideBtn(el) {
              //$(el).unbind('click').css({'opacity':.3, 'cursor': 'default'}).attr('disabled','disabled');
              $(el).hide();
        }


        function enableCrop(el) {
            //$(el).css({'opacity':1, 'cursor': 'pointer'}).removeAttr('disabled');
            $(el).show();
            $(el).unbind('click').on('click', function(){  
              console.log($('#crop').html());


              if ($('#crop').html()=='CANCEL'){
                cropDone(null);
                return;
              }

               removeCrop();
               crop_tool = $('#content_type').val();
               clickCrop();                     	
                    
            });
        }

        function initHandlers(){
           $('#crop').unbind('click');

           if (current_type=='video') {

              if (current_content_type=='video') {
                 showBtn('#create_thumb');
                 disableCrop('#crop');
              } else {
                 enableCrop('#crop');
                 hideBtn('#create_thumb');
              }
           } else {
                   var arr_other = [];
                   if ($settings["allowed_other"]){
                      arr_other = ($settings["allowed_other"]).split(" ");
                   }

              hideBtn('#create_thumb');
              if (arr_other.indexOf(getExt(current_file))==-1){
                 enableCrop('#crop');
              } else {
                 disableCrop('#crop');
              }
           }


           $('#prev').unbind('click');
              $('#prev').on('click', function(e){
                 e.preventDefault();
                 prev_obj()
                 return;

              });

           $('#next').unbind('click');
              $('#next').on('click', function(e){  
                 e.preventDefault();
                 next_obj();
                 return;
              });
           
        }

        function prev_obj(){
                 removeCrop();
                 $('.viewer_window').stop().animate({ opacity: 0 }, 300);
                 $main.navigation("prev",current_pos);
        }
        function next_obj(){
                 removeCrop();
                 $('.viewer_window').stop().animate({ opacity: 0 }, 300);
                 $main.navigation("next",current_pos);
        }

        function hideCurrent(){
                 $('.viewer_window').stop().animate({ opacity: 0 }, 300);
        }

        
        
        
        function drawImage() {

                     
          var elem = 'videoobj';
          var canvas = document.createElement('canvas');
          var context = canvas.getContext('2d');
          var width = $('#'+elem).outerWidth();
          var height = $('#'+elem).outerHeight();
          canvas.width = width;  canvas.height = height;
          
          var elemVideo = document.getElementById(elem);
          console.log(width+' '+height);
          
          context.drawImage(elemVideo, 0, 0, width, height);
          dataURL = canvas.toDataURL();
        
          var image = new Image();
          image.src = dataURL;
        
          $('#screenshot').css({'display': 'flex'});
          $('#screenshot_image').html('').append(image);
          $('#screenshot').animate({opacity:1}, 200);

          $('#crop_accept').unbind('click').on('click', acceptScreenshot);
          $('#crop_cancel').unbind('click').on('click', hideScreenshot);
          return;
         /*
         iframe2image($("div#video_holder").get(0), function (err, img) {
         if (err) { return console.error(err); }
 
          context.drawImage(img, 0, 0, width, height);
          var dataURL = canvas.toDataURL();
        
          var image = new Image();
          image.src = dataURL;
        
          $('#screenshot').html('').css({'display': 'block'}).append(image).animate({opacity:1}, 200); 
         });
          */



        }

        function acceptScreenshot(){
          showLoader();

          $.ajax({
             type: "POST",
             url: apiURL,
             data: { 
                 action: "screenshot",
                 file: current_file,
                 id: $main.getCurrentFolderId(),
                 imgBase64: dataURL
             }
          }).done(function(response) {
              
               try {
                   var res = JSON.parse(response);
               } catch (err) {
                   
                   $main.showLock(err);
                   return false;
               }

                
              if (res.status == 'ok') {
                 ver = time();
                 hideScreenshot();
                 hideLoader();
              } else if (res.status == 'error') {
                 $main.showLock(res.error);
              } else {
                 $main.showLock(res+'');
              }
          })
          .fail(function(xhr, status, error) {
              $main.showLock(status+' '+error);
          });

        }

        function hideScreenshot(){
            $('#screenshot').animate({opacity:0}, 200, function(){$(this).hide();}); 
            $main.need_thumb_reload = true;
        }

        function setVideoSize(){
         
              var bh = $("body").height() - 150;
              var bw = $("body").width();
       
              var holder = $("#video_holder");
              var video = $("#videoobj");
              var vw = video.attr('orig-width');
              var vh = video.attr('orig-height');
              var ratio = vw/vh;

              if(vh > bh){
                  vh = bh;
                  vw = vh*ratio;
              }
       
              if(vw!=0 && vh!=0){
       
                 video.width(vw+'px');
                 video.height(vh+'px');
                 holder.width(vw+'px');
                 holder.height(vh+'px');
       
              }
    
       }


        $viewer.showFile = showFile;
        $viewer.updateFile = updateFile;
        $viewer.clear = clear;
        $viewer.removeCrop = removeCrop;

        function clear() {
            removeCrop();
            current_content_type = '';
            $('.viewer_content_media').html('');
        }
        
        function getJpg(filename) {
            var nm = filename.split('.').shift();
            return nm+".jpg";
        }

        function isImage(filename) {
            var images = ['jpg', 'gif', 'png', 'svg'];
            var ext = filename.split('.').pop();
            return images.indexOf(ext)!=-1;
        }

        function getExt(filename) {
            var images = ['jpg', 'gif', 'png', 'svg'];
            var ext = filename.split('.').pop();
            return ext;
        }
        

    });

})();