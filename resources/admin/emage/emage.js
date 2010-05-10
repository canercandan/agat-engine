
function setImageForms(content)
	{
	var imgWidth = 0;
	var imgHeight = 0;
	
	imgDescription = extractHTML(content, 'title=');
    if(imgDescription != -1)
	    document.forms["imageForm"].imgDescription.value = imgDescription;	
	
	imgSrc = extractHTML(content, 'src=');
    document.getElementById("edit_img_title").innerHTML = imgSrc;
    document.forms['newImageForm'].old_image.value = imgSrc;
	document.forms['imageForm'].img_src.value = imgSrc;

	//Preview
	document.getElementById('imagePreview').innerHTML = '<img id="preview" src="'+imgSrc+'" title="'+imgDescription+'">';
	var imgWidth = document.getElementById("preview").width;
	var imgHeight = document.getElementById("preview").height;
	
	if(imgHeight <= 300 && imgWidth <= 300)
		{
        var scale = "Taille r&eacute;elle";
		}
	else if(imgHeight >= imgWidth)
		{
		document.getElementById("preview").height = 300;
		var scale = (Math.round((300/imgHeight)*10000)/100)+"%";
		}
    else if(imgWidth >= imgHeight)
        {
		document.getElementById("preview").width = 300;
		var scale = (Math.round((300/imgWidth)*10000)/100)+"%";
        }
	
	document.getElementById('imageScale').innerHTML = 'Aper&ccedil;u (<em>'+scale+'</em>) :';	
	
	//Set input size
	var getWidth = content.indexOf('width=');
	var getHeight = content.indexOf('height=');
	
	if(getWidth != -1 || getHeight != -1)
		{
		document.forms["imageForm"].realSize.checked = false;
		
		if((getWidth == -1 && getHeight != -1) || (getWidth != -1 && getHeight == -1))
			{
			document.forms["imageForm"].proportions.checked = true;
			}
		else if(getWidth != -1 && getHeight != -1)
			{
			document.forms["imageForm"].proportions.checked = false;
			}
		setImgProportionOptions("imageForm");	
		
		document.forms["imageForm"].new_width.value = "";
		document.forms["imageForm"].new_height.value = "";
			
		if(getWidth != -1)
			{
			var imgWidth = extractHTML(content, 'width=');
			
			var widthUnit = 0;
			var widthValue = 0;
			
			var testPx1 = /\D/;
			var testPx2 = /px/;
			var testPercent = /%/;
			
			var resultPx1 = testPx1.test(imgWidth);
			var resultPx2 = testPx2.test(imgWidth);
			var resultPercent = testPercent.test(imgWidth);
	
			if(!resultPx1)
				{
				widthUnit = 0;
				widthValue = imgWidth;
				}
			else if(resultPx2)
				{
				endValue = imgWidth.indexOf('px');
				widthUnit = 0;
				widthValue = imgWidth.substring(0, endValue);
				}
			else if(resultPercent)
				{
				endValue = imgWidth.indexOf('%');
				widthUnit = 1;
				widthValue = imgWidth.substring(0, endValue);
				}
			document.forms["imageForm"].new_width.value = widthValue;
			document.forms["imageForm"].new_width_unit.selectedIndex = widthUnit;
			}
		else
			{
			document.forms["imageForm"].new_width_unit.selectedIndex = widthUnit;
			}
			
		if(getHeight != -1)
			{
			var imgHeight = extractHTML(content, 'height=');
			
			var unit = 0;
			var heightValue = 0;
			
				var heightTestPx1 = /\D/;
				var heightTestPx2 = /px/;
				var heightTestPercent = /%/;
				
				var heightResultPx1 = heightTestPx1.test(imgHeight);
				var heightResultPx2 = heightTestPx2.test(imgHeight);
				var heightResultPercent = heightTestPercent.test(imgHeight);
		
				if(!heightResultPx1)
					{
					heightUnit = 0;
					heightValue = imgHeight;
					}
				else if(heightResultPx2)
					{
					endValue = imgHeight.indexOf('px');
					heightUnit = 0;
					heightValue = imgHeight.substring(0, endValue);
					}
				else if(heightResultPercent)
					{
					endValue = imgHeight.indexOf('%');
					heightUnit = 1;
					heightValue = imgHeight.substring(0, endValue);
					}
				document.forms["imageForm"].new_height.value = heightValue;
				document.forms["imageForm"].new_height_unit.selectedIndex = heightUnit;
			}
		}
	else
		{
		document.forms["imageForm"].proportions.checked = true;
		document.forms["imageForm"].realSize.checked = true;
		document.forms["imageForm"].new_width.value = "";
		document.forms["imageForm"].new_height.value = "";
		}
	setImgProportionOptions("newImageForm");
	setImgProportionOptions("imageForm");
	}

function getImageExtension(filePath)
	{
	if (filePath != "")
		{
		nbChar = filePath.length;
		extension = filePath.substring(nbChar-4,nbChar);
		extension = extension.toLowerCase();
		return extension;
		}
 	}

function checkImageExtension(filePath)
	{
	ext = getImageExtension(filePath);
	if(ext!=".jpg" && ext!=".gif" && ext!=".png" && ext!=".bnp")
		{
		imgError("Vous souhaitez charger un fichier '"+ext+"'\n cette extension n'est pas autoris&eacute;e !\n Seules les extensions suivantes sont autorisées :\n'JPG ; PNG ; GIF ; BMP ; FLV ; SWF'");
		document.forms['newImageForm'].new_image.value = "";
		return false;
		}
	else if(filePath != "")
		{
		return true;
		}
	else
		{
		return false;
		document.forms['newImageForm'].new_image.value = "";
		}
	}

function showImageForms(formType)
	{
	if(formType == "new")
		{
		document.getElementById('newImageForm').style.display = '';
		document.getElementById('imageForm').style.display = 'none';
		}
	else if(formType == "edit")
		{
		document.getElementById('newImageForm').style.display = 'none';
		document.getElementById('imageForm').style.display = '';
		}
	}


function setImgProportionOptions(formName)
	{
	if(document.forms[formName].realSize.checked)
		{
		document.forms[formName].proportions.disabled = true;
		document.forms[formName].new_width.disabled = true;
		document.forms[formName].new_height.disabled = true; 
		document.forms[formName].new_height_unit.disabled = true; 
		document.forms[formName].new_width_unit.disabled = true; 
		document.forms[formName].new_width.value = "";
		document.forms[formName].new_height.value = "";
		}
	else
		{
		document.forms[formName].proportions.disabled = false;
		if(document.forms[formName].proportions.checked)
			{
			if(document.forms[formName].new_width.value != "")
				{
				document.forms[formName].new_width.disabled = false; 
				document.forms[formName].new_width_unit.disabled = false;
				document.forms[formName].new_height.disabled = true; 
				document.forms[formName].new_height_unit.disabled = true; 	
				document.forms[formName].new_height.value = "";
				}
			else if(document.forms[formName].new_height.value != "")
				{
				document.forms[formName].new_height.disabled = false; 
				document.forms[formName].new_height_unit.disabled = false;
				document.forms[formName].new_width.value = "";
				document.forms[formName].new_width.disabled = true;
				document.forms[formName].new_width_unit.disabled = true;	
				}
			else
				{
				document.forms[formName].new_width.disabled = false;
				document.forms[formName].new_width_unit.disabled = false;
				document.forms[formName].new_height.disabled = false; 
				document.forms[formName].new_height_unit.disabled = false; 
				}
			}
		else
			{
			document.forms[formName].new_width.disabled = false;
			document.forms[formName].new_width_unit.disabled = false;
			document.forms[formName].new_height.disabled = false; 
			document.forms[formName].new_height_unit.disabled = false; 
			}
		}
	}
	
function imgError(error)
	{
		document.getElementById("contentError").innerHTML = error;
	}
	
function checkNewImageForm()
	{
	var error = ""
	var newImageForm = document.forms["newImageForm"]
	if(newImageForm.new_image.value == "")
		{
		error += "<br>- Vous devez choisir une image.";
		}
	if(newImageForm.img_title.value == "")
		{
		error += "<br>- Vous devez indiquer un titre.";
		}
	if(newImageForm.imgDescription.value == "")
		{
		error += "<br>- Vous devez indiquer une description.";
		}		
	if(newImageForm.realSize.checked == false)
		{
		if(newImageForm.proportions.checked == true)
			{
			if(newImageForm.new_width.value == "" && newImageForm.new_height.value == "")
				{	
				error += "<br>- Vous devez indiquer une hauteur ou une largeur.";	
				}
			}
		else
			{
			if(newImageForm.new_width.value == "" || newImageForm.new_height.value == "")
				{	
				error += "<br>- Vous devez indiquer une hauteur et une largeur.";	
				}
			}
		}
		
	if(error != "")
		{
		imgError(error);
		}
	else
		{
		document.forms["newImageForm"].img_title.value = remplaceSpecialChar(document.forms["newImageForm"].img_title.value);
		newImageForm.submit();
		}
	}
	
function checkImageForm()
	{
	var error = "";
	var imageForm = document.forms["imageForm"];
    var src = imageForm.img_src.value;
    var image = "<img src='"+src+"' title='";

	if(imageForm.imgDescription.value == "")
		error += "<br>- Vous devez indiquer une description.";
    else
        image = image+imageForm.imgDescription.value+"' ";

	if(imageForm.realSize.checked == false)
		{
		if(imageForm.proportions.checked == true)
			{
			if(imageForm.new_width.value == "" && imageForm.new_height.value == "")
				error += "<br>- Vous devez indiquer une hauteur ou une largeur.";	
            else
                {
                if(imageForm.new_width.value != "")
                    image = image+" width='"+imageForm.new_width.value+"' ";
                else if(imageForm.new_height.value != "")
                    image = image+" height='"+imageForm.new_height.value+"' ";
                }
			}
		else
			{
			if(imageForm.new_width.value == "" || imageForm.new_height.value == "")
				error += "<br>- Vous devez indiquer une hauteur et une largeur.";	
			else
                image = image+" width='"+imageForm.new_width.value+"' height='"+imageForm.new_height.value+"'";
			}
		}
    image = image += ">";		
	if(error != "")
		{
		imgError(error);
		}
	else
		{
        savImage(image);
		}
	}

function savImage(image)
    {
    document.forms["imageForm"].content.value = image;
    document.forms["imageForm"].submit();
    }

