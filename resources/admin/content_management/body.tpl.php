		<input type="hidden" name="contentArea" id="contentArea" value="">
		<div id="mask" style="display:none; margin-top:100px;">&nbsp;</div>
			<div id="managementContainer" style="display:none;  margin-top:250px;">
				
                <div id="tinyForm" style="display:none">
					<?php require_once("admin/tiny_mce/text_manager.html"); ?>
				</div>			

				<div id="e_mage" style="display:none">
					<table>
						<tr>
							<td>
								<?php require_once("admin/emage/image_manager.html"); ?>
							</td>
						</tr>
					</table>
				</div>
				
                <div id="streaming" style="display:none">
					<table>
						<tr>
							<td>
								<?php require_once("admin/stream_link/player_manager.html"); ?>
							</td>
						</tr>
					</table>
				</div>

                <div id="news_manager" style="display:none">
					<table>
						<tr>
							<td>
								<?php require_once("admin/news/add_news_manager.html"); ?>
							</td>
						</tr>
					</table>
				</div>

			</div>
		</div>
