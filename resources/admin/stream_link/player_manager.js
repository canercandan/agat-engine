// STREAMING	
function setStreamForms(content, container_id)
	{
	var streamForm = document.forms["streamForm"];
	
	streamForm.content_id.value = container_id;

	
	document.getElementById('streamPreview').innerHTML = content;
	streamForm.player_code.value = content;
	
	var playerWidth = extractHTML(content, 'width=');
	if(playerWidth != -1)
		{
		streamForm.new_width.value = playerWidth;	
		}
	
	var playerHeight = extractHTML(content, 'height=');
	if(playerHeight != -1)
		{
		streamForm.new_height.value = playerHeight;	
		}
	
	if(playerHeight == -1 && playerWidth == -1)
		{
		streamForm.stream_size.value = 'false';
		}
	else
		{
		streamForm.real_size.checked = false;
		streamForm.new_width.disabled = false;
		streamForm.new_height.disabled = false;
		}
	}

function showClass(myClassname, setShow) 
	{
	var node = document.getElementsByTagName("body")[0];
	
	var a = [];
	var re = new RegExp('\\b' + myClassname + '\\b');
	var els = node.getElementsByTagName("*");
	
	var j=els.length
	
	for(var i=0; i<j; i++)
		{
		if(re.test(els[i].className))
			{
			a.push(els[i]);
			}
		}

	if(setShow)
		{
		for(x in a)
			{
	   	a[x].style.display = "";
			}
		}
	else
		{
		for(x in a)
			{
	   	a[x].style.display = "none";
			}
		}
	}
	
function showStreamForms(formType)
	{
	if(formType == "new")
		{
		document.getElementById('newStreamForm').style.display = '';
		document.getElementById('streamForm').style.display = 'none';
		}
	else if(formType == "edit")
		{
		document.getElementById('newStreamForm').style.display = 'none';
		document.getElementById('streamForm').style.display = '';
		}
	}

function streamError(error)
	{
		document.getElementById("streamError").innerHTML = error;
	}

function setStreamSize()
	{
	var stream = document.forms["streamForm"];

	if(stream.real_size.checked)
		{
		stream.new_width.disabled = true;
		stream.new_height.disabled = true;
		}
	else
		{
		stream.new_width.disabled = false;
		stream.new_height.disabled = false;
		}
	}

function checkStreamForm() 
	{
	stream = document.forms["streamForm"];
	
	var error = ""

	var streamCode = stream.player_code.value;
	
	if(stream.player_code.value	== "")
		{
		error += '- Vous devez saisir un code de lecteur streaming.<br>';
		}
	
	var streamWidth = 0;
	var streamHeight = 0;
	if(!stream.real_size.checked)
		{
		streamWidth = stream.new_width.value;
		streamHeight = stream.new_height.value;
		if(stream.new_width.value == "" || streamHeight == "")
			{
			error += '- Vous devez saisir une hauteur ET une largeur pour le lecteur.<br>';
			}
		}

	if(error == "")
		{
		streamCode = remplaceStreamSize();
		
		if(streamCode.indexOf('<embed', 0) != -1)
			{
			begin = streamCode.indexOf('<embed', 0)+6;
			beginEmbed = streamCode.substring(0, begin);
			endEmbed = streamCode.substring(begin, streamCode.length);
			
			streamCode = beginEmbed+' wmode="transparent" '+endEmbed;
			document.forms["streamForm"].player_code.value = streamCode;
			}
		document.forms["streamForm"].submit();
		//alert(streamCode);
		}
	else
		{
		streamError(error);	
		}
	}
	
function updateStreamPreview()
	{
	var content = document.forms["streamForm"].player_code.value;
	
	if(content != "")
		{
		document.forms["streamForm"].player_code.value = content;
		document.getElementById("streamPreview").innerHTML = content;
		}
	else
		{
		streamError('Vous devez indiquer un code pour le lecteur. <br><a href="http://www.youtube.com/watch?v=1X9g-kHeTTU" target="blank">Exemple de page</a>');		
		}
	}
	
function remplaceStreamSize()
	{
	var stream = document.forms["streamForm"];
	var content = stream.player_code.value;
	var streamWidth = stream.new_width.value;
	var streamHeight = stream.new_height.value;	
		
	var contentLength = content.length;
	
	if(streamWidth != 0 && streamWidth != "")
		{
		for(var i=0; i<contentLength; i++)
			{
			if(content.indexOf('width=', i) != -1)
				{
				beginWidthValue = content.indexOf('width=', i)+6;
				endWidthValue = content.indexOf(' ', beginWidthValue);
				
				i = endWidthValue;
				
				beginWidthContent = content.substring(0, beginWidthValue);
				endWidthContent = content.substring(endWidthValue, contentLength);
				
				content = beginWidthContent+'"'+streamWidth+'"'+endWidthContent;
				}
			}
		}
	
	contentLength = content.length;
	
	if(streamHeight != 0 && streamHeight != "")
		{
		for(var j=0; j<contentLength; j++)
			{
			if(content.indexOf('height=', j) != -1)
				{
				
				beginHeightValue = content.indexOf('height=', j)+7;
				endHeightValue = content.indexOf(' ', beginHeightValue);
				
				j = endHeightValue;
				
				beginHeightContent = content.substring(0, beginHeightValue);
				endHeightContent = content.substring(endHeightValue, contentLength);
				
				content = beginHeightContent+'"'+streamHeight+'"'+endHeightContent;
				}
			}
		}
	
	return content;
	}

function streamOnlyInt(myInput)
	{
	var inputValue = document.forms["streamForm"].elements[myInput].value;
	var regex = /\D/;
	var notOnlyInt = regex.test(inputValue);
	
	if(notOnlyInt)
		{
		document.forms["streamForm"].elements[myInput].value = "";
		streamError("Vous devez indiquez un nombre entier.");
		}
	else
		{
		remplaceStreamSize();
		streamError("");
		}
	}
