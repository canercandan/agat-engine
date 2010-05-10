function editContent(display, containerId, contentId, contentType) 
	{
    var test = getScrollPosition();
    document.getElementById("mask").style.marginTop = test[1]+"px";
    document.getElementById("managementContainer").style.marginTop = (test[1]+150)+"px";

    document.getElementById('contentArea').value = "";
	document.getElementById('managementContainer').style.display = "none";
	document.getElementById('tinyForm').style.display = "none";
	document.getElementById('e_mage').style.display = "none";
	document.getElementById('streaming').style.display = "none";
	document.getElementById('mask').style.display = "none";
    document.getElementById('edit_news').style.display = "none";
	document.getElementById('add_news').style.display = "none";

	if(display == true)
		{
		document.getElementById('contentArea').value = containerId;
		document.getElementById('managementContainer').style.display = "";
		document.getElementById('mask').style.display = "";
		}

	if(contentType == "text")
		{
        var content = document.getElementById(containerId).innerHTML;
		content = content.toLowerCase();
		tinyMCE.get('textManager').setContent(content);
		document.getElementById('tinyForm').style.display = "";
        document.forms["tiny_form"].content_id.value = contentId;
		}
	else if(contentType == "image")
		{
        var content = document.getElementById(containerId).innerHTML;
		content = content.toLowerCase();
		document.getElementById('e_mage').style.display = "";
		document.forms["imageForm"].content_id.value = contentId;
		document.forms["newImageForm"].content_id.value = contentId;
		setImageForms(content);
		}
	else if(contentType == "video")
		{
		contentLength = content.length;
		var begin = (content.indexOf("</div>"))+6;
		var result = content.substring(begin, contentLength);
		document.getElementById('streaming').style.display = "";
		setStreamForms(result, contentId);
		}
    else if(contentType == "add_news")
		{
		document.getElementById('news_manager').style.display = "";
		document.getElementById('add_news').style.display = "";
		document.forms["newArticleForm"].news_id.value = containerId;
		setNewsImgOptions();
		}
	else if(contentType == "edit_news")
		{
		document.getElementById('news_manager').style.display = "";
		document.getElementById('edit_news').style.display = "";
		loadEditNews(contentId);
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

function showMenu(menuName)
	{
	menuOptions = menuName+"Options";
	
	document.getElementById("userAdminOptions").style.display = "none";
	document.getElementById("contentAdminOptions").style.display = "none";
	document.getElementById("moduleAdminOptions").style.display = "none";
	
	document.getElementById(menuOptions).style.display = "";
	}

function extractHTML(content, myString)
	{
	nbChar = myString.length;
	if(content.indexOf(myString) != -1)
		{
		var begin = (content.indexOf(myString))+nbChar;
		var nextObjEqual = content.indexOf('=', begin);
		var nextObjRT = content.indexOf('>', begin);

		if(nextObjEqual < nextObjRT && nextObjEqual != -1)
			{
			var i = nextObjEqual;
			var endPos = 0;
			while(i>0 && endPos == 0)
				{
				if(content.charAt(i) == " ")
					{
					var space = true;
					while(space == true && i>0)
						{
						if(content.charAt(i) == " ")
							{
							endPos = i;	
							i -= 1;
							}
						else
							{
							space = false;
							}
						}
					
					}
				i -= 1;
				}
			}
		else
			{
			endPos = nextObjRT;
			}
		
		if(endPos != 0)
			{
			var result = content.substring(begin, endPos);
			if(result.charAt(0) == '"' || result.charAt(0) == "'")
				{
				result = result.substring(1, result.length);	
				}

			if(result.charAt(result.length-1) == '"' || result.charAt(result.length-1) == "'")
				{
				result = result.substring(0, result.length-1);
				}
			}
		else
			{
			var result = -1;	
			}	
		}
	else
		{
		var result = -1;	
		}
	return result;
	}

function getScrollPosition()
    {
    return Array((document.documentElement && document.documentElement.scrollLeft) || window.pageXOffset || self.pageXOffset || document.body.scrollLeft,(document.documentElement && document.documentElement.scrollTop) || window.pageYOffset || self.pageYOffset || document.body.scrollTop);
    }

function remplaceSpecialChar(myString)
	{
	myString = myString.replace(/(\"|\')/gi, '_');
	myString = myString.replace(/( )/gi, '_');
	myString = myString.replace(/(&#x40|&#064;|@|&commat;|&#x41|&#065;|A|&#x61|&#097;|&#xC0|&#192;|À|&Agrave;|&#xC1|&#193;|Á|&Aacute;|&#xC2|&#194;|Â|&Acirc;|&#xC3|&#195;|Ã|&Atilde;|&#xC4|&#196;|Ä|&Auml;|&#xC5|&#197;|Å|&Aring;|&#xE0|&#224;|à|&agrave;|&#xE1|&#225;|á|&aacute;|&#xE2|&#226;|â|&acirc;|&#xE3|&#227;|ã|&atilde;|&#xE4|&#228;|ä|&auml;|&#xE5|&#229;|å|&aring;)/gi, 'a');
	myString = myString.replace(/(&#xC7|&#199;|Ç|&Ccedil;|&#xE7|&#231;|ç|&ccedil;)/gi, 'c');
	myString = myString.replace(/(&#xD0|&#208;|Ð|&ETH;)/gi, 'd');
	myString = myString.replace(/(&#x45;|&#069;|E|&#x65;|&#101;|&#xC8;|&#200;|È|&Egrave;|&#xC9;|&#201;|É|&Eacute;|&#xCA;|&#202;|Ê|&Ecirc;|&#xCB;|&#203;|Ë|&Euml;|&#xE8;|&#232;|è|&egrave;|&#xE9;|&#233;|é|&eacute;|&#xEA;|&#234;|ê|&ecirc;|&#xEB;|&#235;|ë|&euml;)/gi, 'e');
	myString = myString.replace(/(&#x49|&#073;|I|&#x69|&#105;|&#xCC|&#204;|Ì|&Igrave;|&#xCD|&#205;|Í|&Iacute;|&#xCE|&#206;|Î|&Icirc;|&#xCF|&#207;|Ï|&Iuml;|&#xEC|&#236;|ì|&igrave;|&#xED|&#237;|í|&iacute;|&#xEE|&#238;|î|&icirc;|&#xEF|&#239;|ï|&iuml;)/gi, 'i');
	myString = myString.replace(/(&#x4E|&#078;|N|&#x6E|&#110;|&#xD1|&#209;|Ñ|&Ntilde;|&#xF1|&#241;|ñ|&ntilde;)/gi, 'n');
	myString = myString.replace(/(&#x4F|&#079;|O|&#x6F|&#111;|&#xD2|&#210;|Ò|&Ograve;|&#xD3|&#211;|Ó|&Oacute;|&#xD4|&#212;|Ô|&Ocirc;|&#xD5|&#213;|Õ|&Otilde;|&#xD6|&#214;|Ö|&Ouml;|&#xF2|&#242;|ò|&ograve;|&#xF3|&#243;|ó|&oacute;|&#xF4|&#244;|ô|&ocirc;|&#xF5|&#245;|õ|&otilde;|&#xF6|&#246;|ö|&ouml;|&#xF8|&#248;|ø|&oslash;)/gi, 'o');
	myString = myString.replace(/(&#x55|&#085;|U|&#x75|&#117;|&#xD9|&#217;|Ù|&Ugrave;|&#xDA|&#218;|Ú|&Uacute;|&#xDB|&#219;|Û|&Ucirc;|&#xDC|&#220;|Ü|&Uuml;|&#xF9|&#249;|ù|&ugrave;|&#xFA|&#250;|ú|&uacute;|&#xFB|&#251;|û|&ucirc;|&#xFC|&#252;|ü|&uuml;)/gi, 'u');
	myString = myString.replace(/(&#x59|&#089;|Y|&#x79|&#121;|&#xDD|&#221;|Ý|&Yacute;|&#xFD|&#253;|ý|&yacute;|&#xFF|&#255;|ÿ|&yuml;)/gi, 'y');
	myString = myString.replace(/(&#xC6|&#198;|Æ|&AElig;|&#xE6|&#230;|æ|&aelig;)/gi, 'ae ');
	myString = myString.replace(/(&#x8C|&#140;|Œ|&OElig;|&#x9C|&#156;|œ|&oelig;)/gi, 'oe ');
	return myString.toLowerCase();
	}

function isDate(day, month, year) 
	{
	if (day == "" | month == "" | year == "")
 		return false;

	j = day;
	m = month
	a = year

	if (a < 1000) 
		{
		if (a < 89) 
			a+=2000;
			
		else 
			a+=1900;
		}
	
	if (a%4 == 0 && a%100 !=0 || a%400 == 0)
		fev = 29;
	else 
		fev = 28;
	
	nbJours = new Array(31,fev,31,30,31,30,31,31,30,31,30,31);
	
	return ( m >= 1 && m <=12 && j >= 1 && j <= nbJours[m-1] );
	} 






