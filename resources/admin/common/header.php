<html>
  <head>
    <title>AGAT - Administration</title>
    <link rel="stylesheet" type="text/css" href="../common/styles/common.css"/>
    <link rel="stylesheet" type="text/css" href="../common/styles/interface.css"/>
    <script type="text/javascript" src="common/main.js"></script>
  </head>
  <body>
    <div id="banner">
      <a href="index.php?action=destroy">Sign Out</a>
		</div>
		<div id="content">
			
			<div id="leftMenu">
				<div id="userAdmin" onClick="Javascript: showMenu('userAdmin');">Utilisateur</div>
				<div id="userAdminOptions" style="display:none">
					<ul>
						<li>G&eacute;rer les utilisateurs</li>
						<li>G&eacute;rer les groupes</li>
					</ul>
				</div>
				<div id="contentAdmin" onClick="Javascript: showMenu('contentAdmin');">Contenu du site</div>
				<div id="contentAdminOptions" style="display:none">
					<ul>
						<li>Page 1</li>
						<li>Page 2</li>
						<li>...</li>
					</ul>
				</div>
				<div id="moduleAdmin" onClick="Javascript: showMenu('moduleAdmin');">Modules</div>
				<div id="moduleAdminOptions" style="display:none">
					<ul>
						<li>Webmail</li>
						<li>CRM</li>
						<li>...</li>
					</ul>
				</div>
			</div>
			
			<div id="workArea"></div>
			<div id="rightMenu">
				<div id="info"></div>
				<div id="pub"></div>
			</div>
		</div>
		<div id="footer"></div>