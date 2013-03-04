<?php


function getJsObjectToString($object){	
	$result = "";
	if(is_array($object)){
		$size = sizeof($object);
		foreach ($catByGroupe as $key => $categories){
			$size--;
			$icat = sizeof($categories);
			$categorieListejs = "";
			foreach ($categories as $categorie){
				$icat--;
				$categorieListejs .= '{value:"'.$categorie.'",text:"'.$categorie.'"}';
				if($icat > 0){
					$categorieListejs .= ",";
				}
			}
			$groupeListejs .= '{label:"'.$key.'",options:['.$categorieListejs.']}';
			if($igroupe > 0){
				$groupeListejs.=",";
			}
		}
		$javascriptObject = "[$groupeListejs]";
	}else{
		
	}
	
	return $result;
}

function comparatorNumber($a, $b){
	if ($a == $b) {
		return 0;
	}
	return ($a < $b) ? -1 : 1;
}

function callConstructor($instance, $constructName, $nbArgs, $args){
	$isValidConstruct = true;
	if (method_exists($instance, $constructName, $args)) {
		call_user_func_array(array($this, $constructName), $args);
	}else{
		trigger_error('Les arguments passé en parametre ne correspondent a aucun constructeur', E_USER_WARNING);
	}
}

function timestampToString($timestamp){
	if(preg_match("/[0-9]{10,20}/", $timestamp)){
  		setlocale(LC_TIME, 'fr_FR.UTF-8','fra');
  		return utf8_encode(strftime( "%A %d %B %Y %H:%M:%S", $timestamp));
 	}else{
 		 return $timestamp;
 	}
}

function startswith($hay, $needle) {
	return substr($hay, 0, strlen($needle)) === $needle;
}

function endswith($hay, $needle) {
	return substr($hay, -strlen($needle)) === $needle;
}
?>