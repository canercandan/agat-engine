<?php
	$_SESSION['admin'] = "test";

	if(!empty($_GET))
		{
		require_once("../../common/common.php");
		$startLimit = $_GET['start_limit'];
		$endLimit = $_GET['end_limit'];
		}
	
	$reqNum = "SELECT * FROM articles";
	$res = execSQL($reqNum);
    $nbResponses = 0;
    foreach($res as $items)
        {
        $nbResponses += 1;
        }
	$tread = 3;
	$nbPages = ceil($nbResponses/$tread);
	$i = 0;
	$j = $tread;
	$menu = "";
	while($i < $nbPages)
		{
		$start = $i*$tread;
		$end = ($i*$tread)+$tread;
		$page = $i+1;
		
		$menu .= '<td><font onClick="Javascript: changePage(\'my_news\', '.$start.', '.$end.');">'.$page.'</font></td>';
		$j = $j+$tread;
		$i++;
		}
	
	
	$req = "SELECT * FROM articles ORDER BY id LIMIT ".$startLimit.", ".$endLimit;
    $res = execSQL($req);

	if(!empty($_SESSION['admin']))
			{
			echo '<input type="button" value="Ajouter un article" class="formButton" onClick="Javascript: editContent(true, \'my_news\', \'-1\', \'add_news\'); showClass(\'divStreaming\', false);">';
			}

	if($nbResponses == 0)
		{
		echo '<h2>Aucune article pour le moment.</h2>';
		}
	else
		{
		//Menu
		echo '<div class="newsMenuContainer"><table class="newsMenu" align="center"><tr><td>Page :</td>'.$menu.'</tr></table></div>';
		
        foreach($res as $items)
            {
            list($id, $newsId, $date, $title, $author, $src, $imgTitle, $imgWidth, $imgHeight, $text) = $items;
			$imgSize = "";
			if($imgWidth != '-1')
				$imgSize .= ' width="'.$imgWidth.'"';
			if($imgHeight != '-1')
				$imgSize .= ' height="'.$imgHeight.'"';
				
			if($author == '-1')
				$author = "";
				
			if($date == "0000-00-00")
				$date = "";
			
			//Header
			echo '<div class="news_separator"><table><tr><td><strong>'.$title.'</strong></td><td class="news_right">'.$date.'</td></tr></table></div>';
			
			//Image
			echo '<div class="news_content"><table><tr><td>';
			
			if($src != '-1')
				echo '<img src="'.$src.'" '.$imgSize.' title="'.$imgTitle.'">';
			
			//Text
			echo '</td><td>'.$text.'</td></tr></table></div>';
			
			//Footer
			echo '<div class="news_separator"><table><tr><td><em>'.$author.'</em></td><td class="news_right">';
			if(!empty($_SESSION['admin']))
				{
				echo '<form method="POST" action="">
<input type="hidden" name="article_id" value="'.$id.'">
<input class="formButton" type="button" value="Modifier cet article" onClick="editContent(true, \'my_news\', \''.$id.'\', \'edit_news\'); showClass(\'divStreaming\', false);">
<input type="hidden" name="sav_type" value="del_news">
<br/><input class="formButton" type="submit" value="Supprimer cet article" >
</form>';
				}
			echo '</td></tr></table></div><br><br>';
            }
			
		echo '<div class="newsMenuContainer"><table class="newsMenu" align="center"><tr><td>Page :</td>'.$menu.'</tr></table></div>';
		}
?>
