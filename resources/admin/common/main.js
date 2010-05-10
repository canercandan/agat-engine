function showMenu(menuName)
	{
	menuOptions = menuName+"Options";
	
	document.getElementById("userAdminOptions").style.display = "none";
	document.getElementById("contentAdminOptions").style.display = "none";
	document.getElementById("moduleAdminOptions").style.display = "none";
	
	document.getElementById(menuOptions).style.display = "";
	}