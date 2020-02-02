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

tinymce.PluginManager.add('align_cms', function(editor, url) {

	editor.addButton('align_center', {
                                 title : 'ALIGN CENTER',
                                 image : url + '/_text_button_alignM.png',
                                onclick : function() {
                                // Add you own code to execute something on click
                                        editor.focus();
                                        //editor.selection.setContent('Hello world!');
                                        $('#content').tinymce().execCommand('justifycenter');
                                   }
                    });
                    
                    editor.addButton('align_left', {
                                 title : 'ALIGN LEFT',
                                 image : url + '/_text_button_alignL.png',
                                onclick : function() {
                                // Add you own code to execute something on click
                                        editor.focus();
                                    $('#content').tinymce().execCommand('justifyleft');
                                }
    
                    });
                    
                     editor.addButton('align_right', {
                                 title : 'ALIGN RIGHT',
                                 image : url + '/_text_button_alignR.png',
                                onclick : function() {
                                // Add you own code to execute something on click
                                        editor.focus();
                                       $('#content').tinymce().execCommand('justifyright');
                                }
    
                    });
                    
                

	

	//editor.addShortcut('Meta+S', '', 'mceSave');
});
