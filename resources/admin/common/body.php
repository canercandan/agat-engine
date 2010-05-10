<div id="background">
			<div id="container">
				<div id="header">&nbsp;</div>
				<div id="middle">
					<div id="leftMenu">

						<div class="leftMenuOption" id="leftUsers" style="background-image: url('common/images/left_users.jpg')" 
							onMouseOut="Javascript: changeLeftMenuBackground('leftUsers', false);" 
							onMouseOver="Javascript: changeLeftMenuBackground('leftUsers', true);" 
							onClick="Javascript: setDiv('usersContent');"></div>
						<div id="usersContent" class="leftContent" style="display: none;">
							<div class="leftContentMiddleBackground">
								<div class="leftContentMiddle">
                                    <ul>
						                <li><a href="user.php">G&eacute;rer les utilisateurs</a></li>
						                <li><a href="group.php">G&eacute;rer les groupes</a></li>
					                </ul>
                                </div>
							</div>
						</div>

						<div class="leftMenuOption" id="leftContent" style="background-image: url('common/images/left_content.jpg')" 
							onMouseOut="Javascript: changeLeftMenuBackground('leftContent', false);" 
							onMouseOver="Javascript: changeLeftMenuBackground('leftContent', true);"
                            onClick="Javascript: window.open('../');">
						</div>
						
						<div class="leftMenuOption" id="leftModule" style="background-image: url('common/images/left_module.jpg')" 
							onMouseOut="Javascript: changeLeftMenuBackground('leftModule', false);" 
							onMouseOver="Javascript: changeLeftMenuBackground('leftModule', true);"
							onClick="Javascript: setDiv('moduleContent');">
						</div>
						<div class="leftContent" id="moduleContent" style="display: none">
							<div class="leftContentMiddleBackground">
								<div class="leftContentMiddle">module</div>
							</div>
						</div>
						
						<div class="leftMenuOption" id="leftMoreModule" style="background-image: url('common/images/left_more_module.jpg')" 
							onMouseOut="Javascript: changeLeftMenuBackground('leftMoreModule', false);" 
							onMouseOver="Javascript: changeLeftMenuBackground('leftMoreModule', true);">
						</div>
						
						<div id="leftBottom">&nbsp;</div>
					</div>
					<div id="workarea">

