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

/*
 1. Create a folder named "email" within "tinymce/plugins".
 2. Create a file called "plugin.min.js" within the folder.
 2. Paste the below code inside "tinymce/plugins/email/plugin.min.js"
 3. Extend your tiny.init like:
 tinymce.init({
 plugins: "email",
 toolbar: "email"
 });
 */

tinymce.PluginManager.add('email', function (editor, url) {



    editor.addButton('email', {
        title: 'CREATE EMAIL',
        image: url + '/_text_button_email.png',
        onclick: function () {
            // Open window
            editor.windowManager.open({
                title: 'CREATE EMAIL',
                id:'cmsDialog',
                maxHeight:210,
                
                body: [
                    {type: 'label', text: 'TITLE',height:20},
                    {type: 'textbox', name: 'title1',maxHeight:20,minWidth:390,id:"titleEmailInput"},
                    {type: 'label', text: 'EMAIL',height:20},
                    {type: 'textbox', name: 'title',maxHeight:20,minWidth:390,id:"emailInput"}
                ],
                onsubmit: function (e) {
                    // Insert content when the window form is submitted
                    //editor.insertContent('<a href="mailto:' + e.data.title + '">{$selection}</a>');
                    var titleEmail = e.data.title;
                    if(e.data.title1.length>0){
                        titleEmail = e.data.title1;
                    }
                     $('#content').tinymce().execCommand('mceReplaceContent', false, '<a target="_self" class="a_mail" title="'+titleEmail+'" href="mailto:' + e.data.title + '">{$selection}</a>');
                    
                },
                                    buttons: [{
                                                                     text: 'CANCEL',
                                                                     onclick: 'close',
                                                                     id: 'cnacelButton'
                                                      },
                                                      {
                                                                     text: 'DONE',
                                                                     onclick: 'submit',
                                                                     id: 'finishButton'
                                                      }
                                                  
                                                      ]
            });
        }

    });


});

/*
 tinymce.PluginManager.add('email', function(editor, url) {
 
 editor.addButton('email', {
 title : 'email',
 image : url + '/_text_button_email.png',
 onclick : function() {
 // Add you own code to execute something on click
 editor.focus();
 editor.selection.setContent('Hello world!');
 }
 
 });
 
 
 
 //editor.addShortcut('Meta+S', '', 'mceSave');
 });*/
