function getTextEditorData()
{
	return tinymce.activeEditor.getContent();
}

function setText(str)
{
	tinymce.activeEditor.setContent(str);
}

function setS3Dir(str)
{
	if($(".backRed"))
    {
		$(".backRed").trigger("click");
    }

    var editor = tinyMCE.get("content");
    editor.settings.s3dir = str + "media";
}