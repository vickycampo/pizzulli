/**
 * plugin.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2015 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/*global tinymce:true */

tinymce.PluginManager.add('code_cms', function(editor, url) {
    
                  var CM = "";  
	function showDialog() {
                                    
		var win = editor.windowManager.open({
			title: "Source code",
			body: {
				type: 'textbox',
				name: 'code',
				multiline: true,
				minWidth: editor.getParam("code_dialog_width", 600),
				minHeight: editor.getParam("code_dialog_height", Math.min(tinymce.DOM.getViewPort().h - 200, 500)),
				spellcheck: false,
				style: 'direction: ltr; text-align: left'
			},
			onSubmit: function(e) {
				// We get a lovely "Wrong document" error in IE 11 if we
				// don't move the focus to the editor before creating an undo
				// transation since it tries to make a bookmark for the current selection
				editor.focus();

				editor.undoManager.transact(function() {
					editor.setContent(e.data.code);
				});

				editor.selection.setCursorLocation();
				editor.nodeChanged();
			}
		});

		// Gecko has a major performance issue with textarea
		// contents so we need to set it when all reflows are done
		win.find('#code').value(editor.getContent({source_view: true}));
	}

	editor.addCommand("mceCodeEditor", showDialog);

	editor.addButton('code_cms', {
		classes:"code_cms",
                                    image : url + '/_text_button_developer.png',
		tooltip: 'DEV.',
		onclick: function() {
                                                     
                                                      if($("#content_code").length===0){
                                                                var height = $(".mce-edit-area iframe#content_ifr").height();
                                                                var width = $(".mce-edit-area iframe#content_ifr").width();
                                                                console.log(height+' '+width);
                                                                $(".mce-edit-area iframe#content_ifr").before('<textarea style="width:100%;height:'+height+'px;" id="content_code">'+editor.getContent({source_view: true})+'</textarea>');
                                                                $(".mce-edit-area iframe#content_ifr").hide();
                                                                $('.mce-code_cms').addClass("backRed");
                                                                
                                                                CM = CodeMirror.fromTextArea(document.getElementById('content_code'), {
                                                                          value:editor.getContent({source_view: true}),
                                                                          mode : "htmlmixed",
                                                                          htmlMode: true,
                                                                          tabMode: "indent",
                                                                          //lineNumbers: true,
                                                                          lineWrapping: true,
                                                                          matchBrackets: true
                                                                });
                                                                
                                                                /// alert(width);
                                                                
                                                                CM.setSize(width, height);
                                                                
                                                                
                                                       }else{
                                                                
                                                                editor.focus();
                                                               
                                                               
                                                                
                                                                var html =  CM.getValue();// $("#content_code").text();
                                                                //editor.undoManager.transact(function() {
                                                                editor.setContent(html, {format : 'raw'});
                                                                //});
                                                                editor.selection.setCursorLocation();
                                                                editor.nodeChanged();
                                                                
                                                                 $("#content_code").remove();
                                                                 $(".CodeMirror").remove();
                                                                 $(".mce-edit-area iframe#content_ifr").show();
                                                                 $('.mce-code_cms').removeClass("backRed");
                                                       }
                                                    
                                    }
	});

	
});