                                            
(function() {

    'use strict';

    $(document).ready(function () {
        console.log("ready");
        var debug = true;

        var main_holder = $('#paddingContent');
        var news_holder = $('#news_paddingContent');
        var path_holder = $('#header_left_path');
        var debug_obj = $('.loading_debug');
        var dragObjects = {};
        var zindex = 0;
        var selected = [];
        var firstSelected = '';
        var lastSelected = '';
        var dragging = false;
        var current_folder_id = -1;
        var news_folder_id = -1;
        var current_news_section = "";
        var current_folder_type = "";
        var current_path = '';
        var content = [];
        var state = [];
        var current_holder = '';
        var hour = (new Date()).getMinutes();
        var news = [];
        var news_scroll_dy = 0;
        var newsScrollInt = 0;
        var holder_scroll_top = 0;
        var news_scroll_top = 0;
        var drag_position = 0;
        var upload_action = '';
        var selectX = 0;
        var selectY = 0;
        var selectXX = 0;
        var selectYY = 0;
        var viewer_on = false;
        var is_instagram_grid = 0;
        var gsize = 148;

        initPage();

        refreshContent();

        var sess_int = setInterval(checkSession,1000*60);

        chechBackup();	

        getSize();
        
        function getCurrentFolderId() {
            return current_folder_id; 
        }
        function getNewsFolderId() {
            return news_folder_id; 
        }

        $main.getCurrentFolderId = getCurrentFolderId;
        $main.getNewsFolderId = getNewsFolderId;
        $main.need_thumb_reload = false;
        $main.need_content_reload = false;
        $main.getData = getData;
        $main.viewer_on = viewer_on;

        $('#close_lock').bind('click', hideLock);
        $('#close_viewer').bind('click', hideViewer);
        $('#close_viewer1').bind('click', hideViewer);
        $('#replace').bind('click', replaceContent);
                                           
        $('#new_media_section').keydown(createNewSection);
        $('#new_text_section').keydown(createNewSection);
        $('#new_news_section').keydown(createNewSection);

        function checkSession(){
            
            getData({action : 'session'}, null, "", 1);
        }

        function refreshContent(){
            getData({action : 'getContent', id : current_folder_id}, showContent, "", 0);
        }

        $('.update_link').on('click', function(){
            $(this).text("UPDATING...");
            updateWebsite(current_folder_id);
        });

        function updateWebsite(folder_id){

           $.post(base_path+"utils/updatewebsite.php", {action:"update", folder_id: folder_id},function(data) {
             $('.update_link').text("UPDATE WEBSITE");
             if (debug) console.log(data);
           } );
        
        }

        function refreshNews(){
            if (debug) console.log("refreshNews");
            getData({action : 'getNews', id : news_folder_id}, showNews, "", 0);
        }

        function chechBackup(){
		$.get( base_path+"utils/utils.php", function( data ) {
		     
		});
        }

        function getSize(){
		$.get( base_path+"utils/size.php?backups_count="+$settings["backup_count"], function( data ) {
		    var val = JSON.parse(data).size;
		    $('#header_capacity').html(val+" USED"); 
		});
        }

        function encodeName($nm){
         var $new = '';
         var $code = '';
	
	 for(var $i=0;$i<($nm).length;$i++){
	     //echo $nm[$i]." ".ord($nm[$i])."<br>";
	     $code = ($nm[$i]).charCodeAt(0);
             if (($code>=48 && $code<=57) || ($code>=65 && $code<=90) || ($code>=97 && $code<=122) || $code==32 || $code==38 || $code==95 || $code==45 || $code==33 || $code==63){
                $new += $nm[$i];
             }else{
                $new += "["+$code+"]";
             }
	 } 
	 return $new;
	}
	
        function decodeName($nm){
        	var $nm1="";
        	var $z = $nm.split("[");
        	$nm1 = $z[0];
		var $w;
		for(var $i=1;$i<($z).length;$i++){
			$w = ($z[$i]).split("]");
			$nm1 +=  String.fromCharCode($w[0])+$w[1];
        	}
		return $nm1;
	}


        function createNewSection(e){
            
             if(e.keyCode==13){
                  var type = $(e.target).data("type");
                  var name = $(e.target).val().trim();
                  if(name=='') return;
                  name = encodeName(name);
                  $(e.target).val('');                                                                                    //showNewFolder
                  getData({action : 'createFolder', name : name, type: type, parent_id: current_folder_id}, refreshContent, "", 0);

             }
        }
        

        function hideLock(e){
            $('#lock').stop().animate({opacity:0}, 300, null, function(){$('#lock').css('display','none');});

        }

        function pathClick(id){
            
            main_holder.empty();
            current_folder_id = id;
            getData({action : 'getContent', id : id}, showContent, "", 0);
        }

        $('#add_vimeo_btn').on('click', function(e){
             $('#add_vimeo_window').show();
             $('#vimeo_preview').empty();
             $('.add_vimeo_input').val('');
             initVimeoHandlers();
        });

        $('#vimeo_cancel_btn').on('click', function(e){
             $('#add_vimeo_window').hide();
        });

        $('#vimeo_add_btn').on('click', function(e){
             var name = $('#vimeo_name').val().trim();
             var link = $('#vimeo_link').val().trim();
             if (name=='' || link==''){return;}

             getData({action: 'addVimeo', id: current_folder_id, name: name, link: link}, refreshContent, "", 0);

             $('#add_vimeo_window').hide();
        });


        function initVimeoHandlers(){
          
           $('#vimeo_link').on('change, input', function(e){
              
                var options = {
                    url: $(this).val().trim(),
                    width: 360,
                    loop: true
                };
            
                var player = new Vimeo.Player('vimeo_preview', options);
            
                player.setVolume(0);
            
                player.on('play', function() {
                    console.log('played the video!');
                });
           });
        }

        function isInstagramGrid(name){

            var tmp = [];
            if ($settings["instagram_grid"]){
              tmp = ($settings["instagram_grid"]).split(",");
              for (var i = 0; i < tmp.length; i++) {
		     tmp[i] = tmp[i].trim()
              }
            }
            console.log(tmp);
            console.log(name);
            return tmp.includes(name);
        }

        function showContent(result) { 
            if (debug) console.log("showContent");        
            main_holder.empty();
            var path = result['cms_path'];
            current_folder_type = "portfolio";

            current_path = path;

            if (current_folder_id==-1){
                $('#fileSelect').addClass('not_active');
                $('#add_vimeo_btn').addClass('not_active');
            } else {
                $('#fileSelect').removeClass('not_active');
                $('#add_vimeo_btn').removeClass('not_active');
            }

            for (var i = 1; i < result['section'].length; i++) {
            //for (var key in result.section) {
                path += "/" + result['section'][i]['name'];
            }
            //for (var i = 0; i < result['content'].length; i++) {
            //console.log("l="+result['content'].length);
            var tmp = result['section'];
            var section = tmp[tmp.length - 1];

            
            if (isInstagramGrid(section["name"])){
               $('#paddingContent').addClass('content_instagram');
               is_instagram_grid = 1;
            }else{
               $('#paddingContent').removeClass('content_instagram');
               is_instagram_grid = 0;
            }

            if (debug) console.log("showContent "+path);

            content =  result.content;

            for (var key in result.content) {
                
                addItem(result['content'][key], key, path);
            }
            if (result.content.length==0 && current_folder_id!=-1) {
               addEmptyMessage();
            }

            //lazyload();
            $("img.lazy").lazyload(); 
            initHolderHandlers();
            
            setPath(result['section']);
             
            if (module.length){
              
               var name = module.shift();
               
               getData({action: 'getFolderId', parent_id: current_folder_id, name: name}, openContent, "", 0);

            }
        }

        function openContent(data){
            
            if(data.status == 'error') {

              clearInterval(sess_int);
              showLock(data.error);

            } else if(data.status == 'ok') {

               if (data.section.type == 'text') {
               
                   //setPath(data['section']);

                   setTimeout(function(){
                       setTextPath(data.section.name);
                       openInfo(getShortPath() + data.section.name+'/data');
                   }, 2000);

               } else  if (data.section.type == 'news') {
                   news_folder_id = data.section.id;
                   setTextPath(holder.data('name'));
                   getData({action: 'getNews', id: news_folder_id}, showNews, "", 0);
               
               } else {
                   current_folder_id = data.section.id; 
                   getData({action: 'getContent', id: data.section.id}, showContent, "", 0);
               
               }
           }
        }



        function hideMainBlock() {
            $('#header').fadeOut();
            $('#content').fadeOut();
            $('#footer').fadeOut();
        }

        function showMainBlock() {
            $('#header').fadeIn();
            $('#content').fadeIn();
            $('#footer').fadeIn();
        }

        function hideNewsBlock() {
            $('.note-popover').hide();
            $('#news_header').fadeOut();
            $('#news_content').fadeOut();
            news_folder_id = -1;
        }

        function showNewsBlock() {
            $('#news_header').fadeIn();
            $('#news_content').fadeIn();
            $('#news_save_and_close').unbind('click').on('click', function() {
                hideNewsBlock();
                showMainBlock();
            });
            $('#news_update_and_close').unbind('click').on('click', function() {
                updateWebsite(news_folder_id);
                hideNewsBlock();
                showMainBlock();

            });
        }

  // ----------------------------------------------
        function showNews(result) {  

            hideMainBlock();       
            showNewsBlock();       

            var path = result['cms_path'];
            current_folder_type = "news";
            news = [];

            current_path = path;

            for (var i = 1; i < result['section'].length; i++) {
                path += "/" + result['section'][i]['name'];
            }
            current_news_section =  result['section'].pop()['name'];
            $('#news_left_path').html(current_news_section);  


            var news_xml = result['content'];
           
            var xmlDoc = $.parseXML(news_xml);
	    var xml  = $( xmlDoc );

            var media = [];

            $(xml).find("news").each(function () {
              media = [];

              $(this).find("media").find("item").each(function () {
                 
                  media.push({name:$(this).attr('name'),
                              width:$(this).attr('width'),
                              height:$(this).attr('height'),
                              thumbwidth:$(this).attr('thumbwidth'),
                              thumbheight:$(this).attr('thumbheight')
                             });
              });

              if ($(this).attr('image')){
                  media.push({name:$(this).attr('image'),
                              width:0,
                              height:0,
                              thumbwidth:0,
                              thumbheight:0
                             });
              }
              var body = '';

              if ($(this).text()) {
                 body = $(this).text().trim();
              } else {
                 body = $(this).find("body").text().trim();
              }
              
              news.push({
                         date:checkDate($(this).attr('date')),
                         header:str_replace('"','&quot;',$(this).attr('header')),
                         body: body,
                         media:media
                       });
              
           });

            buildNews();
            setPath(result['section']);
        }

        function time(){
		return parseInt(new Date().getTime()/1000+ (new Date()).getTimezoneOffset() * 60000);
	}


        function checkDate(str){
           if (str == parseInt(str)) {
              return str;
           }
           return parseInt(new Date(str).getTime() - (new Date()).getTimezoneOffset() * 60000)/1000;// ;

        }

        function timeConverter(UNIX_timestamp){
          var a = (new Date(UNIX_timestamp * 1000 + (new Date()).getTimezoneOffset() * 60000));
          var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
          var year = a.getFullYear();
          //var month = months[a.getMonth()];
          var month = a.getMonth()+1;
          var date = a.getDate();
          var hour = a.getHours();
          var min = a.getMinutes();
          var sec = a.getSeconds();
          month = month>9 ? month : '0'+month;
          date = date>9 ? date : '0'+date;
          var time =  month + '/' + date + '/' + year;
          return time;
        }
        
        function initNews(){
            $( ".datepicker" ).datepicker();

            $('.news_body').on('resize', function(e){
                if (debug) console.log('resize');
            });

            $('.news_input').on('change keyup paste', function(){
                $(this).closest('form').find('.news_update').css({'display':'inline-block'});
                var block = $(this).closest('.news_block');
                var id = parseInt(block.data('id'));

                if (debug) console.log(id+' '+news[id]["date"]+' '+timeConverter(news[id]["date"]));

                news[id]["date"] = checkDate(block.find("input[name=date]").val());

                if (debug) console.log(id+' '+news[id]["date"]+' '+timeConverter(news[id]["date"]));

                if (debug) console.log(news[id]["date"]);
                news[id]["header"] = str_replace('"','&quot;',block.find("input[name=header]").val());
                //news[id]["body"] =  block.find('textarea').summernote('code');
                news[id]["body"] =  block.find('textarea').val();
                console.log(block.find('textarea').val());

            });
            
             
            $('.close_news').on('click', function(e){
               $(this).hide();
                var block = $(this).closest('.news_block');

                block.animate({height : 22}, 300);
                block.find('.news_block_cover').show();
                block.find('.news_block_cover_date').html(block.find("input[name=date]").val()); 
            });

            $('.news_block_cover').on('click', function(e){
               $(this).hide();
                closeOpenNews();

                var block = $(this).closest('.news_block');
                var id = parseInt(block.data('id'));

                var h = adjustBlock(block);
                block.find('.close_news').show();

                //if ($('textarea').tinymce()){
                    //$('textarea').tinymce().remove();
                //}
                
                //initTM(block, id, setNewsBody, h);
                initSN(block, id, setNewsBody, h);
            });
                                            

            function closeOpenNews(){
                $('.close_news:visible').click();
            }

            function setNewsBody(id, data){
                 //console.log($('.close_news:visible').closest('.news_block_inner').find('.news_fake_body'));
                 //$('.close_news:visible').closest('.news_block_inner').find('.news_fake_body').text(data);
                 news[id]["body"] = data;
                 updateNews();
            }

            function openFirst(){
                 $('.news_block[data-id=0]').find('.news_block_cover').click();
            }

            $('#add_news').on('click', function(e){
               e.preventDefault();
               
               news.unshift({
                         date: time(),
                         header: '',
                         body: '',
                         media:[]
                       });
               
               buildNews();
               setTimeout(openFirst,300);
                
            });
            $('.news_header').on('blur', function(e){
                updateNews();

            });
            $('.news_body').on('blur', function(e){
                updateNews();

            });
            $('.news_date').on('change', function(e){
                updateNews();

            });

            $('.add_news_media').on('click', function(e){
               var holder = $(this).closest('.news_block');
               $('.news_block').removeClass('active');
               holder.addClass('active');
               addImageClick(holder);
            });
            
            $('.remove_news').on('click', function(e){
               var holder = $(this).closest('.news_block');
               var date = holder.find("input[name=date]").val();
               var header = holder.find("input[name=header]").val();
               var id = parseInt(holder.data('id'));
                 console.log(id);
                 console.log(news[id]);
               

               confirm('ARE YOU SURE YOU WANT TO DELETE THE NEWS?',//'Delete "'+date+' / ' +header+'"?',
                         function () {
                              news.splice(id, 1);
                              buildNews();
                              updateNews();
                              
                         },
                         function () {
                
                         });

            });

            $('.drag_news').on('dragstart', function() {
                return false;
            });

            $('.drag_news').on('mousedown', function(){
               var block = $(this).closest('.news_block');
               var id = parseInt(block.data('id'));
               var w = block.width()+22;
               block.css({'position':'absolute', 'width': w, 'z-index': 1000, 'opacity':.5});
               clearInterval(newsScrollInt);
               newsScrollInt = setInterval(scrollNews, 20);

                document.onmousemove = function(e) {
                   moveAt(e);

                }

                function scrollNews() {

                   var st = $('#scrollableContent').scrollTop();
                   $('#scrollableContent').scrollTop(st + news_scroll_dy);
                }

                function moveAt(e) {
                   //console.log(e.pageY +' '+$('#paddingContent').scrollTop()+' '+getWinDim().h);
                   block.css({'top': e.pageY});

                   if (e.pageY<100) {
                       news_scroll_dy = -5;
                   } else if (e.pageY>(getWinDim().h-100)){
                       news_scroll_dy = 5;
                   } else {
                       news_scroll_dy = 0;
                   }
                }
                document.onmouseup = function(e) {
                   holder_scroll_top = $('#scrollableContent').scrollTop();
                   news_scroll_top = $('#news_scrollableContent').scrollTop();
                   detectPosition(block);
                   block.css({'position':'relative','z-index': 1, 'opacity':1});
                   document.onmousemove = null;
                   document.onmouseup = null;
                   clearInterval(newsScrollInt);
                }


            });
        }

        function confirm(message, yesCallback, noCallback) {
            $('.confirmation').fadeIn(300);

            $('.confirmation_text').html(message);
            var dialog = $('#modal_dialog').dialog();
        
            $('#btnYes').unbind('click').click(function() {
                console.log('#btnYes');
                $('.confirmation').fadeOut(300);
                yesCallback();
            });
            $('#btnNo').unbind('click').click(function() {
                console.log('#noYes');
                $('.confirmation').fadeOut(300);
                noCallback();
            });
        }


        function detectPosition(block) {
                    
            console.log('dragging '+block.data('id'));

            var aim = block.data('id');
            var target = -1;
            $('.news_block').each(function(index, elem){
               if ($(elem).data('id')!==block.data('id')){

                 if($(elem).offset().top > block.offset().top){
                     
                     target = $(elem).data('id');
                     return false;
                 }
               }
               
            });
            
            console.log('insert on '+target);

            var c = news[aim];

            if (target<aim) {
               news.splice(aim, 1);
            }

            if (target==-1){
                news.push(c);
            }else if (target!=aim){
                news.splice(target, 0, c);
            }

            if (target>aim) {
               news.splice(aim, 1);
            }

            buildNews();

            if (target!=aim){
               updateNews();
            }
        }


        
        function getWinDim() {
		var width = window.innerWidth
		|| document.documentElement.clientWidth
		|| document.body.clientWidth;

		var height = window.innerHeight
		|| document.documentElement.clientHeight
		|| document.body.clientHeight;
		return {"w":width, "h":height};
        }

        function initMediaHandlers(){
            $('.delete_news_image').unbind('click');

            $('.delete_news_image').on('click', function(e){
               var obj = $(this).closest('.news_image_holder');
               var holder = $(this).closest('.news_block');
               var num = parseInt(obj.data('num'));
               var id = parseInt(holder.data('id'));
               
               news[id]["media"].splice(num,1);

               buildMedia(id);
               holder.find('.news_update').css({'display':'inline-block'});
               setTimeout(adjustBlock,100,holder);
               updateNews();
            });

        }

        function buildMedia(id){
            var holder = $('[data-id="'+id+'"]');
          
            
            var images = '';
            for (var i=0;i<news[id]["media"].length;i++){
              images += '<div class="news_image_holder" data-num="'+i+'">';
              images += '<div class="delete_news_image"></div><img src="'+getNewsPath()+'/thumbnails/'+news[id]["media"][i]["name"]+'" width="100"></div>';
            }
            holder.find('.news_media_inner').html(images);

            initMediaHandlers();
        }


        function updateNews(){
                var news_xml = buildXml();
                getData({action : 'updateNews', news_xml: news_xml, id: news_folder_id}, newsUpdated, '', 1);
        }

        function addNewsMedia(data){
             console.log("addNewsMedia()");
             var id = parseInt($('.active').data('id'));
             news[id]["media"].push({name:data.name,
                              width:data.iw,
                              height:data.ih,
                              thumbwidth:data.tw,
                              thumbheight:data.th
                             });
            
             buildMedia(id);
             setTimeout(adjustBlock,500,$('.active'));
             updateNews();
        }


        function addImageClick(obj){
            var input = document.createElement('input');
        
            input.type = "file";
            input.setAttribute("accept", "image/*, video/*, audio/*, .svg");
            input.setAttribute("id", "file_upload");

            $(input).change(function() {
                var file = input.files[0];
                readURL(this, obj);
                upload_action = "addFile";
                handleFiles(input.files);
            });
        
            setTimeout(function(){
                $(input).click();
            },200);
        }
        
        function readURL(input, obj) {
         
          //var obj = input.closest('.news_block');

          if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
             
              var str = '<img src="'+e.target.result+'" width="100">';
              var media_block = obj.find('.news_media_inner');
              media_block.append(str);
              
            }
        
            reader.readAsDataURL(input.files[0]);
          }
        }
        
        function adjustBlock(obj){
                //console.log(obj);
                var block_inner = obj.find('.news_block_inner');
                var min = block_inner.height();
                if (min<280) { min=280;}
                obj.animate({height : min}, 300);
                var h = min - 34 - 42 - 13;
                //obj.find('.news_body').animate({height : h}, 300);
                return h;
        }

        function buildXml(){
            var str = '<data>'+"\n";

            for (var i=0;i<news.length;i++){
                //console.log(i+' '+news[i]["date"]+' '+timeConverter(news[i]["date"]));
            }
            news.sort(sortFunction);

            function sortFunction(a, b) {
                if (a["date"] === b["date"]) {
                    return 0;
                }
                else {
                    return (a["date"] > b["date"]) ? -1 : 1;
                }
            }

            for (var i=0;i<news.length;i++){
                str += '<news date="'+news[i]["date"]+'" header="'+news[i]["header"]+'">'+"\n";
                str += "\t"+'<media>'+"\n";
                   for (var j=0;j<news[i]["media"].length;j++){
                      str += "\t\t"+'<item name="'+news[i]["media"][j]["name"]+'" width="'+news[i]["media"][j]["width"]+'" height="'+news[i]["media"][j]["height"]+'" thumbwidth="'+news[i]["media"][j]["thumbwidth"]+'" thumbheight="'+news[i]["media"][j]["thumbheight"]+'"/>'+"\n";
                   }
                str += "\t"+'</media>'+"\n";
                str += "\t"+'<body><![CDATA[';
                str += news[i]["body"];
                str += ']]></body>'+"\n";
                str += '</news>'+"\n";
 
            }
            str += '</data>';
            return str;
        }

        function newsUpdated(data){
            if (debug) console.log("newsUpdated()");
        }

        function buildNews(){
            clearNews();
            var add_str = '<div style="display:table; width: 800px;margin:auto"><div class="button" id="add_news"><div>ADD NEWS</div></div>&nbsp;&nbsp;&nbsp;&nbsp;NEWS COUNT: '+news.length+'</div>';
            news_holder.append(add_str);

            for (var i=0;i<news.length;i++){
                addNews(i, news[i], current_path);
            }
            initNews();

            if (news_scroll_top>0) {
                $('#news_scrollableContent').scrollTop(news_scroll_top);
                news_scroll_top = 0;
            }
        }

        function clearNews(){
           news_holder.empty();
        }


        function formatDate(date) {
          var monthNames = [
            "January", "February", "March",
            "April", "May", "June", "July",
            "August", "September", "October",
            "November", "December"
          ];
        
          var day = date.getDate();
          var monthIndex = date.getMonth()+1;
          var year = date.getFullYear();
                
          //return day + ' ' + monthNames[monthIndex] + ' ' + year;
          return (monthIndex<10 ? "0"+monthIndex : monthIndex) + '/' + day + '/' +  year;
        }

        
        function addNews(id, item, path){
          var fake = '<div class="news_block_cover"><div class="news_block_cover_date">'+timeConverter(item["date"])+'</div><div class="news_block_cover_header">'+item["header"]+'</div></div>';
          var str = '<div class="news_block_holder"><div class="news_block" data-id="'+id+'">'+fake+'<div class="news_block_inner">'+
                       '<form>'+
                       '<div class="news_title"><input name="date" type="text" class="datepicker news_date news_input" value="' + timeConverter(item["date"]) + '">'+
                       '<input type="text" name="header" placeholder="Header" class="news_header news_input" value="' + item["header"] + '">'+
                       '<div class="remove_news"></div>'+
                       '<div class="drag_news_div"><a href="#" class="drag_news"><i class="fa fa-exchange rotate90"></i></a></div>'+
                       '<div class="close_news"></div>'+
                       '</div>'+
                       '<div class="news_media">'+
                       '<div class="news_media_inner">'+
                       //images+
                       '</div>'+
                       '<div class="news_controls">'+
                       '    <div class="add_news_media button-sm">ADD MEDIA</div>'+
                       '</div>'+
                       '</div>'+
                       '<div class="news_body"><textarea class="news_input news_body news_fake_body" name="body" rows="5">'+
                       item["body"]+
                       '</textarea>'+
                       '</div>'+
                       //'<div style="clear:both"></div>'+
                       //'<div class="news_controls news_update">'+
                       //'    <a href="#" class="button-sm button-red">UPDATE NEWS</a><br>'+
                       //'</div>'+
                       '</form>'+
                    '</div>'+
                    '</div><div class="news_block_movepointer">'+
                    '</div>';
          news_holder.append(str);

          buildMedia(id);
        }
        //-----------------------------------------------------------------------------------------
        function showLock(error){
            $('#lock').stop().css('display','block').animate({opacity:1}, 300);
            $('.lock_info').html(error);
        }
        $main.showLock = showLock;


        function getData(post_data, callback, params, in_background){

            if (post_data.action!='session'){
                console.log("getData "+post_data.action+' '+post_data.id);
            }

            if (!in_background){
                showLoader(); 
            }

            addToDebug('network : ' + post_data['action']);

            var jqxhr = $.post( apiURL, post_data, function(data) {
                //console.log(data);
            },  'json')
                .done(function(data) {
                    addToDebug('network : success');
                    //console.log( "second success" );
                    //console.log(data);
                    hideLoader();

                    if(data.status == 'error') {

                      if (post_data.action=='session'){
                          location.href = base_path+"login.php";
                      }

                      showLock("Error: "+data.error);

                    } else if(data.status == 'ok') {

                      if(callback) {
                        if (params) {
                          callback(data, params);
                        } else {
                          callback(data);
                        }
                      }
                    }else{
                        console.log('error');
                        showLock(data+'');
                    }

                })
                .fail(function(xhr, status, error) {
                    addToDebug('network : error');
                    //console.log( "error" );
                    hideLoader();
                    showLock("Fatal error: "+status+' on '+post_data.action+'<br>'+error+'<br>'+xhr.responseText);
                     //+JSON.stringify(xhr, null, '\t'));
                })                                                                            
                .always(function() {
                    //console.log( "finished" );
                });

            jqxhr.always(function() {
                //console.log( "second finished" );
            });
        }
        function addToDebug(str){
            debug_obj.html(str+'<br>'+debug_obj.html());
        }

        function getPath() {
             //console.log("current_path="+current_path);
             return current_path+"/"+state.join('/');
        }

        function getLevel() {
           return state.length;
        }

        function getShortPath() {
             //console.log("current_path="+current_path);
             if (state.length){
                return state.join('/')+'/';
             } else {
                return '';
             }
        }

        function getNewsPath() {
             //console.log("news_path="+current_path);
             return current_path+"/"+state.join('/')+'/'+current_news_section;
        }


        $main.infoClosed = infoClosed;
        function infoClosed(){
            history.pushState(state.join('/'), null, base_path+''+state.join('/'));
        } 

        $main.settingsClosed = settingsClosed;
        function settingsClosed(){

             
        } 

        function setTextPath(name){
            console.log("setTextPath "+state.join('/')+'/'+name);
            var slash = '';
            if(state.length){ slash = '/';}
            history.pushState(state.join('/') + slash + name, null, base_path+''+state.join('/') + slash + name);

        }

        function setPath(arr){
            console.log("setPath");
            var path = '';
            var debug = '';
            state = []; 

            for(var i=0;i<arr.length;i++){
                path += '<a href="" onclick="return false;" class="path_links" data-id="'+arr[i]['id']+'">'+decodeName((arr[i]['name']).replace(' ','&nbsp;'))+'</a>';
                debug += arr[i]['name'];
                if(i>0) {
                    state.push(arr[i]['name']);
                }
                if(i<(arr.length-1)) {
                    path += '&nbsp;/&nbsp;';
                    debug += '->';
                }
            }
                console.log(state);
            
            history.pushState(state.join('/'), null, base_path+''+state.join('/'));

            addToDebug('set path: '+debug);
            path_holder.html(path);

            $('.path_links').bind('click', function(){
                pathClick($(this).data('id'));
            });

        }

        function getLinks(){
            var links = [];

            $('.path_links').each(function(){
                links.push($(this));
            });
            return links;
        }


        function goOneLeveleUp(){
             var links = getLinks();
             if (links.length>1){
              var link = links[links.length-2];
              console.log(link.html() + ' '+ link.data('id'));
              pathClick(link.data('id'));
             }

        }

        function showLoader(){
            $('#loading').stop().css('display','block').animate({opacity:1}, 300);
        }
        window.showLoader = showLoader;

        function hideLoader(){
            $('#loading').stop().animate({opacity:0}, 300, null, function(){$('#loading').css('display','none');});
        }
        window.hideLoader = hideLoader;
        
        function clearSelected(){
            //console.log("clearSelected f");
            $('.holder').removeClass('selected');
            firstSelected = '';
            lastSelected = '';
            markSelected();
        }

        function  markSelected(){
            $('.holder').find('.holder_shadow').css({display:'none'});
            $('.selected').find('.holder_shadow').css({display:'block'});
        }

        function isSelected(obj){
            return obj.hasClass('selected');
        }

        function getSelected(){
            var selected = [];

            $('.selected').each(function(){
                selected.push($(this));
            });
            return selected;
        }

        function markSelectedBetween(){
            var indexes = [];

            indexes.push((firstSelected.data('position')));
            indexes.push((lastSelected.data('position')));

            console.log(indexes);
            var min = Math.min.apply(Math,indexes);
            var max = Math.max.apply(Math,indexes);
            $('.holder').removeClass('selected');
            $('.holder').each(function(){
                if ($(this).data('position')>=min && $(this).data('position')<=max){
                    $(this).addClass('selected');
                }
            });
        }

        document.onmousedown = function(e){
            if ($(e.target).attr('id') && ($(e.target).attr('id') == 'paddingContent' || $(e.target).attr('id') == 'scrollableContent' || $(e.target).attr('id').indexOf('header')==0  || $(e.target).attr('id').indexOf('footer')==0)){
                console.log("clearSelected "+ $(e.target).attr('id'));
                clearSelected();
            }
        };

        $('#scrollableContent').on('mousedown', function(e){
            console.log("down ");
            if ($(e.target).hasClass('holder_drag')){
                return;
            }
            selectX = e.pageX;
            selectY = e.pageY;
            
            document.onmousemove = function(e){
                console.log("move ");

                selectXX = e.pageX;
                selectYY = e.pageY;
                drawSelection();
            } 
            document.onmouseup = function(e){
                console.log("up");
                document.onmousemove = null;
                document.onmouseup = null;
                clearSelection();
            } 

        });


       function findSelected(x1,y1,x2,y2){
           clearSelected();

           var children = [].slice.call(main_holder.children());

           children.forEach(function(child) {

              if (captureEl(child,x1,y1,x2,y2)) {
                    $(child).addClass('selected');
              }
           });
           markSelected();

       }

        function captureEl(obj,x1,y1,x2,y2){
              if (is_instagram_grid) gsize = 293; 


              if ($(obj).offset().top>(y1) && $(obj).offset().top<(y2) &&
                  $(obj).offset().left>(x1) && $(obj).offset().left<(x2)){
                     return true;
              }
              if (($(obj).offset().top+gsize)>(y1) && ($(obj).offset().top+gsize)<(y2) &&
                  ($(obj).offset().left+gsize)>(x1) && ($(obj).offset().left+gsize)<(x2)){
                     return true;
              }
              if (($(obj).offset().top)>(y1) && ($(obj).offset().top)<(y2) &&
                  ($(obj).offset().left+gsize)>(x1) && ($(obj).offset().left+gsize)<(x2)){
                     return true;
              }
              if (($(obj).offset().top+gsize)>(y1) && ($(obj).offset().top+gsize)<(y2) &&
                  ($(obj).offset().left)>(x1) && ($(obj).offset().left)<(x2)){
                     return true;
              }
              return false;
        }

        function clearSelection(){
           console.log('clearSelection');
           selectX = selectXX = selectY = selectYY = 0;
           drawSelection();
        }

        function drawSelection(){
            var x1 = selectX;
            var x2 = selectXX;
            if (x2<x1){
               x1 = selectXX;
               x2 = selectX;
            }
            var y1 = selectY;
            var y2 = selectYY;
            if (y2<y1){
               y1 = selectYY;
               y2 = selectY;
            }

            $('#selection').css({top:y1,left: x1});
            $('#selection').height(y2 - y1);
            $('#selection').width(x2 - x1);
            if ((x2-x1)!=0){
               findSelected(x1,y1,x2,y2);
            } else {
               $('#selection').css({top:-10,left: -10});

            }
        }


        $(document).keydown(function() {       
            if(event.which == 27){
                
                if ($('#move_options').hasClass('opened')){
                  closeMoveOptions();
                  return;
                }
                if ($('#file_settings').hasClass('opened') || $('#section_settings').hasClass('opened')){ 
                  saveFolderSettings(1);
                  saveFileSettings(1);
                  return;
                }

                if (viewer_on){
                  if ($viewer.getCropTool()==''){
                    hideViewer(null);
                  } else {
                    $viewer.cropDone(null);
                  }
                  return;
                } 

                goOneLeveleUp();
            }

            if ((event.ctrlKey || event.metaKey) && event.which==73) {
               $(".loading_debug").css({opacity: 1});
               showLoader();
            }

        });


        function contains(arr, x) {
            return arr.filter(function(elem) { return elem == x }).length > 0;
        }

        function showNewFolder(data) {
            $('.empty').hide();
            addItem(data.section, main_holder.children().length, getPath());
            initHolderHandlers();
        }
        function showNewFile(data) {
            $('.empty').hide();
            addItem(data.file, main_holder.children().length, getPath());
            initHolderHandlers();
            //lazyload();
            $("img.lazy").lazyload();
        }

        function getThumbName(name) {
            return name.substr(0,name.lastIndexOf("."))+".jpg";
        }

        function addEmptyMessage(){
            console.log("addEmptyMessage");
            var div = document.createElement('div');
            div.className = "empty";
            var h = $('#scrollableContent').height();
            $(div).height(h);
            div.innerHTML = '<div style="text-align:center;">THIS SECTION IS EMPTY<br><br><b>CLICK TO UPLOAD</b></div>';
            main_holder.append(div);
           
            $(div).on('click', function(e){
              
               e.preventDefault(); // предотвращает перемещение к "#"
               fileElem = document.getElementById("fileElem");
               upload_action = "addFile";
               fileElem.click();
            });

        }

        function cutName(name) {
          var p = name.lastIndexOf(".");
          name = name.substr(0,p-5) + name.substr(p,name.length);
          if (name.length>24){
             return name.substr(0,12)+'...'+name.substr(-12);
          } else {
             return name;
          }
        }

        function addItem(item, pos, path) {

           //console.log(item);
            //console.log(pos+' '+path);

            if(item.id){
                //console.log("folder "+item.id);
            } else {
                //console.log("file "+item.thmbname);
            }
            var div = document.createElement('div');
            if (is_instagram_grid) {
            	div.className = "holder holder_instagram";
            } else {
            	div.className = "holder";
            }

            //div.ondblclick = function(){$(this).find('.holder_folder').html('OPEN ME')};
            div.onclick = function (e) {
                //console.log('click id=' + $(this).data('id'));
                //var obj = $('#item_' + item.id);
                var obj = $(this);
                if (e.shiftKey) {
                    obj.addClass('selected');
                    if (firstSelected == '') {
                        firstSelected = obj;
                    } else {
                        lastSelected = obj;
                        markSelectedBetween();
                    }
                    markSelected();
                } else if (e.ctrlKey || e.metaKey) {
                     
                    if (obj.hasClass('selected')) {
                        obj.removeClass('selected');
                    } else {
                        obj.addClass('selected');
                        if (firstSelected == '') {
                            firstSelected = obj;
                        }
                    }
                    markSelected();
                } else {
                    //clearSelected();
                    return;
                }

            };
            div.setAttribute("id", "item_" + item.id);
            div.setAttribute("data-position", pos);
            div.setAttribute("data-id", item.id);
            div.setAttribute("data-name", item.name);
            div.setAttribute("data-vimeo_id", item.vimeo_id);


            var icon = '';
            var folder_style = '';
            var holder_onoff_state = '';

            if (item.status=="1"){
                holder_onoff_state = 'holder_onoff_on';
            } else {
                holder_onoff_state = 'holder_onoff_off';
            }

            var holder_option_state = '';

            if (item.option=="1"){
                holder_option_state = 'holder_option_off';
            } else {
                holder_option_state = 'holder_option_on';
            }
            var type = item.type;
            var counter = '';
            var option = '';
            var upload_thumb = '';
            var duplicate = '';
            var if_folder = '';
            var video_icon = '';

            var name = item.name;
            var display_name = decodeName(name);


            if (item.type == 'portfolio') {                                             
                folder_style = 'style="background: url('+window.base_path+'images/folder.svg)"';
                counter = '<div class="holder_counter">' + item.count+'</div>';          
                if_folder = 'holder_settings_center';

                var cover_hidden = " holder_cover_hidden";

                if (item.cover) {
                   if (item.show_preview=="1"){
                     cover_hidden = "";
                   }
                   icon = '<div class="holder_cover'+cover_hidden+'"><img onload="this.removeAttribute(\'data-src\');" class="lazy" data-src="'+path+'/'+item.cover+'?'+hour+'" data-original="'+path+'/'+item.cover+'?'+hour+'"></div>';
                }
            } else if (item.type == 'text') {

                folder_style = 'style="background: url('+window.base_path+'images/text_section.svg)"';
                if_folder = 'holder_settings_center';

            } else if (item.type == 'news') {

               folder_style = 'style="background: url('+window.base_path+'images/news_section.svg)"';
                if_folder = 'holder_settings_center';
               /* folder_style = 'style="background-repeat: no-repeat; background-size: 58px 48px; background-position: center; border: 1px solid #cdcdcd;background-image: url('+window.base_path+'images/news_section_icon.svg)"';
                */

                icon = '';//'<div class="holder_cover"><div class="file-icon file-icon-lg" data-type="NEWS"></div></div>';

            } else {
                type = "file";
                upload_thumb = '<div class="upload_thumb dynamic1"></div>';
                duplicate = '<div class="holder_duplicate dynamic1"></div>';

                folder_style = 'style="background: url('+window.base_path+'images/file.svg);"';

                option = '<div class="holder_option ' + holder_option_state + ' dynamic" data-option="'+item.option+'"></div>';

                if ($settings["option_enabled"]!="1") {
                   option = '';
                }

                var thumb_folder = 'thumbnails_cms';
                if (is_instagram_grid) {
                    thumb_folder = 'thumbnails_instagram';
                }
                var cover_instagram = '';
                if (is_instagram_grid) {
                    cover_instagram = 'holder_cover_instagram';
                    folder_style = '';
                }

                if (item.thmbname) {
                   icon = '<div class="holder_cover '+cover_instagram+'"><img onload="this.removeAttribute(\'data-src\');" class="lazy" data-src="'+path+'/'+thumb_folder+'/'+item.thmbname+'?'+hour+'" data-original="'+path+'/'+thumb_folder+'/'+item.thmbname+'?'+hour+'"></div>';
                } else {
                   if (item.vimeo_id){
                      icon = '<div class="holder_cover '+cover_instagram+'"><img onload="this.removeAttribute(\'data-src\');" data-src=""><div class="file-icon file-icon-lg" data-type="vimeo"></div></div>';
                   } else {
                      icon = '<div class="holder_cover '+cover_instagram+'"><img onload="this.removeAttribute(\'data-src\');" data-src="'+path+'/'+thumb_folder+'/'+getThumbName(item.name)+'"><div class="file-icon file-icon-lg" data-type="'+item.name.substr(-3)+'"></div></div>';
                   }
                }
                display_name = cutName(decodeName(name));

                if (isImage(name)) {

                }else if (is_instagram_grid) {

                   video_icon = '<div class="holder_video_icon"></div>';
                }
            }

            div.setAttribute("data-type", type);

            var start_div = '';//'<div class="btns_holder">';
            var end_div = '';//'</div>';

            var folder_name = '<div class="item_name">' + display_name + '</div><div class="item_underline"></div>';
            var dragpoint = '<div class="holder_drag dynamic1"></div>';
            var movepoint = '<div class="holder_move dynamic"></div>';
            var debug_info = '<div class="holder_debug"></div>';
            var deleteme = '<div class="holder_delete dynamic"></div>';
            var settings = '<div class="holder_settings '+if_folder+' dynamic1"></div>';
            var onoff = '<div class="holder_onoff '+holder_onoff_state+' dynamic" data-status="'+item.status+'"></div>';
            var btn = '<div class="holder_btn"></div>';
            var shadow = '<div class="holder_shadow"></div>';
            var pnl_right = '<div class="holder_move_pointer"></div>';
            var pnl_left = '<div class="holder_move_pointer0"></div>';
            var holder_folder = '<div class="holder_folder" ' + folder_style + '>' + icon + shadow + '</div>';
            var caption = '<div class="holder_caption"><div class="holder_caption_back"></div><div class="holder_caption_block"><div class="holder_caption_text">open me</div></div></div>';
            div.innerHTML = holder_folder + folder_name + video_icon +caption + btn + counter  + start_div + dragpoint + movepoint + deleteme + settings + upload_thumb + duplicate + onoff + option + end_div  + pnl_right + pnl_left + debug_info;
            main_holder.append(div);
            if (item.status=="0"){
               $(div).css({'opacity': .4});
            }
            $(div).find('.holder_debug').html('id:' + item.id + ' p:' + pos);
        }    

        function isImage(filename) {
            var images = ['jpg', 'gif', 'png', 'svg'];
            var ext = filename.split('.').pop();
            return images.indexOf(ext)!=-1;
        }
                   

        function initHolderHandlers(){
            $('.holder_btn').bind('mouseover', holder_over)
                .bind('mouseout', btn_out)
                .bind('click', openObj);


            $('.holder_drag').bind('mouseover', drag_over)
                .bind('mouseout', btn_out)
                .bind('mousedown', startDrag);

            $('.holder_move').bind('mouseover', move_over)
                .bind('mouseout', btn_out)
                .bind('mousedown', startMove);


            $('.holder_delete').bind('mouseover', delete_over)
                .bind('mouseout', btn_out)
                .bind('click', deleteObj);

            $('.holder_onoff').bind('mouseover', onoff_over)
                .bind('mouseout', btn_out)
                .bind('click', onoffObj);

            $('.holder_option').bind('mouseover', option_over)
                .bind('mouseout', btn_out)
                .bind('click', optionObj);

            $('.holder_settings').bind('mouseover', settings_over)
                .bind('mouseout', btn_out)
                .bind('click', settingsObj);

            $('.upload_thumb').bind('mouseover', uploadthumb_over)
                .bind('mouseout', btn_out)
                .bind('click', uploadthumbObj);

            $('.holder_duplicate').bind('mouseover', duplicate_over)
                .bind('mouseout', btn_out)
                .bind('click', duplicateObj);
        }

        function duplicateObj(e){
            var holder = $(e.target).closest('.holder');
            var status = $(e.target).data('status');

            if(holder.data('type') == 'file') {
                var name = holder.data('name');
                duplicateFile(name);
            }
        }

        function uploadthumbObj(e){
            if (e.shiftKey) {
                return;
            } else if (e.ctrlKey || e.metaKey) {
                return;
            }
            if (getSelected().length>0) {
               return;
            }

            var holder = $(e.target).closest('.holder');
            var status = $(e.target).data('status');

            if(holder.data('type') == 'file') {
                current_holder = holder;    
                console.log('upload thumb');
                uploadThumb();
            }


        }

        function settingsObj(e){
            if (e.shiftKey) {
                return;
            } else if (e.ctrlKey || e.metaKey) {
                return;
            }
            if (getSelected().length>0) {
               return;
            }

            var holder = $(e.target).closest('.holder');
            var status = $(e.target).data('status');

            if(holder.data('type') == 'file') {
                var name = holder.data('name');
                console.log('settings file');
                showFileSettings(name);
            } else {
                var id = holder.data('id');
                console.log('settings folder '+id);
                showFolderSettings(id);
            }


        }


        function onoffObj(e){
            if (e.shiftKey) {
                return;
            } else if (e.ctrlKey || e.metaKey) {
                return;
            }
            var holder = $(e.target).closest('.holder');
            var status = $(e.target).data('status');

            if (!isSelected(holder)){
                clearSelected();
            }

             var files = [];
             var folders = [];



            if(getSelected().length==0){

               if(holder.data('type') == 'file') {
	            files.push(holder.data('name'));
	         } else {
	            folders.push(holder.data('id'));
               }

            } else {


             (getSelected()).forEach(function(obj) {
	        
	         status = status && obj.find('.holder_onoff').data('status');

	         if (obj.data('type')=="file"){
	            files.push(obj.data('name'));
	         } else {
	            folders.push(obj.data('id'));
	         }
             });
            }

            getData({action: 'onoffObjects', id: current_folder_id, files: files, folders: folders, status: status}, onoff, holder, 0);

            /*
            if(holder.data('type') == 'file') {
                console.log('onoff file ');
                var name = holder.data('name');
                getData({action: 'onoffFile', id: current_folder_id, name: name, status: status}, onoff, holder, 0);
            } else {
                var id = holder.data('id');
                getData({action: 'onoffFolder', id: id, status: status}, onoff, holder, 0);
            }
            */

        }

        function onoff(data, obj){
            var objs = [];

            if(getSelected().length==0){
                 objs.push(obj);
            } else {
                (getSelected()).forEach(function(obj) {
                     objs.push(obj);
                });
            }
            
            var btn;

            objs.forEach(function(obj) {
                btn = obj.find('.holder_onoff');
                
                if (data.new_status=="1") {
                    obj.css({'opacity': 1});
                    btn.data('status', 1).removeClass('holder_onoff_off').addClass('holder_onoff_on');
                } else {
                    obj.css({'opacity': .3});
                    btn.data('status', 0).removeClass('holder_onoff_on').addClass('holder_onoff_off');
                }                                       
            });
        }

        function optionObj(e){
            if (e.shiftKey) {
                return;
            } else if (e.ctrlKey || e.metaKey) {
                return;
            }
            var holder = $(e.target).closest('.holder');
            var option = $(e.target).data('option');

            if (!isSelected(holder)){
                clearSelected();
            }

             var files = [];



            if(getSelected().length==0){

               if(holder.data('type') == 'file') {
	            files.push(holder.data('name'));
	         } else {
                 }

            } else {


             (getSelected()).forEach(function(obj) {
	        
	         option = option || obj.find('.holder_option').data('option');

	         if (obj.data('type')=="file"){
	            files.push(obj.data('name'));
	         } else {
	            folders.push(obj.data('id'));
	         }
             });
            }

            getData({action: 'optionFiles', id: current_folder_id, files: files, option: option}, set_option, holder, 0);


            /*
            if(holder.data('type') == 'file') {
                console.log('option file ');
                var name = holder.data('name');
                getData({action: 'optionFile', id: current_folder_id, name: name, option: option}, set_option, holder, 0);
            } else {
            }
            */

        }

        function set_option(data, obj){
            var objs = [];

            if(getSelected().length==0){
                 objs.push(obj);
            } else {
                (getSelected()).forEach(function(obj) {
                     if(obj.data('type') == 'file') {
                         objs.push(obj);
                     }
                });
            }
            
            var btn;

            var mode = data.new_option=="1" ? 'off' : 'on';
            var str = "option";
            if ($settings["option_caption"]!='') {
                str = $settings["option_caption"];
            }



            objs.forEach(function(obj) {

                btn = obj.find('.holder_option');
           
                if (data.new_option=="1") {
                    btn.data('option', 1).removeClass('holder_option_on').addClass('holder_option_off');
                } else {
                    btn.data('option', 0).removeClass('holder_option_off').addClass('holder_option_on');
                }
                showCaption(obj,str+" "+mode, 0);
            });


        }

       

        function deleteObj(e){
            if (e.shiftKey) {
                return;
            } else if (e.ctrlKey || e.metaKey) {
                return;
            }
            //console.log("delete "+e.target);
            var holder = $(e.target).closest('.holder');
            var name = holder.data('name');

            if (!isSelected(holder)){
                clearSelected();
            }

            if(getSelected().length==0){
                if(holder.data('type') == 'file') {
                        
                       confirm('ARE YOU SURE YOU WANT TO DELETE FILE(S)?',//'Delete <b>'+ name + '</b> file(s)?'
                         function () {
        	            main_holder.empty();
                    	    getData({action: 'deleteObjects', id: current_folder_id, files: [name]}, refreshContent, "", 0);
                         },
                         function () {
                
                         });

                } else {
                       confirm('ARE YOU SURE YOU WANT TO DELETE FOLDER?',//'Delete <b>'+ name + '</b> folder?'
                         function () {
                                 main_holder.empty();
                                 var id = holder.data('id');
                                 getData({action: 'deleteFolder', id: id}, refreshContent, "", 0);
                         },
                         function () {
                
                         });


            
                }
            } else {
             var files = [];
             var folders = [];

             (getSelected()).forEach(function(obj) {
	         console.log('move obj '+obj.data('type')+' '+obj.data('name'));

	         if (obj.data('type')=="file"){
	            files.push(obj.data('name'));
	         } else {
	            folders.push(obj.data('id'));
	         }
             });
               confirm('ARE YOU SURE YOU WANT TO DELETE FILE(S)?',//'Delete <b>'+ name + '</b> file(s)?'
                         function () {
        	            main_holder.empty();
                    	    getData({action: 'deleteObjects', id: current_folder_id, files: files, folders: folders}, refreshContent, "", 0);
                         },
                         function () {
                
              });



            }

        }

              $('#s_prev').on('click', function(e){
                 e.preventDefault();
                 $viewer.hideCurrent(); 
                 saveFileSettings(0);
                 navigation("prev",$viewer.getCurrentPos());
                 return;
              });

              $('#s_next').on('click', function(e){  
                 e.preventDefault();
                 $viewer.hideCurrent(); 
                 saveFileSettings(0);
                 $main.navigation("next",$viewer.getCurrentPos());
                 return;
              });


        function navigation(direction ,pos) {
            console.log(direction+' '+pos);
            if (direction == 'next') {
                pos = pos+1;
            } else {
                pos = pos-1;
            }
            if (pos== main_holder.children().length){
                pos = 0;
            }
            if (pos<0) {
                pos = main_holder.children().length - 1;
            }
            var holder = main_holder.find('[data-position="'+pos+'"]');

            if(holder.data('type') != 'file') {
                navigation(direction ,pos);
            } else {
                openMedia(holder);
            }
        }

        $main.navigation = navigation;

        function openObj(e){
            if (e.shiftKey) {
                return;
            } else if (e.ctrlKey || e.metaKey) {
                return;
            }
            //console.log("open " +e.target);
            var holder = $(e.target).closest('.holder');
            openMedia(holder);
       }

       function countContent(){
           var count = 0;
           var children = [].slice.call(main_holder.children());

           children.forEach(function(child) {
               //if($(child).data('type') == 'file') {
                  count++;
               //}
          });

           
           return count;
       }

       function openMedia(holder) {
            current_holder = holder;    

            if(holder.data('type') == 'file') {
                showViewer(getPath(), holder.data('name'), holder.data('position'), holder.data('vimeo_id'), countContent());
                showFileInfo(holder.data('name'));
                return;
            }
            var external = $settings["external_editme_section"].split(',');

            for (var i=0;i<external.length;i++){
                if ((external[i]).trim()==holder.data('name')){
                     window.open('http://'+$settings["external_editme_section_url"]);
                     return;
                }
            }

            if(holder.data('name') == $settings["menu_btn1_name"]) {
                window.open($settings["menu_btn1_link"]);
                return;
            }
            if(holder.data('name') == $settings["menu_btn2_name"]) {
                window.open($settings["menu_btn2_link"]);
                return;
            }

            if(holder.data('type') == 'text') {

                //console.log(getPath()+'/'+holder.data('name')+'/data');
                setTextPath(holder.data('name'));

                openInfo(getShortPath() + holder.data('name')+'/data');

            } else  if(holder.data('type') == 'news') {
                news_holder.empty();
                news_folder_id = holder.data('id');
                getData({action: 'getNews', id: news_folder_id}, showNews, "", 0);
            } else {

                main_holder.empty();
                current_folder_id = holder.data('id');
                getData({action: 'getContent', id: current_folder_id}, showContent, "", 0);
            }

        }

        function reloadThumb(){
             var img = current_holder.find('.holder_cover').find('img');
             var icon = current_holder.find('.holder_cover').find('div');
             icon.remove();
             console.log(img.data('src'));
             console.log(img.attr('src'));
             var link = img.data('src');
             if (!link){
                link = img.attr('src');
             }
             
             img.attr('src', link+'?'+(new Date().getTime()));

             img.onload = function(){
                if (debug) console.log('loaded');
             }
        }

        function startMove(e){
            var holder = $(e.target).closest('.holder');
            if (!isSelected(holder)){
                clearSelected();
            }

            if(getSelected().length==0){
                $(e.target).closest('.holder').addClass('selected');
            }
            showMoveOptions();
        }

        function startDrag(e){
            if(getSelected().length>0){
                return;
            }

            if(getSelected().length==0){
                $(e.target).closest('.holder').addClass('selected');
                drag_position = $(e.target).closest('.holder').data('position');

            }

           

            (getSelected()).forEach(function(obj) {
                obj.clone().appendTo('#avatar');
                obj.css({opacity: .3});
            });

            dragObjects.avatar = $('#avatar');
            dragObjects.objects = $(e.target).closest('.holder');
            dragObjects.downX = e.pageX;// - dragObjects.avatar.offset().left;
            dragObjects.downY = e.pageY;// - dragObjects.avatar.offset().top;
            dragging = true;
            positionAvatar(e);
            initDragging();
        }
        function positionAvatar(e){
                dragObjects.avatar.css({position:'absolute','z-index':999});
                dragObjects.avatar.css('left', (e.pageX-14) + 'px').css('top', (e.pageY-140) + 'px');
        }

        function initDragging(){
            if(!dragObjects.avatar) return;

            document.onmousemove = function(e){
                positionAvatar(e);
                var point = {};
                point.x = e.pageX + 60;
                point.y = e.pageY - 60;
                $('#point').css({top:point.y, left:point.x});
                findNearest(point);
                checkPageScroll(e.pageY);
            }

            document.onmouseup = function(e) {
                dragging = false;
                $('.holder').css({opacity:1});
                stopDragging();
            }
        }
        var scrollTimeout;
        var scrollPage = false;
        var scroll_dy;

        function checkPageScroll(y){
            
            var bottom_y = $('#footer').offset().top;

            if (y<100) scroll_dy = (100-y)/2;
            if (y>bottom_y) scroll_dy = (bottom_y - y)/2;
            

            if ((y<100 || y>bottom_y) && !scrollPage){
              scrollPage = true;
              

              scrollTimeout = setInterval(function(){
                
                 var content_scroll_pos = $('#scrollableContent').scrollTop();
                 $('#scrollableContent').scrollTop(content_scroll_pos - scroll_dy);
              }, 20);

            } else if (y>100 && y<bottom_y) {
              clearTimeout(scrollTimeout);
              scrollPage = false;

            }
        }

        function findNearest(point) {

            $('.holder_move_pointer').hide();
            $('.holder_move_pointer0').hide();
            var type = dragObjects.objects.data('type');


           var children = [].slice.call(main_holder.children());
           var x,y,dx,dy,new_l;
           var l = 10000;
           var nearest;
           var ok;

           children.forEach(function(child) { 
               ok = false;

               if ($(child).data('type') == type){
                   ok = true;
               }

               if ((type=='portfolio' || type=='text') && $(child).data('type') == 'news'){
                   ok = true;
               }

               if ((type=='news' || type=='text') && $(child).data('type') == 'portfolio'){
                   ok = true;
               }

               if ((type=='news' || type=='portfolio') && $(child).data('type') == 'text'){
                   ok = true;
               }

               if (ok){
                   x = $(child).offset().left + 70;
                   y = $(child).offset().top + 70;
                   dx = Math.abs(x - point.x);
                   dy = Math.abs(y - point.y);
                   new_l = Math.sqrt(dx*dx + dy*dy);
                   if (new_l < l){
                      l = new_l;
                      nearest = $(child);
                   }
               }
           });
           var x1 = nearest.offset().left + 70;

           if(point.x<x1){
               nearest.find('.holder_move_pointer0').show();
           } else {
               nearest.find('.holder_move_pointer').show();
           }

        }
        function stopDragging(){
            var obj;
            var obj1 = $('.holder_move_pointer:visible');
            var obj2 = $('.holder_move_pointer0:visible');
            var target = '';
            var targetFile = '';
            var position = '';

            //console.log(obj1.closest('.holder').data('position')+' '+obj2.closest('.holder').data('position'));

            document.onmousemove = function(e){};
            document.onmouseup = function(e) {};
            $('#avatar').empty();
            clearSelected();

            if (obj1.closest('.holder').data('position')!=undefined) {
                obj = obj1;
                position = 'after';
            } else 

            if (obj2.closest('.holder').data('position')!=undefined) {
                obj = obj2;
                position = 'before';
            } else {
               $('.holder_move_pointer').hide();
               $('.holder_move_pointer0').hide();

                return;
            }
          

                drag_position = obj.closest('.holder').data('position');
                target = obj.closest('.holder').data('id');
                targetFile = obj.closest('.holder').data('name');

            /* 
            if (obj.closest('.holder').data('position')>=0){
                drag_position = obj.closest('.holder').data('position')+1;
                target = obj.closest('.holder').data('id');
                targetFile = obj.closest('.holder').data('name');
            } else if(obj1.closest('.holder').data('position')===0){
                drag_position = 0;
            } else {
               drag_position = 'end';
            }
            */
            var children = [].slice.call(main_holder.children());
            var count = children.length;

            console.log(position +" "+drag_position);
            
            console.log("this "+  dragObjects.objects.data('position'));

            console.log("count "+count);

            var old_position = dragObjects.objects.data('position');
            var aim = dragObjects.objects.data('id');
            var aimFile = dragObjects.objects.data('name');
            var type = dragObjects.objects.data('type');
            var aimFiles = [];

            (getSelected()).forEach(function(object) {
               if (objects.data('type')=='file'){
                  aimFiles.push(object.data('name'));
               }
            });

            $('.holder_move_pointer').hide();
            $('.holder_move_pointer0').hide();

           
            if (position=='before' && old_position == (drag_position-1)) {
               return;
            }
            if (position=='after' && old_position == (drag_position+1)) {
               return;
            }
            if (old_position == (drag_position)) {
               return;
            }
            

            if (type=='file') {
                getData({action: 'moveFileToPosition', id: current_folder_id, aim: aimFile, target: targetFile, old_position: old_position, new_position: drag_position, position: position}, refreshContent, "", 0);
            } else {
                getData({action: 'moveFolderToPosition', parent_id: current_folder_id, aim: aim, target: target, old_position: old_position, new_position: drag_position, position: position}, refreshContent, "", 0);
            }

        }

        function holder_over(e){
            if(dragging) {
                if($(e.target).closest('.holder').hasClass("selected")){
                   return;
                }
                return;
                if($(e.target).closest('.holder').data('position')==0 && e.pageX<100){
                    $(e.target).closest('.holder').find('.holder_move_pointer0').show();
                } else {
                    $(e.target).closest('.holder').find('.holder_move_pointer').show();
                }
            }
            showCaption($(e.target).closest('.holder'),"open", 1);
        }

        function drag_over(e){
            if (getSelected().length==0) {
                showCaption($(e.target).closest('.holder'),"drag", 1);
            }
        }


        function move_over(e){
            showCaption($(e.target).closest('.holder'),"move", 1);
        }

        function delete_over(e){
            showCaption($(e.target).closest('.holder'),"delete", 1);
        }
        function onoff_over(e){
            var mode = $(e.target).data('status')==1 ? 'off' : 'on';
            showCaption($(e.target).closest('.holder'),"turn "+mode, 1);
        }

        function option_over(e){
            var mode = $(e.target).data('option')==1 ? 'off' : 'on';
            var str = "option";
            if ($settings["option_caption"]!='') {
                str = $settings["option_caption"];
            }
            showCaption($(e.target).closest('.holder'),str+" "+mode, 0);
        }

        function settings_over(e){
            if (getSelected().length==0) {
                showCaption($(e.target).closest('.holder'),"settings", 0);
            }
        }

        function uploadthumb_over(e){
            if (getSelected().length==0) {
                showCaption($(e.target).closest('.holder'),"upload thumbnail", 0);
            }
        }

        function duplicate_over(e){
            if (getSelected().length==0) {
                showCaption($(e.target).closest('.holder'),"duplicate", 0);
            }
        }

        function btn_out(e){
            $(e.target).closest('.holder').find('.holder_move_pointer').hide();
            $(e.target).closest('.holder').find('.holder_move_pointer0').hide();
            hideCaption($(e.target).closest('.holder'));
        }

        function showCaption(holder, text, adds) {

            if(dragging) return;

            $('.holder_caption').hide();

            if (is_instagram_grid) {
               holder.find('.dynamic').hide();
               if(getSelected().length==0){
                   holder.find('.dynamic1').hide();
               }
            }

            if (holder.find('.holder_option').data('option')=='1'){
                holder.find('.holder_caption_back').addClass('holder_caption_back_red');
            } else {
                holder.find('.holder_caption_back').removeClass('holder_caption_back_red');
            }
            holder.find('.holder_caption').stop().fadeIn(300);

            if (is_instagram_grid) {
               holder.find('.dynamic').stop().fadeIn(300);
               if(getSelected().length==0){
                  holder.find('.dynamic1').stop().fadeIn(300);
               }

               if ($(".holder_video_icon").is(":visible")) {
                  console.log('fadeOut');
                  holder.find('.holder_video_icon').hide();//stop().fadeOut(300);
               }
            }


            var new_text = text;
            if (adds){
               new_text += ' '+ getNameByType(holder.attr('data-type'));
            }
            if($('.selected').length && text!='open'){
                new_text = text + ' all selected';
            }
            holder.find('.holder_caption_text').html(new_text);
        }

        function hideCaption(holder) {
            //console.log('hideCaption');
            holder.find('.holder_caption').stop().fadeOut(300);
            if (is_instagram_grid) {
               holder.find('.dynamic').stop().fadeOut(300);
               holder.find('.dynamic1').stop().fadeOut(300);
               console.log('fadeIn');
               holder.find('.holder_video_icon').stop().fadeIn(300);
            }

        }

        function getNameByType(type){
            switch(type){
                case 'portfolio': return 'section'; break;
                case 'text': return 'section'; break;
                case 'news': return 'news section'; break;
                case '': return 'file'; break;
                default: return 'file'; break;
            }
        }

        function showViewer(path, file, pos, vimeo_id, count){
            $('#viewer').stop().css('display','block').animate({opacity:1}, 300);
            $viewer.showFile(path, file, pos, vimeo_id, count, getFileCaption(file), null);
            $('#settings_counter').html((pos+1)+' / '+count);
            $('.file_settings_navigation').show();
            viewer_on = true;
        }

        function hideViewer(e){
            viewer_on = false;
            $('#viewer').stop().animate({opacity:0}, 300, null, function(){$('#viewer').css('display','none');});
            $viewer.clear();
            $('.file_settings_navigation').hide();

            if ($main.need_thumb_reload) {   
               reloadThumb();
               $main.need_thumb_reload = false;
            }
            if ($main.need_content_reload) {   
               refreshContent();
               $main.need_content_reload = false;
            }
        }
        //==========================================   
        function setDescriptions(){
            var desc = [];
            var l = getLevel();
            for (var i=1;i<5;i++){
               desc = ($settings["describe_label"+i]+'').split(",");
               if (!desc[l]){
                 desc[l] = "DESCRIPTION "+i;
               }
               $('#describe_label'+i).html(desc[l]);
            }

            if (l==0){
               $('#ga_holder').show();
               $('#gpixel_holder').show();
               $('#fbpixel_holder').show();
            } else {
               $('#ga_holder').hide();
               $('#gpixel_holder').hide();
               $('#fbpixel_holder').hide();
            }
        }

        function showFolderSettings(id) {
          var w =  -1*$('#section_settings').width();
          $('#section_settings').show()
          .addClass("opened")
          .css({'right': w})
          .animate({'right':0}, 300);
          $('#shadow').show();
          $('#section_id').val(id);
          setDescriptions();


            for (var key in content) {
                //console.log(content[key]["id"]+'=='+id);
                if (content[key]["id"]==id){
                    $('#section_name').val(decodeName(content[key]["name"]));
                    $('#section_descr1').val(content[key]["description"]);
                    $('#section_descr2').val(content[key]["description2"]);
                    $('#section_descr3').val(content[key]["description3"]);
                    $('#section_descr4').val(content[key]["description4"]);
                    if (content[key]["show_preview"]=="1"){
                       $('#show_preview').val(1);
                       $('#show_preview_btn').html('Show section preview');
                    }else{
                       $('#show_preview').val(0);
                       $('#show_preview_btn').html('Hide section preview');
                    }
                    var seo = JSON.parse(content[key]["seo"]);
                    $('#meta_title').val(seo.title);
                    $('#meta_description').val(seo.description);
                    $('#meta_keywords').val(seo.keywords);

                    if (getLevel()==0){
                      $('#ga').val(seo.ga);
                      $('#gpixel').val(seo.gpixel);
                      $('#fbpixel').val(seo.fbpixel);
                    }
                    break;
                }
            }
        }

        $('#show_preview_btn').unbind('click').on('click', function(e){
           var val =  $('#show_preview').val();
           if (val==1) {
           console.log('show');
               $('#show_preview').val(0);
               $('#show_preview_btn').html('Hide section preview');

           } else {
           console.log('hide');
               $('#show_preview').val(1);
               $('#show_preview_btn').html('Show section preview');
           }
        });

        $('#file_settings_link').on('click', function(e){
              e.preventDefault();
              showFileSettings(current_holder.data('name'));
        });

        function getFileCaption(name) {
            for (var key in content) {
                if (content[key]["name"]==name){
                    return content[key]["caption"];
                }
            }
            return false;
        }

        function showStructure(data) {

          $('#shadow').show();

          $main.structure = data;

          showMoveLevel(1, -1);
          
        }

        function showMoveLevel(level, id) {

          for (var i=level+1;i<10;i++) {
            if ($('#move_column_'+i).length) {
             
               $('#move_column_'+i).html('');
               $('#move_column_'+i).remove();

            }
          }
          var data = $main.structure;

          var str = '';
          str += '<div class="move_this_level"><a href="#" class="move_to_this_level" data-id="'+id+'">MOVE TO THIS LEVEL</a></div>';
          str += '<div class="move_level">';
          for (var key in data.structure) {
             if (data.structure[key]["parent_id"]==id && data.structure[key]["type"]=='portfolio'){   
               str += '<div class="move_to_item move_to_item'+level+'"><a href="#" data-level="'+level+'" data-id="'+data.structure[key]["id"]+'" class="move_level'+level+'">'+data.structure[key]["name"]+"</a></div>";
             }
          }
          str += '</div>';
          str += '</div>';

          if ($('#move_column_'+level).length) {
             
             $('#move_column_'+level).html(str);

          } else {
            
             $('.move_options_to').append('<div id="move_column_'+level+'" class="move_to_column">'+str+'</div>');
          }
          
          $('.move_to_this_level').unbind('click').on('click', function(e){
              console.log("move to this level "+$(this).data('id'));
              moveObjectsToLevel($(this).data('id'));
          });
          $('.move_level'+level).on('click', function(e){
             $('.move_to_item'+level).removeClass('move_selected_item');
             $(this).closest('div').addClass('move_selected_item');
             console.log($(this).data('id'));
             var new_level = $(this).data('level')+1;
             showMoveLevel(new_level, $(this).data('id'));
          });

        }

        function moveObjectsToLevel(id) {  

             var files = [];
             var folders = [];

             (getSelected()).forEach(function(obj) {
	         console.log('move obj '+obj.data('type')+' '+obj.data('name'));

	         if (obj.data('type')=="file"){
	            files.push(obj.data('name'));
	         } else {
	            folders.push(obj.data('id'));
	         }
             });
      
             getData({action: 'moveToFolder', id: current_folder_id, to_id: id, files:files, folders:folders}, moveDone, "", 0);

        }

        function moveDone(data) {
           clearSelected();
           closeMoveOptions();
           refreshContent();

        }

        function showMoveOptions() {
          
          var w =  -1*$('#move_options').width();
          $('#move_options').show();
          $('#move_options').show().addClass("opened");

          $('#move_options').css({'left': w});
          $('#move_options').animate({'left':0}, 300);
          $('#shadow').show();

          var objs = [];
          (getSelected()).forEach(function(obj) {
	         console.log('move obj '+obj.data('type')+' '+obj.data('name'));
	         objs.push(obj.data('name'));
          });

          getData({action: 'getStructure'}, showStructure, "", 0);


          $('.move_options_infotext').html('MOVE TO SECTION...');
        }

        function closeMoveOptions(){
          clearSelected();
          $('#shadow').hide(); 
          var w =  -1*$('#move_options').width();
          $('#move_options').removeClass('opened').animate({'left': w}, 300, function(){$('#move_options').hide();});
        }

 
        function showFileSettings(name) {
          console.log(name);
          var w =  -1*$('#file_settings').width();
          $('#file_settings').show()
          .addClass("opened")
          .css({'right': w})
          .animate({'right':0}, 300);
          $('#shadow').show();
          showFileInfo(name);

        }

        function setFileText(name, data){
            console.log("file="+name);
            console.log(data);
        }

        function showFileInfo(name){
            console.log("showFileInfo");
            $('#file').val(name);

            for (var key in content) {
                if (content[key]["name"]==name){
                    $('#file_name').text(content[key]["name"]);
                    $('#file_caption1').val(content[key]["caption"]);
                    $('#file_caption2').val(content[key]["caption2"]);
                    $('#file_caption3').val(content[key]["caption3"]);
                    $('#file_caption4').val(content[key]["caption4"]);
                    
          	    //if ($('#file_text').tinymce()){
		        //$('#file_text').tinymce().remove();
		    //}
                    
                    $('#file_text').val(content[key]["txt"]);

		    //initTM($('#file_settings'), name, setFileText, 160);
		    initSN($('#file_settings'), name, setFileText, 160);

                    break;
                }
            }

        }

        function updateContent(res) {
            console.log(res.status);
              if (res.status == 'ok') {

                  for (var key in content) {
                      
                      if (content[key]["name"]==res.file.name){
                          content[key]["caption"] = res.file.caption;
                          content[key]["caption2"] = res.file.caption2;
                          content[key]["caption3"] = res.file.caption3;
                          content[key]["caption4"] = res.file.caption4;
                          content[key]["txt"] = res.file.txt;
                          $viewer.updateFile(content[key]);
                          break;
                      }
                  }
                  
                 
              }
              if (res.status == 'error') {
                 showLock(res.error);
              }

        }

        function closeFolderSettings(){
          $('#shadow').hide();
          var w =  -1*$('#section_settings').width();
          $('#section_settings').removeClass("opened").animate({'right': w}, 300, function(){$('#section_settings').hide();});
        }

        function closeFileSettings(){
          $('#shadow').hide();  
          var w =  -1*$('#file_settings').width();
          $('#file_settings').removeClass("opened").animate({'right': w}, 300, function(){$('#file_settings').hide();});
        }

        $('.section_settings_cancel').on('click', function(){
            closeFolderSettings();
        });

        $('.move_options_cancel').on('click', function(){
            closeMoveOptions();
        });

        $('#shadow').on('click', function(){
            //closeFolderSettings();
            //closeFileSettings();
            //closeMoveOptions();
        });

 
        $('#duplicate').on('click', function(){
            duplicateFolder(0);
        });

        $('#duplicatewithdata').on('click', function(){
            duplicateFolder(1);
        });

        $('.section_settings_save').on('click', function(){
            saveFolderSettings(1);
        });

        $('.file_settings_cancel').on('click', function(){
            saveFileSettings(1);
        });

        $('.file_settings_save').on('click', function(){
            saveFileSettings(1);
        });

        function saveFileSettings(close_me){
             if (!$('#file_settings').hasClass('opened')){ return;}
             console.log('saveFileSettings');
             var name = $('#file_name').text();
             var caption1 = $('#file_caption1').val();
             var caption2 = $('#file_caption2').val();
             var caption3 = $('#file_caption3').val();
             var caption4 = $('#file_caption4').val();
             //var txt = tinyMCE.activeEditor.getContent();
             var txt = $('#file_settings').find('textarea').summernote('code');
             $('#file_settings').find('textarea').summernote('destroy');
             //console.log("---------");
             //console.log(txt);
             //console.log("---------");
             var need_save = false;


             for (var key in content) {
                 //console.log(content[key]["name"]+'=='+res.file.name);
                 if (content[key]["name"]==name){
                    if (content[key]["caption"]!=caption1) {need_save = true;console.log("here1");}
                    if (content[key]["caption2"]!=caption2) {need_save = true;console.log("here2");}
                    if (content[key]["caption3"]!=caption3) {need_save = true;console.log("here3");}
                    if (content[key]["caption4"]!=caption4) {need_save = true;console.log("here4");}
                    if (content[key]["txt"]!=txt) {need_save = true;console.log("here5");}
                 }

             }

             if (need_save) {
                getData({action: 'updateFile', id: current_folder_id, name: name, caption1: caption1, caption2:caption2, caption3:caption3, caption4:caption4, txt: txt}, updateContent, "", 0); 
             }
             if (close_me){
                closeFileSettings();
             }

        }

        function saveFolderSettings(close_me){
             if (!$('#section_settings').hasClass('opened')){ return;}

             console.log('saveFolderSettings');
             var id = $('#section_id').val();
             var name = $('#section_name').val().trim();

             if (name=='') {alert('Empty name!'); return;}
             name = encodeName(name);

             var descr1 = $('#section_descr1').val();
             var descr2 = $('#section_descr2').val();
             var descr3 = $('#section_descr3').val();
             var descr4 = $('#section_descr4').val();

             var show_preview = $('#show_preview').val();

            var seo = {};
            seo.title = $('#meta_title').val();
            seo.description = $('#meta_description').val();
            seo.keywords = $('#meta_keywords').val();

            if (getLevel()==0){
              seo.ga = $('#ga').val();
              seo.gpixel = $('#gpixel').val();
              seo.fbpixel = $('#fbpixel').val();

            }
             var need_save = false;

            for (var key in content) {
                if (content[key]["name"]==name && content[key]["id"]!=id){
                  alert('Folder name already exists!');
                  return;
                }
                if (content[key]["id"]==id){
                    if (content[key]["name"]!=name) {need_save = true;}
                    if (content[key]["description"]!=descr1) {need_save = true;}
                    if (content[key]["description2"]!=descr2) {need_save = true;}
                    if (content[key]["description3"]!=descr3) {need_save = true;}
                    if (content[key]["description4"]!=descr4) {need_save = true;}

                    var old_seo = JSON.parse(content[key]["seo"]);
                    if (seo.title!=old_seo.title) {need_save = true;}
                    if (seo.description!=old_seo.description) {need_save = true;}
                    if (seo.keywords!=old_seo.keywords) {need_save = true;}

                    if (show_preview!=content[key]["show_preview"]){need_save = true;}

                    

                    if (getLevel()==0){
                       if (seo.ga!=old_seo.ga) {need_save = true;}
                       if (seo.gpixel!=old_seo.gpixel) {need_save = true;}
                       if (seo.fbpixel!=old_seo.fbpixel) {need_save = true;}
                    }

                }
            }  
            
            
            if (need_save) {
                getData({action: 'updateFolder', id: id, name: name, descr1: descr1, descr2:descr2, descr3:descr3, descr4:descr4, show_preview: show_preview, seo: JSON.stringify(seo)}, refreshContent, "", 0);
            }
            if (close_me){
                closeFolderSettings();
            }
        }

       function duplicateFolder(withdata){
             var id = $('#section_id').val();
             var name = $('#section_name').val();
             getData({action: 'duplicateFolder', id: id, name: name, withdata: withdata}, refreshContent, "", 0);

       }
       function duplicateFile(name){
             getData({action: 'duplicateFile', id: current_folder_id, name: name}, refreshContent, "", 0);

       }
       //============================================            

        var files_count = 0;
        var fileSelect = document.getElementById("fileSelect"),
        fileElem = document.getElementById("fileElem"),
        onefileElem = document.getElementById("oneFileElem");
        
        fileSelect.addEventListener("click", function (e) {
          if (current_folder_id==-1) {
              
               return;
          }
          if (fileElem) {
            upload_action = "addFile";
            fileElem.click();
          }
          e.preventDefault();
        }, false);

	function uploadThumb(){
          if (onefileElem) {
            $(onefileElem).val('');
            upload_action = "uploadThumb";
            onefileElem.click();
          }
        }

        function uploadThumbResult(data) {
            if (debug) console.log(data);
            reloadThumb();
        }


	function replaceContent(e){
          if (onefileElem) {
            $(onefileElem).val('');
            upload_action = "replaceFile";
            onefileElem.click();
          }
        }

        function replaceContentResult(data) {
            console.log(data);
            $viewer.reloadFile(data.file.name);
        }

       
        function uploadfinishVoid(){
          if (debug) {console.log("uploadfinishVoid() ");}

          $("#fileElem").val('');
          $('#uploader_progress').animate({'right':-300}, 300, function(){$('#uploader_progress').hide();});
          hideLoader();
        }
        function uploadfinish(){
          if (debug) {console.log("uploadfinish() "+current_folder_type);}
          $("#fileElem").val('');
          $('#uploader_progress').animate({'right':-300}, 300, function(){$('#uploader_progress').hide();});

          if(current_folder_type == "news") {

          } else {
             refreshContent();
          }
        }
        
        function handleFiles(files) {
          if (debug) console.log("handleFiles()");

          $('#uploader_progress').empty();
          $('#uploader_progress').show();
          $('#uploader_progress').css({'right': -300});
          $('#uploader_progress').animate({'right':0}, 300);
          showLoader();
          
          for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var imageType = /^image\//;
           
            if (!imageType.test(file.type.match)) {
             // continue;
            }
           
            files_count++;
            upload(file, i);
          }
        }
    
        window.handleFiles = handleFiles;
        
        function upload(file, n) {
        
          var xhr = new XMLHttpRequest();
        
          // обработчик для закачки
          xhr.upload.onprogress = function(event) {
            //log(n+" "+event.loaded + ' / ' + event.total);
            var per = (100/event.total)*event.loaded;
           
            $("#upload_"+n).find('.upload_progress').css('width', per+'%');
          }

          xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
              
               try {
                   var res = JSON.parse(xhr.response);
               } catch (err) {
                   
                   showLock(err+'');
                   return false;
               }

              if (res.status == 'ok') {
                 if (debug) console.log("current_folder_type="+current_folder_type);

                 if(current_folder_type == "news") {
                    addNewsMedia(res.file);
                 } else {
                    if (upload_action=="addFile") {
                        showNewFile(res);
                    } else  if (upload_action=="replaceFile") {
                        replaceContentResult(res);
                    } else  if (upload_action=="uploadThumb") {
                        uploadThumbResult(res);
                    }
                 }
              }
              if (res.status == 'error') {
                 showLock(res.error);
              }
            }
          }
        
          xhr.onerror = function() {
              log("error "+n);
          }

          xhr.onload = function() {
            if (this.status == 200) {
              log("success "+n);
              files_count--;
              if (files_count==0){
                 if (debug) console.log('finish');
                 if (upload_action=="uploadThumb"){
                    uploadfinishVoid();
                 } else {
                    uploadfinish();
                 }
              }
            } else {
              log("error " + this.status);
            }
          };
          
          addDisplay(file.name, n);
          xhr.open("POST", apiURL, true);
          var formData = new FormData();
          formData.append("action", upload_action);

          if (upload_action=="replaceFile") {
             formData.append("name", $viewer.getCurrentFile());
          }

          if (upload_action=="uploadThumb") {
             formData.append("name", getThumbName(current_holder.data('name')));
          }


          if (news_folder_id!=-1) {
             formData.append("id", news_folder_id);
          } else {
             formData.append("id", current_folder_id);
          }
          formData.append("Filedata", file);
        
          xhr.send(formData);
        
        } //upload
    
        function addDisplay(name, n){
        	var str = '<div>'+name+'</div>'+
        	     '<div class="upload_back">'+
        	        '<div class="upload_progress">'+
        	        '</div>'+
        	     '</div>';
        
                  var div = document.createElement('div');
                  div.className = "upload_item";
                  div.setAttribute("id", "upload_" + n);
                  div.innerHTML = str;
        	  $('#uploader_progress').append(div);
        
        }
        
        function log(str){
                 console.log(str);
        }

        function initSN(obj, id, callback, h){
                 console.log("initSN()");
                 obj.find('textarea').summernote('destroy');

		 obj.find('textarea').summernote({height: h,
                    toolbar: [
                      // [groupName, [list of button]]
                      ['style', ['bold', 'italic', 'underline', 'clear']],
                      //['font', ['strikethrough', 'superscript', 'subscript']],
                      ['fontsize', ['fontsize']],
                      ['color', ['color']],
                      //['para', ['ul', 'ol', 'paragraph']],
                      ['insert', ['link', 'picture', 'video']],
                      ['misc', ['codeview','undo','redo','help']]
                    ],
                  callbacks: {
                    onBlur: function() {
                      console.log('Editable area loses focus');
                      callback(id, obj.find('textarea').val());

                    }
                  }
                 
                });

                
                
                

        }
           
        function initTM(obj, id, callback, h){
                console.log("initTM "+id);
                obj.find('textarea').addClass('tinymce');

                obj.find('textarea').tinymce({
                    // Location of TinyMCE script
                    script_url: base_path+'text_editor/tinymce/js/tinymce/tinymce.js',
                    // General options
                    height: h,
                    skin: "cms",
                   // menubar: false,
                    theme: "cms",
                    s3dir:"1",
                    //s1_text:"STYLE1",
                    //s2_text:"STYLE2",
                    //s3_text:"STYLE3",
                    plugins : "media_cms code_cms email paste link_cms link font_style advlist_cms align_cms",
                  
                    paste_auto_cleanup_on_paste : true,
                    paste_postprocess : function(pl, o) {
                        o.node.innerText = o.node.innerText.replace(/<\/?[^>]+(>|$)/g, "");
                    },
                    paste_remove_spans : true,
                    paste_remove_styles : true,
                    media_strict: false,
                    extended_valid_elements : "*[*]",
                    theme_advanced_toolbar_location: "top",
                    theme_advanced_toolbar_align: "left",
                    
                    theme_advanced_resizing: true,
                    content_css: "text_editor/css/style.css",

                    init_instance_callback: function (editor) {
                      editor.on('blur', function (e) {
                      console.log('Editor was blurred!');
                      callback(id, tinyMCE.activeEditor.getContent());
                      });
                    }
                });

        }
//=================================================================       
//===================== 
    });

})();