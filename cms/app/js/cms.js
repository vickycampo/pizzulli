<!--            

    /* If you will use some other flash id instead of "mainFlash" in <object> tag, then
       change "mainFlash" parameter of thisMovie function in the string:
       thisMovie("mainFlash").onDataUploaded();
       to the desired id.
    */
    function initPage(){
       
       addTextEditor(); 
    }
    function setBash(bash){
           //alert(bash);
         if(bash!=""){
           location.hash = bash;
        }else{
           //location.hash = " ";
        }
    }
    var path = "";
    var path1 = "";
    var txtfile = "";
    var need_refresh = false;



    function openSettings(){
       s_cms_div = document.getElementById("sdiv");
       s_cms_div_inside = document.getElementById("sdiv_inside");
       s_cms_div.style.display = "block";
       s_cms_div.style.left = 0;
       $(s_cms_div_inside).load(base_path+"editsettings.php");


    }

    function openInfo(file){
     
       //window.open("editinfo.php?fl="+"data_cms/"+escape(file)+".txt","info","toolbar=no,location=no,width=800,height=600,left=50,top=0");
       ///return;
       text_cms_div = document.getElementById("idiv");
       text_cms_div_editor = document.getElementById("editor");
       text_cms_div.style.display = "block";
       text_cms_div.style.left = 0;
       //$("#idiv").load("editinfo.php?fl="+"data_cms/"+escape(file)+".txt");
       //$("#idiv").load("text_cms/?path="+"data_cms/"+escape(file));
       t = file.split("/");
       fl = t.splice(-1,1);
       v = t.join("/");
       path = data_folder+""+v+"/";
       path = str_replace("&","%26",path);
       path = str_replace("_plus_","%2B",path);
       path1 = v+"/";
       path1 = str_replace("&","%26",path1);
       path1 = str_replace("_plus_","%2B",path1);
       txtfile = fl[0];
       console.log("path="+path);
        getXml();
        
       	var currentWidth = $(window).width();
	var currentHeight = $(window).height();
	editorWidth = currentWidth*0.8;
	editorHeight = currentHeight*0.9;
	var leftmargin = currentWidth*0.1;
	var topmargin = currentHeight*0.1;
        showRichTextEditor(editorWidth, editorHeight, 0, 0, 0, 0, true, true, true, true, true, true, true, true, path);



    }



var editorWidth;
var editorHeight;
var editorLeft;
var editorRight;
var editorMarginTop;
var editorMarginBottom;
var textEditorInited = false;

function resizeTextEditor(){
        //console.log("resizeTextEditor");
	var currentWidth = $(window).width();
	var currentHeight = $(window).height();

	editorWidth = currentWidth*0.8;
	editorHeight = currentHeight*0.9;
	if (editorWidth < 720) {
            editorWidth = 720;
	}
	/*
	if(editorWidth == 0 && editorMarginBottom == 0)
	{
		editorWidth = currentWidth - editorLeft - editorRight;
		
		if(currentHeight < 800)
		{
			editorHeight = currentHeight - editorMarginTop - 100;
		}
	}
	else if(editorHeight == 0 && editorLeft == 0 && editorRight == 0)
	{
		editorHeight = currentHeight - editorMarginTop - editorMarginBottom;
		editorLeft = Math.round(currentWidth / 2 - editorWidth / 2);
	}
	 */	
	$("#editor").css("width", editorWidth + "px");
	$("#editor").css("height", editorHeight + "px");
	$("#editor").css("left", editorLeft + "px");
	$("#editor").css("top", editorMarginTop + "px");

}

function showRichTextEditor(edWidth, edHeight, edLeft, edRight, edMarginTop, edMarginBottom, boldEnabeld, italicEnabled, linkEnabled, imageEnabled, listEnabled, alignEnabled, codeEnabled, isNew, sectionName) 
{	
	editorWidth = edWidth;
	editorHeight = edHeight;
	editorLeft = edLeft;
	editorRight = edRight;
	editorMarginTop = edMarginTop;
	editorMarginBottom = edMarginBottom;

        resizeTextEditor();
	
	$("#editor").css("z-index", 9);

	if(isNew)
	{
		var iframe = window.frames['text_editor'];
		
		if (iframe)
		{
			if (iframe.contentWindow)
			{
				iframe.contentWindow.setS3Dir(sectionName);
				iframe.contentWindow.setText("");
			}
			else
			{
				iframe.setS3Dir(sectionName);
				iframe.setText("");
			}
		}
	}
}
function setData(val,title,date){
		var iframe = window.frames['text_editor'];
		//console.log(path);
		if (iframe)
		{
			if (iframe.contentWindow)
			{
				iframe.contentWindow.setS3Dir(path);
				iframe.contentWindow.setText(val);
				iframe.contentWindow.setTitle(title);
				iframe.contentWindow.setDate(date);
			}
			else
			{
				iframe.setS3Dir(path);
				iframe.setText(val);
				iframe.setTitle(title);
				iframe.setDate(date);
			}
		}

}
function saveData(){
  //console.log("saveData");
  //console.log(getEditorData());
  showLoader();
  content = getEditorData();
  title = getEditorTitle();
  date = getEditorDate();
  //console.log(getEditorTitle());
  //console.log(getEditorDate());

       //console.log("path="+path);
       //console.log("txtfile="+txtfile);
   $.post(base_path+"text_editor/generate_xml.php", { path:path,txtfile:txtfile,content:content,title:title,date:date },function(data) {
     //console.log(data);
     hideLoader();
   } );
  
}
function getXml(){

   var jqxhr = $.get(base_path + "../"+path + txtfile+"_cms.xml?i="+(new Date()).getSeconds(), function(data) {
        setData($(data).find("content").text(),$(data).find("notes").text(),$(data).find("date").text());
        
   })
     .done(function() {
      
     })
     .fail(function() {
       setData('','','');
     })
  .always(function() {
    
  });
 
}
function getEditorDate()
{
	var iframe = window.frames['text_editor'];
	
	if (iframe)
	{
		if (iframe.contentWindow)
		{
			return iframe.contentWindow.get_Date();
		}
		else
		{
			return iframe.get_Date();
		}
	}	
}

function getEditorTitle()
{
	var iframe = window.frames['text_editor'];
	
	if (iframe)
	{
		if (iframe.contentWindow)
		{
			return iframe.contentWindow.getTitle();
		}
		else
		{
			return iframe.getTitle();
		}
	}	
}

function getEditorData()
{
	var iframe = window.frames['text_editor'];
	
	if (iframe)
	{
		if (iframe.contentWindow)
		{
			return iframe.contentWindow.getTextEditorData();
		}
		else
		{
			return iframe.getTextEditorData();
		}
	}	
}

function addTextEditor()
{
	var textEditorIframe = document.createElement("iframe");
	textEditorIframe.setAttribute("src", base_path+"/text_editor/index1.html?i="+(new Date()).getSeconds());
	textEditorIframe.setAttribute("id", "text_editor");
	textEditorIframe.style.border = 0;
	textEditorIframe.style.width = "100%";
	textEditorIframe.style.height = "100%";
	
	document.getElementById('editor').appendChild(textEditorIframe);
}

//=======================
    function str_replace(search, replace, subject) {
	return subject.split(search).join(replace);
    }
    function closeInfo(){
       document.getElementById("idiv").style.display = "none";
       $main.infoClosed();                                    
    }
    function closeSettings(){
       document.getElementById("sdiv").style.display = "none";
       $main.settingsClosed();                                    
    }
    function thisMovie(movieName) {
        var isIE = navigator.appName.indexOf("Microsoft") != -1;
        return (isIE) ? window[movieName] : document[movieName];
    }

    //function onDataUploaded(fileName, filePath, isLast) {
    function onDataUploaded(fileName, filePath, isLast, isOriginal, description){
        //alert(fileName+", "+ filePath+", "+ isLast+", "+isOriginal+", "+description);             
        
        thisMovie("cms").onDataUploaded(fileName, filePath, isLast, isOriginal, description);
    }
    function onUploadProgressed(percent) {
        thisMovie("cms").onUploadProgressed(percent);
    }
    function onCompleteUpload(){
    }
    function openJava(url,name,params) {
      javaWindow =  window.open(url,name,params);
       
    }
    function closeJava() {
       javaWindow.close();
    }
    function openVideoNews(fld,file,w,h,type) {
       top = screen.availHeight/2 - (h+300)/2 - 15;
       left = screen.availWidth/2 - (w+300)/2;
       window.open(base_path+"video.php?file="+escape(file)+"&h="+h+"&w="+w,"video","height="+(h+300)+",width="+(w+300)+",top="+top+",left="+left);
}
    function openVideo(fld,file,w,h,type) {
       top = screen.availHeight/2 - (h+300)/2 - 15;
       left = screen.availWidth/2 - (w+300)/2;
       window.open(base_path+"utils/video.php?file="+escape(fld)+escape(file)+"&h="+h+"&w="+w,"video","height="+(h+300)+",width="+(w+300)+",top="+top+",left="+left);
    }
    
    function openOriginal(url){
       window.open(url,"original","height="+document.height+",width="+document.width+",top="+0+",left="+0);

    }

//-->
