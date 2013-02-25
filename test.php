<?php 
if(isset($_GET['listes'])){
	$listes = getAllListe();
	foreach($listes as $res){
		$date = $res->date();
		if(strpos($date, '2012') == false){
			$explode = explode(" ", $date);
			$date = "";
			$date .= $explode[1];
			$date .= " ";
			switch($explode[2]){
				case 'janvier':
					$date .= "January";
					$date .= " ";
					break;
				case 'février':
					$date .= "February";
					$date .= " ";
					break;
				case 'mars':
					$date .= "March";
					$date .= " ";
				break;
				case 'avril':
					$date .= "April";
					$date .= " ";
				break;
				case 'mai':
					$date .= "May";
					$date .= " ";
				break;
				case 'juin':
					$date .= "June";
					$date .= " ";
				break;
				case 'juillet':
					$date .= "July";
					$date .= " ";
				break;	
				case 'août':
					$date .= "August";
					$date .= " ";
				break;
				case 'septembre':
					$date .= "September";
					$date .= " ";
				break;
				case 'octobre':
					$date .= "October";
					$date .= " ";
				break;
				case 'novembre':
					$date .= "November";
					$date .= " ";
				break;
				case 'décembre':
					$date .= "December";
					$date .= " ";
				break;			
			}
			$date .= "2012";
			$date .= " ";
			$date .= $explode[3];
		}else{
			$explode = explode(" ", $date);
			$date = "";
			$date .= $explode[1];
			$date .= " ";
			switch($explode[2]){
				case 'janvier':
					$date .= "January";
					$date .= " ";
					break;
				case 'février':
					$date .= "February";
					$date .= " ";
					break;
				case 'mars':
					$date .= "March";
					$date .= " ";
					break;
				case 'avril':
					$date .= "April";
					$date .= " ";
					break;
				case 'mai':
					$date .= "May";
					$date .= " ";
					break;
				case 'juin':
					$date .= "June";
					$date .= " ";
					break;
				case 'juillet':
					$date .= "July";
					$date .= " ";
					break;
				case 'août':
					$date .= "August";
					$date .= " ";
					break;
				case 'septembre':
					$date .= "September";
					$date .= " ";
					break;
				case 'octobre':
					$date .= "October";
					$date .= " ";
					break;
				case 'novembre':
					$date .= "November";
					$date .= " ";
					break;
				case 'décembre':
					$date .= "December";
					$date .= " ";
					break;
			}
			$date .= $explode[3];	
			$date .= " ";
			$date .= $explode[4];
		}
		$date = strtotime($date);
		$id = $res->id();
		mysql_query('UPDATE listes_public SET date = "'.$date.'" WHERE id = "'.$id.'"')or die(mysql_error());
	}
}elseif(isset($_GET['commentaires'])){
	$listes = getAllCommentaires();
	foreach($listes as $res){
		$date = $res->date();
		if(strpos($date, '2012') == false){
			$explode = explode(" ", $date);
			$date = "";
			$date .= $explode[1];
			$date .= " ";
			switch($explode[2]){
				case 'janvier':
					$date .= "January";
					$date .= " ";
					break;
				case 'février':
					$date .= "February";
					$date .= " ";
					break;
				case 'mars':
					$date .= "March";
					$date .= " ";
					break;
				case 'avril':
					$date .= "April";
					$date .= " ";
					break;
				case 'mai':
					$date .= "May";
					$date .= " ";
					break;
				case 'juin':
					$date .= "June";
					$date .= " ";
					break;
				case 'juillet':
					$date .= "July";
					$date .= " ";
					break;
				case 'août':
					$date .= "August";
					$date .= " ";
					break;
				case 'septembre':
					$date .= "September";
					$date .= " ";
					break;
				case 'octobre':
					$date .= "October";
					$date .= " ";
					break;
				case 'novembre':
					$date .= "November";
					$date .= " ";
					break;
				case 'décembre':
					$date .= "December";
					$date .= " ";
					break;
			}
			$date .= "2012";
			$date .= " ";
			$date .= $explode[3];
		}else{
			$explode = explode(" ", $date);
			$date = "";
			$date .= $explode[1];
			$date .= " ";
			switch($explode[2]){
				case 'janvier':
					$date .= "January";
					$date .= " ";
					break;
				case 'février':
					$date .= "February";
					$date .= " ";
					break;
				case 'mars':
					$date .= "March";
					$date .= " ";
					break;
				case 'avril':
					$date .= "April";
					$date .= " ";
					break;
				case 'mai':
					$date .= "May";
					$date .= " ";
					break;
				case 'juin':
					$date .= "June";
					$date .= " ";
					break;
				case 'juillet':
					$date .= "July";
					$date .= " ";
					break;
				case 'août':
					$date .= "August";
					$date .= " ";
					break;
				case 'septembre':
					$date .= "September";
					$date .= " ";
					break;
				case 'octobre':
					$date .= "October";
					$date .= " ";
					break;
				case 'novembre':
					$date .= "November";
					$date .= " ";
					break;
				case 'décembre':
					$date .= "December";
					$date .= " ";
					break;
			}
			$date .= $explode[3];
			$date .= " ";
			$date .= $explode[4];
		}
		$date = strtotime($date);
		$id = $res->id();
		mysql_query('UPDATE commentaires SET date = "'.$date.'" WHERE id = "'.$id.'"')or die(mysql_error());
	}	
}
?>
<html>
<form id="maj">
	<input type="submit" name="listes" value="listes">
	<input type="submit" name="commentaires" value="commentaires">
</form>
</html>