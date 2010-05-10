<?php
require_once('../common/common.php');

$newsId = $_GET['news_id'];
$res = adminExecSQL("SELECT * FROM articles WHERE id=".$newsId." LIMIT 1");

foreach($res as $items)
    list($id, $newsId, $date, $title, $author, $src, $imgTitle, $imgWidth, $imgHeight, $text) = $items;

//Revoir date
$ckDate = "";
if($date != "0000-00-00")
	{
	$day = substr($date, -2);
	$month = substr($date, -5, 2);
	$year = substr($date, -10, 4);
	}
else
	{
	$ckDate = "checked";
	$day = "";
	$month = "";
	$year = "";
	}

$ckAuthor = "";
if($author == -1)
	{
	$author = "";
	$ckAuthor = "checked";
	}	

$ckImage = "";
$ckRealSize = "";
$ckProportion = "";
$percentWidthUnit = "";
$pxWidthUnit = "";
$percentHeightUnit = "";
$pxHeightUnit = "";
$imgUnitWidth = "";
$imgUnitHeight = "";
if($src == -1)
	{
	$src = "";
	$imgTitle = "";
	$imgWidth = "";
	$imgHeight = "";
	}
else
	{
	if($imgWidth == "-1" && $imgHeight == "-1")
		{
		$ckRealSize = "checked";
		}
	else
		{
		if(($imgWidth == "-1" && $imgHeight != "-1") || ($imgWidth != "-1" && $imgHeight == "-1"))
			$ckProportion = "checked";
			
		if($imgWidth != "-1")
			{
			if(ereg("px",$imgWidth))
				{
				$percentWidthUnit = "";
				$pxWidthUnit = "selected";
				$pos = strrpos($imgWidth, "p");
				$imgUnitWidth = substr($imgWidth, 0, $pos);
				}
			else if(ereg("%",$imgWidth))
				{
				$percentWidthUnit = "selected";
				$pxWidthUnit = "";
				
				$pos = strrpos($imgWidth, "%");
				$imgUnitWidth = substr($imgWidth, 0, $pos);
				}
			else
				{
				$percentWidthUnit = "";
				$pxWidthUnit = "selected";
				$imgUnitWidth = $imgWidth;
				}
			}
		
		if($imgHeight != "-1")
			{
			if(ereg("px",$imgHeight))
				{
				$percentHeightUnit = "";
				$pxHeightUnit = "selected";
				$pos = strrpos($imgHeight, "p");
				$imgUnitHeight = substr($imgHeight, 0, $pos);
				}
			else if(ereg("%",$imgHeight))
				{
				$percentHeightUnit = "selected";
				$pxHeightUnit = "";
				$imgUnitHeight = strstr($imgHeight, "%", true);
				$pos = strrpos($imgHeight, "%");
				$imgUnitHeight = substr($imgHeight, 0, $pos);
				}
			else
				{
				$percentHeightUnit = "";
				$pxHeighthUnit = "selected";
				$imgUnitHeight = $imgHeight;
				}
			}
		}
	}


?>
	<form name="editArticleForm" method="POST" action="" enctype="multipart/form-data">
	<table id="imgFromManagement">
		<tr>
			<td>
				<div id="addForm">
					<input type="hidden" name="news_id" value="<?php echo $id; ?>">
					<input type="hidden" name="news_text" value="">
					<input type="hidden" name="sav_type" value="update_news">
					<table class="newsTbl">
						<tr>
							<td><h2>Modifier cet article</h2></td>
						</tr>
						<tr>
							<td>
								<table>
									<tr>
										<td><strong>Titre : </strong></td>
										<td><input type="text" name="news_title" value="<?php echo $title; ?>"></td>
										<td>&nbsp;</td>
									</tr>	
									<tr>
										<td><strong>Date (<em>jour | mois | ann&eacute;e</em>) : </strong></td>
										<td><input type="text" name="news_day" class="news_date" value="<?php echo $day; ?>">&nbsp;
											<input type="text" name="news_month" class="news_date" value="<?php echo $month; ?>">&nbsp;
											<input type="text" name="news_year" class="news_year" value="<?php echo $year; ?>"></td>
										<td><input type="checkbox" name="ck_news_date" <?php echo $ckDate; ?>> Ne pas afficher la date.</td>
									</tr>
									<tr>
										<td><strong>Auteur de l'article : </strong></td>
										<td><input type="text" name="news_author" value="<?php echo $author; ?>"></td>
										<td><input type="checkbox" name="ck_news_author" <?php echo $ckAuthor; ?>> Ne pas afficher l'auteur.</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>
								<table>
									<tr>
										<td><strong>Image de l'article</strong></td>
									</tr>
									<tr>
										<td><input type="checkbox" name="ck_edit_image" checked onChange="Javascript: setNewsEditImgOptions();"> Ne pas modifier l'image.</td>
									</tr>
									<tr>
										<td><input type="checkbox" name="ck_image" <?php echo $ckImage; ?> disabled="true" onChange="Javascript: setNewsEditImgOptions();"> Ne pas afficher d'image.</td>
									</tr>
									<tr>
										<td><input type="file" name="new_image" disabled="true" onChange="Javascript: ckNewsImgExtension(this.value);"></td>
									</tr>
									<tr>
										<td>
											<table>
												<tr>
													<td><strong>Titre de l'image : </strong></td>
													<td><input type="text" name="img_title" disabled="true" value="<?php echo $imgTitle; ?>"></td>
												</tr>	
												<tr>
													<td><strong>Description de l'image: </strong></td>
													<td><textarea disabled="true" name="img_description"><?php echo $imgTitle; ?></textarea></td>
												</tr>	
											</table>
										</td>
									</tr>
									<tr>
										<td>
											<table>
												<tr>
													<td><strong>Affichage taille r&eacute;el</strong></td>
													<td><input type="checkbox" name="real_size" disabled="true" <?php echo $ckRealSize; ?> onChange="Javascript: setNewsScaleOptions('editArticleForm');"></td>
												</tr>
												<tr>
													<td><strong>Conserver les proportions</strong></td>
													<td><input type="checkbox" name="proportions" disabled="true" <?php echo $ckProportion; ?> onChange="Javascript: setNewsScaleOptions('editArticleForm');"></td>
												</tr>
											</table>
											<table class="proportionTbls">
												<tr>
													<td><strong>Largeur de l'image</strong></td>
													<td>
														<input type="text" name="new_width" onChange="Javascript: newsOnlyInt(this.name, 'editArticleForm');" disabled="true" value="<?php echo $imgUnitWidth; ?>">
													</td>
													<td>
														<select name="new_width_unit" disabled="true"><option <?php echo $pxWidthUnit; ?>>px</option><option <?php echo $percentWidthUnit; ?>>%</option></select>
													</td>
												</tr>
												<tr>
													<td><strong>Hauteur de l'image</strong></td>
													<td>
														<input type="text" name="new_height" onChange="Javascript: newsOnlyInt(this.name, 'editArticleForm');" disabled="true" value="<?php echo $imgUnitHeight;?>">
													</td>
													<td>
														<select name="new_height_unit" disabled="true"><option <?php echo $pxHeightUnit; ?>>px</option><option<?php echo $percentHeightUnit;?>>%</option></select>
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					
				</div>
			</td>
			<td valign="top">
				<h2>&nbsp;</h2>
				<table>
					<tr>
						<td><strong>Texte de l'article</strong></td>
					</tr><tr>
						<td><textarea id="edit_article_text" name="edit_article_text" rows="5" cols="40" style="400"><?php echo $text; ?></textarea></td>
					</tr>
				</table>
				<br/><br/><br/>
				<input class="inputButton" type="button" value="Enregistrer" onClick="Javascript: checkEditNews();"/>
				<input class="inputButton" type="button" value="Annuler" onClick="Javascript: editContent(false, '', 'images'); showClass('divStreaming', true);"/><br>
			</td>
		</tr>
	</table>
</form>
