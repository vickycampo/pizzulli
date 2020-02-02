function setupEditor(ed){
    
      ed.addButton('email', {
      title : 'email',
       image : 'img/_text_button_email.png',
       onclick : function() {
                                // Add you own code to execute something on click
                                        ed.focus();
                                        ed.selection.setContent('Hello world!');
                                }
    
         });
}

