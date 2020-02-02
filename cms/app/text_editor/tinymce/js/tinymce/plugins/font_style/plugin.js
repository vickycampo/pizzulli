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

tinymce.PluginManager.add('font_style', function(editor, url) {

	editor.addButton('b_cms', {
                                 title : 'BOLD',
                                 image : url + '/_text_button_bold.png',
                                onclick : function() {
                                // Add you own code to execute something on click
                                        editor.focus();
                                       // setNewFormat("strong");
                                       
                                     tinymce.activeEditor.formatter.register('mycustomformatstrong', {
                                                    inline : 'strong'//,
                                                   // styles : {color : '#ff0000'}
                                     });

                                    tinymce.activeEditor.formatter.toggle('mycustomformatstrong');
                                       
                                }
    
                    });
                    
                    editor.addButton('i_cms', {
                                 title : 'ITALIC',
                                 image : url + '/_text_button_italic.png',
                                onclick : function() {
                                // Add you own code to execute something on click
                                       editor.focus();
                                       //setNewFormat("em");
                                        tinymce.activeEditor.formatter.register('mycustomformatem', {
                                                    inline : 'em'//,
                                                   // styles : {color : '#ff0000'}
                                        });

                                        tinymce.activeEditor.formatter.toggle('mycustomformatem');
                                }
    
                    });
                    
        });
