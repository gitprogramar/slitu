<?php
	$params = explode("&", $_SERVER['QUERY_STRING']);
	//var_dump($params);
	$name = "";
	$lastName = "";
	$dataBase = "";
	foreach($params as $param) {		
		$value = explode("=", $param);
		//var_dump($value);
		if($value[0] == "name") {
			$name = $value[1];
		}
		elseif($value[0] == "lastname") {
			$lastName = $value[1];
		}
		elseif($value[0] == "database") {
			$dataBase = $value[1];
		}
	} 
	if(strlen($name) == 0 || strlen($lastName) == 0 || strlen($dataBase) == 0) {		
		echo "not found";
		return;
	}

	// name and lastname entered
	$lastname = formatString(rawurldecode($lastName));
	$name = formatString(rawurldecode($name));
	$userFound = false;
	$dataBaseRecord;
	
	// full DB file path
	$fullPathToFile = getcwd()."/db/". $dataBase .".csv" ;
	$file = fopen($fullPathToFile,"r");
	
	// loop throght the rows
	while(! feof($file))
	{
		$row = fgetcsv($file);
		$rowArray = array($row[0], $row[1], $row[4]); // lastname, name, position
		$data = array_map("utf8_encode", $rowArray);
		
		// find exactly by lastname and if contains some name
		if(strpos(formatString($data[1]), $name) !== false && formatString($data[0]) == $lastname) {
			$dataBaseRecord = $data[0] . ", " . $data[1] . ", " .$data[2];
			$userFound = true;
			break;
		}
	}

	// close file
	fclose($file);
	
	if($userFound) {
		echo "$dataBaseRecord";
	}
	else {
		echo "not found";
	}
	
	return;
	
	// format the name to avoid case sensitive and punctuations
	function formatString($value) {
		$arraySearch = array("á", "é", "í", "ó", "ú");
		$arrayReplace = array("a", "e", "i", "o", "u");
		return str_replace($arraySearch, $arrayReplace, strtolower($value));
	}
?>