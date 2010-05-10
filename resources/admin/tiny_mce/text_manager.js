function savTextContent()
	{
	var content = tinyMCE.get('textManager').getContent();
	
	document.forms["tiny_form"].content.value = content;
	document.forms["tiny_form"].submit();
	}
