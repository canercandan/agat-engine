function setDiv(div)
	{
	if(div == 'usersContent')
		{
		if(document.getElementById(div).style.display == 'none')
			{
			document.getElementById(div).style.display = '';
			document.getElementById('moduleContent').style.display = 'none';
			setBackgroundElement(document.getElementById('leftUsers'), './images/left_users_open.jpg');
			setBackgroundElement(document.getElementById('leftModule'), './images/left_module.jpg');
			}
		else
			{
			document.getElementById(div).style.display = 'none';
			document.getElementById('moduleContent').style.display = 'none';
			setBackgroundElement(document.getElementById('leftUsers'), './images/left_users.jpg');
			}
		}
	else if(div == 'moduleContent')
		{
		if(document.getElementById(div).style.display == 'none')
			{
			document.getElementById(div).style.display = '';
			document.getElementById('usersContent').style.display = 'none';
			setBackgroundElement(document.getElementById('leftUsers'), './images/left_users.jpg');
			setBackgroundElement(document.getElementById('leftModule'), './images/left_module_open.jpg');
			}
		else
			{
			document.getElementById(div).style.display = 'none';
			document.getElementById('usersContent').style.display = 'none';
			setBackgroundElement(document.getElementById('leftModule'), './images/left_module.jpg');
			}
		}
	}
	
function changeLeftMenuBackground(divName, show)
	{
	switch(divName)
		{
		case 'leftUsers':
			if(document.getElementById("usersContent").style.display == "none")
				{
				if(show)
					setBackgroundElement(document.getElementById(divName), 'images/left_users_hover.jpg');
				else
					setBackgroundElement(document.getElementById(divName), 'images/left_users.jpg');
				}
			break;

		case 'leftContent':
			if(show)
				setBackgroundElement(document.getElementById(divName), 'images/left_content_hover.jpg');
			else
				setBackgroundElement(document.getElementById(divName), 'images/left_content.jpg');
			break;

		case 'leftModule':
			if(document.getElementById("moduleContent").style.display == "none")
				{
				if(show)
					setBackgroundElement(document.getElementById(divName), 'images/left_module_hover.jpg');
				else
					setBackgroundElement(document.getElementById(divName), 'images/left_module.jpg');
				}
			break;
			
		case 'leftMoreModule':
			if(show)
				setBackgroundElement(document.getElementById(divName), 'images/left_more_module_hover.jpg');
			else
				setBackgroundElement(document.getElementById(divName), 'images/left_more_module.jpg');
			break;
		}
	}
	
function setBackgroundElement(elmt, bg)
	{
	if(document.all) 
		{
		elmt.style.setAttribute("cssText","background-image: url("+bg+");");	
		}
	else
		{
		elmt.setAttribute("style","background-image: url("+bg+");");	
		}
	}







