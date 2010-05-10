<?php
if(!empty($_POST['sav_type']))
	{
    $contentId = $_POST['content_id'];
    switch($_POST['sav_type'])
		{
        case 'new':
            if(file_exists($_POST['old_image']))
			    {
			    unlink($_POST['old_image']);
			    }
		
		    $imgSize = "";
		    if(empty($_POST['realSize']))
			    {
			    if(empty($_POST['proportions']))
				    {
				    $imgSize = ' width="'.$_POST['new_width'].$_POST['new_width_unit'].'" height="'.$_POST['new_height'].$_POST['new_height_unit'].'"';
				    }
			    else
				    {
				    if(empty($_POST['new_width']))
					    {
					    $imgSize = ' height="'.$_POST['new_height'].$_POST['new_height_unit'].'"';
					    }
				    else if(empty($_POST['new_height']))
					    {
					    $imgSize = ' width="'.$_POST['new_width'].$_POST['new_width_unit'].'"';
					    }
				    }
			    }
		
		    $newName = "";
		
		    if (isset($_FILES['new_image']['tmp_name']))
			    {
			    $fileName = $_FILES['new_image']['name'];
			    $newName = $_POST['img_title'].substr($fileName, -4, 4);
			    $savPath = './images/';
			    move_uploaded_file($_FILES['new_image']['tmp_name'], $savPath.$newName);
			    }
			
		    $content = '<img id="'.$_POST['img_title'].'" '.$imgSize.' src="./images/'.$newName.'" title="'.addSlashes($_POST['imgDescription']).'" class="dynamicImage">';
		
		    $req = "UPDATE variables SET value='".$content."' WHERE key='".$_POST['content_id']."'";
            if($newName != "")		
                savSQL($req);

		    $_POST = Array();
		    $_FILES = Array();
            break;
        case 'edit':
            $src = $_POST['img_src'];
	        $imgSize = "";
	        if(empty($_POST['realSize']))
		        {
		        if(empty($_POST['proportions']))
			        {
			        $imgSize = ' width="'.$_POST['new_width'].$_POST['new_width_unit'].'" height="'.$_POST['new_height'].$_POST['new_height_unit'].'"';
			        }
		        else
			        {
			        if(empty($_POST['new_width']))
				        {
				        $imgSize = ' height="'.$_POST['new_height'].$_POST['new_height_unit'].'"';
				        }
			        else if(empty($_POST['new_height']))
				        {
				        $imgSize = ' width="'.$_POST['new_width'].$_POST['new_width_unit'].'"';
				        }
			        }
		        }
	        $content = '<img id="nImg_'.$_POST['img_title'].'" '.$imgSize.' src="'.$src.'" title="'.addslashes($_POST['imgDescription']).'" class="dynamicImage">';
	        $req = "UPDATE variables SET value='".$content."' WHERE key='".$_POST['content_id']."'";
            savSQL($req);
	        $_POST = Array();
            break;
        case 'edit_text':
            $content = $_POST['content'];
            $content = stripslashes($content);
            $content = str_replace("'", "&#146;", $content);
            $req = "UPDATE variables SET value='".$content."' WHERE key='".$contentId."'";
            savSQL($req);
		    $_POST = Array();
            break;
        case 'edit_video':
            $content = $_POST['player_code'];
            $content = str_replace ( '"', "'", $content); 

            $req = 'UPDATE variables SET value="'.stripslashes($content).'" WHERE key="'.$contentId.'"';
            savSQL($req);
		    $_POST = Array();
            break;
        case 'del_news':
		    {
		    $articleId = $_POST['article_id'];
		
		    $req = "SELECT src FROM articles WHERE id=".$articleId;
            $res = execSQL($req);
            foreach($res as $items)
                list($src) = $items;
		
		    if(file_exists($src))
			    {
			    unlink($src);
			    }
		    if($articleId != "")
			    $req = "DELETE FROM articles WHERE id=".$articleId;
            savSQL($req);
		    $_POST = Array();
            break;
		    }
        case 'add_news':
            if(empty($_POST['ck_news_date']))
			    $date = $_POST['news_year'].'-'.$_POST['news_month'].'-'.$_POST['news_day'];
		    else
			    $date = "0000-00-00";
			
		    if(empty($_POST['ck_news_author']))
			    $author = addSlashes($_POST['news_author']);
		    else
			    $author = "-1";
		        $newsTitle = addSlashes($_POST['news_title']);
		
		        $src = '-1';
		        $imgTitle = '-1';
		        $imgWidth = '-1';
		        $imgHeight = '-1';
		        $error = false;
		        if(empty($_POST['ck_image']))
			        {
			        if(empty($_POST['real_size']))
				        {
				        if(empty($_POST['proportions']))
					        {
					        $imgWidth = $_POST['new_width'].$_POST['new_width_unit'];
					        $imgheight = $_POST['new_height'].$_POST['new_height_unit'];
					        }
				        else
					        {
					        if(empty($_POST['new_width']))
						        {
						        $imgheight = $_POST['new_height'].$_POST['new_height_unit'];
						        }
					        else if(empty($_POST['new_height']))
						        {
						        $imgWidth = $_POST['new_width'].$_POST['new_width_unit'];
						        }
					        }
				        }
			
		        $newName = "";
		        if (isset($_FILES['new_image']['tmp_name']))
			        {
			        if ($_FILES['new_image']['error']) 
                        {
	                    switch ($_FILES['new_image']['error'])
	                    	{
	                      case 1: // UPLOAD_ERR_INI_SIZE
	                      	echo"Le fichier d&eacute;passe la limite autoris&eacute;e par le serveur.";
	                      	$error = true;
	                        break;
	                      case 2: // UPLOAD_ERR_FORM_SIZE
	                        echo "Le fichier d&eacute;passe la limite autoris&eacute;e dans le formulaire.";
	                        $error = true;
	                        break;
	                      case 3: // UPLOAD_ERR_PARTIAL
	                        echo "L'envoi du fichier a &eacute;t&eacute; interrompu pendant le transfert.";
	                        $error = true;
	                        break;
	                      case 4: // UPLOAD_ERR_NO_FILE
	                        echo "Le fichier que vous avez envoy&eacute; a une taille nulle.";
	                        $error = true;
	                        break;
	                        }
					    }
				    else
					    {
					    $fileName = $_FILES['new_image']['name'];
					    $newName = $_POST['img_title'].substr($fileName, -4, 4);
					    $savPath = './images/';
					    if(file_exists($savPath.$newName))
						    {
						    $i = 0;
						    while(file_exists($savPath.$i.'_'.$newName))
							    {
							    $i++;
							    }
						    move_uploaded_file($_FILES['new_image']['tmp_name'], $savPath.$i.'_'.$newName);
						    $newName = $i.'_'.$newName;
						    }
					    else
						    move_uploaded_file($_FILES['new_image']['tmp_name'], $savPath.$newName);
					
					    $src = $savPath.$newName;
					    }
				    }
				
			    $imgTitle = addSlashes($_POST['img_description']);
			    }
		    $text = $_POST['new_article_text'];
            $text = stripSlashes($text);            
            $text = str_replace("'", "&#146;", $text);
		    

		    if(!$error)
			    {
			    $req = "INSERT INTO articles (news_group, news_date, title, author, src, img_title, img_width, img_height, text) VALUES('".$_POST["news_id"]."', '".$date."', '".$newsTitle."', '".$author."', '".$src."', '".$imgTitle."', '".$imgWidth."', '".$imgHeight."', '".$text."')";
			    savSQL($req);
			    }
		    $_POST = Array();
		    $_FILES = Array();
            break;
        case 'update_news':
            $updateSQL = '';

		    $req = "SELECT src FROM articles WHERE id=".$_POST['news_id']." LIMIT 1";
            $res = execSQL($req);
            foreach($res as $items)
                {
                list($old_src) = $items;
                }
		    //$query = mysql_query($req)or die('Erreur SQL : <br>'.$req.'<br>'.mysql_error());
		    //$data = mysql_fetch_array($query);
		    //$old_src = $data[0];
		
		
		    if(empty($_POST['ck_news_date']))
			    $date = $_POST['news_year'].'-'.$_POST['news_month'].'-'.$_POST['news_day'];
		    else
			    $date = "0000-00-00";
		
		    $updateSQL .= " news_date='".$date."',";
			
		    if(empty($_POST['ck_news_author']))
			    $author = addSlashes($_POST['news_author']);
		    else
			    $author = "-1";
		
		    $updateSQL .= " author='".$author."',";
		
		    $newsTitle = addSlashes($_POST['news_title']);
		    $updateSQL .= " title='".$newsTitle."',";
		
		    $error = false;
		    if(empty($_POST['ck_edit_image']))
			    {
			    //Vérifier fichier
			    if(empty($_POST['ck_image']))
				    {
				    $imgSize = "";
				    $updateSQL .= " img_title='".$_POST['img_title']."',";
				
				    if(empty($_POST['real_size']))
					    {
					    if(empty($_POST['proportions']))
						    {
						    $updateSQL .= " img_width='".$_POST['new_width'].$_POST['new_width_unit']."',";
						    $updateSQL .= " img_height='".$_POST['new_height'].$_POST['new_height_unit']."',";
						    }
					    else
						    {
						    if(empty($_POST['new_width']))
							    {
							    $imgSize = " height='".$_POST['new_height'].$_POST['new_height_unit']."',";
							    $updateSQL .= " img_height='".$_POST['new_height'].$_POST['new_height_unit']."',";
							    }
						    else if(empty($_POST['new_height']))
							    {
							    $imgSize = " width='".$_POST['new_width'].$_POST['new_width_unit']."',";
							    $updateSQL .= " img_width='".$_POST['new_width'].$_POST['new_width_unit']."',";
							    }
						    }
					    }
			
				    $newName = "";
				    if (isset($_FILES['new_image']['tmp_name']) && $_FILES['new_image']['error'] != 4)
					    {
					    if ($_FILES['new_image']['error']) 
						    {
			          switch ($_FILES['new_image']['error'])
			          	{
			            case 1: // UPLOAD_ERR_INI_SIZE
			            	echo"Le fichier dépasse la limite autorisée par le serveur.";
			            	$error = true;
			              break;
			            case 2: // UPLOAD_ERR_FORM_SIZE
			              echo "Le fichier dépasse la limite autorisée dans le formulaire.";
			              $error = true;
			              break;
			            case 3: // UPLOAD_ERR_PARTIAL
			              echo "L'envoi du fichier a été interrompu pendant le transfert.";
			              $error = true;
			              break;
			            case 4: // UPLOAD_ERR_NO_FILE
			              echo "Le fichier que vous avez envoyé a une taille nulle.";
			              $error = true;
			              break;
			            }
						    }
					    else
						    {
						    $fileName = $_FILES['new_image']['name'];
						    $newName = $_POST['img_title'].substr($fileName, -4, 4);
						    $savPath = './images/';
						    if(file_exists($savPath.$newName))
							    {
							    $i = 0;
							    while(file_exists($savPath.$i.'_'.$newName))
								    {
								    $i++;
								    }
							    move_uploaded_file($_FILES['new_image']['tmp_name'], $savPath.$i.'_'.$newName);
							    $newName = $i.'_'.$newName;
							    $updateSQL .= " src='".$savPath.$newName."',";
							    }
						    else
							    move_uploaded_file($_FILES['new_image']['tmp_name'], $savPath.$newName);
							    $updateSQL .= " src='".$savPath.$newName."',";
							    }
						    }
					    }
				    else
					    {
					    $updateSQL .= " src='-1',";
					    $updateSQL .= " img_title='-1',";
					    $updateSQL .= " img_width='-1',";
					    $updateSQL .= " img_height='-1',";
					    if(file_exists($old_src))
						    {
						    unlink($old_src);
						    }
					    }
				    }
			    $text = $_POST['edit_article_text'];
                $text = stripSlashes($text);            
                $text = str_replace("'", "&#146;", $text);
			    $updateSQL .= " text='".$text."'";

		    if(!$error)
			    {
			    //UPDATE clients_tbl SET prenom='Jacques' WHERE id=1
			    $req = "UPDATE articles SET ".$updateSQL." WHERE id=".$_POST['news_id'];
			    savSQL($req);
			    }
		    $_POST = Array();
		    $_FILES = Array();
            break;
		}
	}
	
	
?>
