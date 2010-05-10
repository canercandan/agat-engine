//News
function setNewsImgOptions()
	{
	var articleForm = document.forms['newArticleForm'];
	var ckImage = articleForm.ck_image.checked;
	if(ckImage)
		{
		articleForm.new_image.disabled = true;
		articleForm.img_title.disabled = true;
		articleForm.img_description.disabled = true;
		articleForm.real_size.disabled = true;
		articleForm.proportions.disabled = true;
		articleForm.new_width.disabled = true;
		articleForm.new_width_unit.disabled = true;
		articleForm.new_height.disabled = true;
		articleForm.new_height_unit.disabled = true;
		}
	else
		{
		articleForm.new_image.disabled = false;
		articleForm.img_title.disabled = false;
		articleForm.img_description.disabled = false;
		articleForm.real_size.disabled = false;
		if(!articleForm.real_size.checked)
			{
			articleForm.proportions.disabled = false;
			articleForm.new_width.disabled = false;
			articleForm.new_width_unit.disabled = false;
			articleForm.new_height.disabled = false;
			articleForm.new_height_unit.disabled = false;
			}
		}
	}
	
function setNewsEditImgOptions()
	{
	var articleForm = document.forms['editArticleForm'];
	var ckImage = articleForm.ck_image.checked;
	var ckEditImage = articleForm.ck_edit_image.checked;
	if(ckEditImage)
		{
		articleForm.ck_image.disabled = true;
		articleForm.new_image.disabled = true;
		articleForm.img_title.disabled = true;
		articleForm.img_description.disabled = true;
		articleForm.real_size.disabled = true;
		articleForm.proportions.disabled = true;
		articleForm.new_width.disabled = true;
		articleForm.new_width_unit.disabled = true;
		articleForm.new_height.disabled = true;
		articleForm.new_height_unit.disabled = true;
		}
	else
		{
		articleForm.ck_image.disabled = false;
		if(ckImage)
			{
			articleForm.new_image.disabled = true;
			articleForm.img_title.disabled = true;
			articleForm.img_description.disabled = true;
			articleForm.real_size.disabled = true;
			articleForm.proportions.disabled = true;
			articleForm.new_width.disabled = true;
			articleForm.new_width_unit.disabled = true;
			articleForm.new_height.disabled = true;
			articleForm.new_height_unit.disabled = true;
			}
		else
			{
			articleForm.new_image.disabled = false;
			articleForm.img_title.disabled = false;
			articleForm.img_description.disabled = false;
			articleForm.real_size.disabled = false;
			if(!articleForm.real_size.checked)
				{
				articleForm.proportions.disabled = false;
				articleForm.new_width.disabled = false;
				articleForm.new_width_unit.disabled = false;
				articleForm.new_height.disabled = false;
				articleForm.new_height_unit.disabled = false;
				}
			}
		}
	}

function setNewsImgProportionOptions(formName)
	{
	if(document.forms[formName].real_size.checked)
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
	
function ckNewsImgExtension(filePath)
	{
	ext = getImageExtension(filePath);
	if(ext!=".jpg" && ext!=".gif" && ext!=".png" && ext!=".bnp" && ext!=".flv" && ext!=".swf")
		{
		newsError("Vous souhaitez charger un fichier '"+ext+"'\n cette extension n'est pas autoris&eacute;e !\n Seules les extensions suivantes sont autorisées :\n'JPG ; PNG ; GIF ; BMP ; FLV ; SWF'");
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
	
function newsError(error)
	{
	document.getElementById("newsError").innerHTML = error;
	}
		
function setNewsScaleOptions(formName)
	{
	if(document.forms[formName].real_size.checked)
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



function newsOnlyInt(myInput, formName)
	{
	var inputValue = document.forms[formName].elements[myInput].value;
	var regex = /\D/;
	var notOnlyInt = regex.test(inputValue);
	
	if(notOnlyInt)
		{
		document.forms[formName].elements[myInput].value = "";
		newsError("Vous devez indiquez un nombre entier.");
		}
	else
		{
		setNewsImgProportionOptions(formName);
		newsError("");
		}
	}

function checkNewsForm()
	{
	var error = "";
	error = ckNewsImageForm(error);
	error = ckNewsTextForm(error);
	error = ckNewsForm(error);
	
	if(error != "")
		{
		newsError(error);
		}
	else
		{
		formNews = document.forms['newArticleForm'];
		var content = tinyMCE.get('new_article_text').getContent();
		formNews.news_text.value = content;
		formNews.img_title.value = remplaceSpecialChar(formNews.img_title.value);
		formNews.submit();
		}
	}

function ckNewsImageForm(error)
	{
	var imageForm = document.forms["newArticleForm"];
	if(!imageForm.ck_image.checked)
		{
		if(imageForm.img_title.value == "")
			{
			error += "<br>- Vous devez indiquer un titre &agrave; votre image.";
			}
		if(imageForm.img_description.value == "")
			{
			error += "<br>- Vous devez indiquer une description &agrave; votre image.";
			}
		if(imageForm.new_image.value == "")
			{
			error += "<br>- Vous devez choisir une image.";
			}
		if(imageForm.real_size.checked == false)
			{
			if(imageForm.proportions.checked == true)
				{
				if(imageForm.new_width.value == "" && imageForm.new_height.value == "")
					{	
					error += "<br>- Vous devez indiquer une hauteur ou une largeur &agrave; votre image.";	
					}
				}
			else
				{
				if(imageForm.new_width.value == "" || imageForm.new_height.value == "")
					{	
					error += "<br>- Vous devez indiquer une hauteur et une largeur &agrave; votre image.";	
					}
				}
			}
		}
		return error;
	}
	
function ckNewsTextForm(error)
	{
	var content = tinyMCE.get('new_article_text').getContent();
	if(content == "")
		{
		error += "<br>- Vous devez saisir un texte pour ce nouvel article.";
		}
	return error;
	}
	
function ckNewsForm(error)
	{
	var newsForm = document.forms["newArticleForm"];
	if(!newsForm.ck_news_date.checked)
		{
		var day = newsForm.news_day.value;
		var month = newsForm.news_month.value;
		var year = newsForm.news_year.value;
		
		if(!isDate(day, month, year))
			{
			error += "<br>- Vous devez indiquer une date valide.";
			}
		}
	if(newsForm.news_title.value == "")
		{
		error += "<br>- Vous devez saisir un titre pour cet article.";	
		}
	if(!newsForm.ck_news_author.checked)
		{
		if(newsForm.news_author.value == "")
			{
			error += "<br>- Vous devez indiquer un auteur.";
			}
		}
		return error;
	}
	
//Check news edit form

function checkEditNews()
	{
	var error = "";
	error = ckEditNewsImage(error);
	error = ckEditNewsText(error);
	error = ckEditNews(error);
	
	if(error != "")
		{
		newsError(error);
		}
	else
		{
		formNews = document.forms['editArticleForm'];
		var content = tinyMCE.get('edit_article_text').getContent();
		formNews.news_text.value = content;
		if(!formNews.ck_edit_image.checked)
			formNews.img_title.value = remplaceSpecialChar(formNews.img_title.value);
		formNews.submit();
		}
	}

function ckEditNewsImage(error)
	{
	var imageForm = document.forms["editArticleForm"];
	if(!imageForm.ck_edit_image.checked)
		{
		if(!imageForm.ck_image.checked)
			{
			if(imageForm.img_title.value == "")
				{
				error += "<br>- Vous devez indiquer un titre &agrave; votre image.";
				}
			if(imageForm.img_description.value == "")
				{
				error += "<br>- Vous devez indiquer une description &agrave; votre image.";
				}
			if(imageForm.real_size.checked == false)
				{
				if(imageForm.proportions.checked == true)
					{
					if(imageForm.new_width.value == "" && imageForm.new_height.value == "")
						{	
						error += "<br>- Vous devez indiquer une hauteur ou une largeur &agrave; votre image.";	
						}
					}
				else
					{
					if(imageForm.new_width.value == "" || imageForm.new_height.value == "")
						{	
						error += "<br>- Vous devez indiquer une hauteur et une largeur &agrave; votre image.";	
						}
					}
				}
			}
		}
		return error;
	}
	
function ckEditNewsText(error)
	{
	var content = tinyMCE.get('edit_article_text').getContent();
	if(content == "")
		{
		error += "<br>- Vous devez saisir un texte pour ce nouvel article.";
		}
	return error;
	}
	
function ckEditNews(error)
	{
	var newsForm = document.forms["editArticleForm"];
	if(!newsForm.ck_news_date.checked)
		{
		var day = newsForm.news_day.value;
		var month = newsForm.news_month.value;
		var year = newsForm.news_year.value;
		
		if(!isDate(day, month, year))
			{
			error += "<br>- Vous devez indiquer une date valide.";
			}
		}
	if(newsForm.news_title.value == "")
		{
		error += "<br>- Vous devez saisir un titre pour cet article.";	
		}
	if(!newsForm.ck_news_author.checked)
		{
		if(newsForm.news_author.value == "")
			{
			error += "<br>- Vous devez indiquer un auteur.";
			}
		}
		return error;
	}

function loadEditNews(newsId)
	{
	var objet = 'admin/news/draw_news_edit_form.php?news_id='+newsId;
	var xhr_object = null;
	if(window.XMLHttpRequest) // Firefox
		{
	  xhr_object = new XMLHttpRequest();
		}
	else if(window.ActiveXObject) // Internet Explorer
		{
	  xhr_object = new ActiveXObject("Microsoft.XMLHTTP");
		}
	else 
		{ // XMLHttpRequest non supporté par le navigateur
			//A MODIFIER - REMPLACER PAR ADRESSE EMAIL
	  alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
	  return;
		}
	
	var file = file;
	
	xhr_object.open("GET", objet, true);
	
	xhr_object.onreadystatechange = function() 
		{
	    if(xhr_object.readyState == 4)
	  	    {
			document.getElementById("edit_news").innerHTML = xhr_object.responseText;
			tinyMCE.init(
                {
		        // General options
                mode : "exact",
                elements : "textManager, new_article_text, edit_article_text",
		        theme : "advanced",
                language : "fr",
		        plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount",

		        // Theme options
                theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect",
		        theme_advanced_buttons2 : "cut,copy,paste,pastetext,|,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		        theme_advanced_buttons3 : "tablecontrols,|,hr,|,ltr,rtl",
		        theme_advanced_buttons4 : "",
		        theme_advanced_toolbar_location : "top",
		        theme_advanced_toolbar_align : "left",
		        theme_advanced_statusbar_location : "bottom",
		        theme_advanced_resizing : false,

		        // Example content CSS (should be your site CSS)
		        content_css : "css/content.css",

		        // Drop lists for link/image/media/template dialogs
		        template_external_list_url : "lists/template_list.js",
		        external_link_list_url : "lists/link_list.js",
		        external_image_list_url : "lists/image_list.js",
		        media_external_list_url : "lists/media_list.js",

		        // Replace values for the template plugin
		        template_replace_values : 
                    {
			        username : "Some User",
			        staffid : "991234"
		            }
	            });
			}
		}  
	xhr_object.send(null);	
	}

function changeNewsPage(divId, startLimit, endLimit)
	{
	var objet = 'admin/news/styles/simple_blue.php?start_limit='+startLimit+'&end_limit='+endLimit;
	var xhr_object = null;
	if(window.XMLHttpRequest) // Firefox
		{
	  xhr_object = new XMLHttpRequest();
		}
	else if(window.ActiveXObject) // Internet Explorer
		{
	  xhr_object = new ActiveXObject("Microsoft.XMLHTTP");
		}
	else 
		{ // XMLHttpRequest non supporté par le navigateur
			//A MODIFIER - REMPLACER PAR ADRESSE EMAIL
	  alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
	  return;
		}
	
	var file = file;
	
	xhr_object.open("GET", objet, true);
	
	xhr_object.onreadystatechange = function() 
		{
	    if(xhr_object.readyState == 4)
            {
            document.getElementById(divId).innerHTML = xhr_object.responseText;
            }   
		}  
	xhr_object.send(null);		
	}
