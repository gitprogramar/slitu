<?php $params=explode("&", $_SERVER['QUERY_STRING']);$name="";$lastName="";$dataBase="";foreach($params as $param){$value=explode("=", $param);if($value[0]=="name"){$name=$value[1];}elseif($value[0]=="lastname"){$lastName=$value[1];}elseif($value[0]=="database"){$dataBase=$value[1];}}if(strlen($name)==0 || strlen($lastName)==0 || strlen($dataBase)==0){echo "not found";return;}$lastname=formatString(rawurldecode($lastName));$name=formatString(rawurldecode($name));$userFound=false;echo "Search by: " .$lastname."&nbsp".$name."<br>";$fullPathToFile=getcwd()."/db/". $dataBase .".csv" ;$file=fopen($fullPathToFile,"r");while(! feof($file)){$row=fgetcsv($file);$rowArray=array($row[0], $row[1], $row[4]); $data=array_map("utf8_encode", $rowArray);if(strpos(formatString($data[1]), $name) !==false && formatString($data[0])==$lastname){$userFound=true;echo "Found: " . $data[0] . ", " . $data[1] . ", " .$data[2];break;}}if(!$userFound){echo "No se encontró el usuario en la base.";}fclose($file);function formatString($value){$arraySearch=array("á", "é", "í", "ó", "ú");$arrayReplace=array("a", "e", "i", "o", "u");return str_replace($arraySearch, $arrayReplace, strtolower($value));}?>