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

tinymce.PluginManager.add('style', function(editor, url) {

	editor.addButton('style1', {
                                 title : editor.settings.s1_text,
                                 image : url + '/_text_button_style1.png',
                                onclick : function() {
                                // Add you own code to execute something on click
                                        editor.focus();
                                            tinymce.activeEditor.formatter.register('mycustomformat1', {
                                                    inline : 'span',
                                                    classes : ["style1"]
                                            });

                                           tinymce.activeEditor.formatter.toggle('mycustomformat1');
                                        //$('#content').tinymce().execCommand('mceReplaceContent', false, '<span class = "style1">{$selection}</span>');
                                }
    
                    });
                    
                    editor.addButton('style2', {
                                 title : editor.settings.s2_text,
                                 image : url + '/_text_button_style2.png',
                                onclick : function() {
                                // Add you own code to execute something on click
                                        editor.focus();
                                       
                                         tinymce.activeEditor.formatter.register('mycustomformat2', {
                                                    inline : 'span',
                                                    classes : ["style2"]
                                            });

                                           tinymce.activeEditor.formatter.toggle('mycustomformat2');
                                       
                                        //$('#content').tinymce().execCommand('mceReplaceContent', false, '<span class = "style2">{$selection}</span>');
                                }
    
                    });
                    
                    editor.addButton('style3', {
                                 title : editor.settings.s3_text,
                                 image : url + '/_text_button_style3.png',
                                onclick : function() {
                                // Add you own code to execute something on click
                                        editor.focus();
                                        //$('#content').tinymce().execCommand('mceReplaceContent', false, '<span class = "style3">{$selection}</span>');
                                         tinymce.activeEditor.formatter.register('mycustomformat3', {
                                                    inline : 'span',
                                                    classes : ["style3"]
                                            });

                                           tinymce.activeEditor.formatter.toggle('mycustomformat3');
                                }
    
                    });

	

	//editor.addShortcut('Meta+S', '', 'mceSave');
});
