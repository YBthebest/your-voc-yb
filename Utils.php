<?php
$daysMap = array(
		"janvier"=>"January",
		"février"=>"February",
		"mars"=>"March",
		"avril"=>"April",
		"mai"=>"May",
		"juin"=>"June",
		"juillet"=>"July",
		"août"=>"August",
		"septembre"=>"September",
		"octobre"=>"October",
		"novembre"=>"November",
		"décembre"=>"December"
);

function convertArrayObjectToJSArray($arrayObject, $dropProperties=null){	
	$jsObject = "[";
	$i = 0;
	$size = sizeof($arrayObject);
	foreach($arrayObject as $object){
		$i++;
		$jsObject .= convertObjectToJs($object, $dropProperties);		
		if($i < $size){
			$jsObject .= ",";
		}
	}
	$jsObject .= "]";
	return $jsObject;
}

function convertObjectToJs($phpObject, $dropProperties=null){
	$pros = getClassProperties(get_class($phpObject));
	$jsObject = "{";
	$i=0;
	$size = sizeof($pros);
	foreach ($pros as $property) {		
		$i++;
		$property->setAccessible(true);		
		if(!contains($property->getName(), $dropProperties)){
			$value = $property->getValue($phpObject);
			if(is_array($value)){
				$firstValue = $value[0];
				if(is_array($firstValue)){
					$value = convertArrayObjectToJSArray($value);
				}else{
					$value = convertToJsArray($value);
				}
			}else if(is_object($value)){
				$value = convertObjectToJs();
			} else {
				$value = '"'.mysql_real_escape_string($value).'"';//mysql_real_escape_string()
			}
			$jsObject .= $property->getName().":".$value;
			if($i < $size){
				$jsObject .= ",";
			}
		}
	}
	$jsObject .= "}";
	return $jsObject;
}

function contains($find, $searchContainer){
	if($find === null){
		return false;
	}else{
		if(is_array($searchContainer)){
			return in_array($find, $searchContainer);
		}else{
			return $find == $searchContainer;
		}
	}
}

function convertToJsArray($array){
	$arrayJS = "[";
	$i = 0;
	foreach ($array as $value){
		if($i > 0){
			$arrayJS .= ",";			
		}
		$i++;
		$arrayJS .= '"'.$value.'"';
	}
	$arrayJS .= "]";
	return $arrayJS;
}

function getClassProperties($className){
	$ref = new ReflectionClass($className);
	$props = $ref->getProperties();
	$props_arr = array();
	foreach($props as $prop){
		$f = $prop->getName();
		//         if($prop->isPublic() and (stripos($types, 'public') === FALSE)) continue;
		//         if($prop->isPrivate() and (stripos($types, 'private') === FALSE)) continue;
		//         if($prop->isProtected() and (stripos($types, 'protected') === FALSE)) continue;
		//         if($prop->isStatic() and (stripos($types, 'static') === FALSE)) continue;
		$props_arr[$f] = $prop;
	}
	if($parentClass = $ref->getParentClass()){
		$parent_props_arr = getClassProperties($parentClass->getName());//RECURSION
		if(count($parent_props_arr) > 0)
			$props_arr = array_merge($parent_props_arr, $props_arr);
	}
	return $props_arr;
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
  		return utf8_encode(strftime("%A %d %B %Y %H:%M:%S", $timestamp));
 	}else{
 		 return $timestamp;
 	}
}

function stringDateToTimestamp($date){
	if(!preg_match("/[0-9]{10,20}/", $date)){
		$dateToParse = $date;
		if(!preg_match("/ 201[0-9] /", $date)){
			$dateToParse = preg_replace("/([0-9]{2}:[0-9]{2}:[0-9]{2})/","2012 $1", $date);
		}
		$explode = explode(" ", $dateToParse);
		$daysMap = $GLOBALS['daysMap'];
		$dateToParse = $explode[1]." ".$daysMap[$explode[2]]." ".$explode[3]." ".$explode[4];
		$timestamp = strtotime($dateToParse);
		return $timestamp;
	}else{
		return $date;
	}
}

function startswith($hay, $needle) {
	return substr($hay, 0, strlen($needle)) === $needle;
}

function endswith($hay, $needle) {
	return substr($hay, -strlen($needle)) === $needle;
}

function notEmpty($param){
	return isset($param) && !empty($param);
}
?>