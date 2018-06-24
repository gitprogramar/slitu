<?php
	$params = explode("&", $_SERVER['QUERY_STRING']);
	//var_dump($params);
	$name = "";
	$lastName = "";
	$file = "";
	$xyname = "";
	$xyposition = "";
	$position = "";
	$size = "";
	foreach($params as $param) {		
		$value = explode("=", $param);
		//var_dump($value);
		if($value[0] == "name") {
			$name = $value[1];
		}
		elseif($value[0] == "lastname") {
			$lastName = $value[1];
		}
		elseif($value[0] == "file") {
			$file = $value[1];
		}
		elseif($value[0] == "xyname") {
			$xyname = $value[1];
		}
		elseif($value[0] == "xyposition") {
			$xyposition = $value[1];
		}
		elseif($value[0] == "position") {
			$position = $value[1];
		}
		elseif($value[0] == "size") {
			$size = $value[1];
		}
	} 
	if(strlen($name) == 0 || strlen($lastName) == 0) {		
		echo "Por favor completar campos obligatorios";
		return;
	}
	
	// TODO: check name and last name exists for security purposes
	//echo $name . " " .$lastName;
	//return;
	
	
	require_once('pdf/fpdf.php');
	require_once('pdf/fpdi.php');
	
	// Original file
	$file = urldecode($file);
	$fullPathToFile = getcwd()."/images/".$file.".pdf";
	
	
	// initiate FPDI
	$pdf = new FPDI();
	$pdf->AddFont('Mtcorsva','','Mtcorsva.php');
	// add a page
	$pdf->AddPage("L");
	// set the source file
	$pdf->setSourceFile($fullPathToFile);
	// import page 1
	$tplIdx = $pdf->importPage(1);
	// use the imported page 
	$pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);
	
	// now write some text above the imported page
	$pdf->SetFont('Mtcorsva', '', 24);
	$pdf->SetTextColor(0, 0, 0);
	$posName = explode(",", $xyname);
	$pdf->SetXY((int)$posName[0] /*- calculatePosition(urlRawDecode($lastName).urlRawDecode($name))*/, $posName[1]);
	$nameText = urlRawDecode($lastName).urlRawDecode($name);
	if(strlen($position) > 1) {
		//$nameText .= ",";
	}
	$pdf->Write(0, $nameText);
	$posPosition = explode(",", $xyposition);
	$pdf->SetXY((int)$posPosition[0] /*+ calculatePosition(urlRawDecode($lastName).urlRawDecode($name))*/, $posPosition[1]);
	$pdf->Write(0, urlRawDecode($position));

	if($size == "480" || $size == "320") {
		$pdf->Output(); // fix mobile and tablets
	}
	else {
		$pdf->Output($file.".pdf", "D"); // deskttop
	}
	
	function calculatePosition($text) {
		return strlen($text) + (strlen($text)*0.72);
		
	}
	
	function urlRawDecode($raw_url_encoded)
	{
		# Fix latin character problem
		/*
		á %C3%A1 
		é %C3%A9
		í %C3%AD
		ó %C3%B3
		ú %C3%BA
		*/
		if(preg_match_all("/\%C3\%([A-Z0-9]{2})/i", $raw_url_encoded,$res))
		{
			$res = array_unique($res = $res[1]);
			$arr_unicoded = array();
			
			foreach($res as $key => $value){
				
				if($value == "A1") { 
					$arr_unicoded[] = chr(0xe1); // á
				}
				elseif($value == "A9") { 
					$arr_unicoded[] = chr(0xe9); // é
				}
				elseif($value == "AD") { 
					$arr_unicoded[] = chr(0xed); // í
				}
				elseif($value == "B3") { 
					$arr_unicoded[] = chr(0xf3); // ó
				}
				elseif($value == "BA") { 
					$arr_unicoded[] = chr(0xfa); // ú
				}
				elseif($value == "B1") {
					$arr_unicoded[] = chr(0xf1); // ñ
				}
				elseif($value == "BC") {
					$arr_unicoded[] = chr(0xfc); // ü
				}
				elseif($value == "81") { 
					$arr_unicoded[] = chr(0xc1); // Á
				}
				elseif($value == "89") { 
					$arr_unicoded[] = chr(0xc9); // É
				}
				elseif($value == "8D") { 
					$arr_unicoded[] = chr(0xcd); // Í
				}
				elseif($value == "93") { 
					$arr_unicoded[] = chr(0xd3); // Ó
				}
				elseif($value == "9A") { 
					$arr_unicoded[] = chr(0xda); // Ú
				}
				elseif($value == "91") { 
					$arr_unicoded[] = chr(0xd1); // Ñ
				}
				elseif($value == "9C") { 
					$arr_unicoded[] = chr(0xdc); // Ü
				}
								
				$res[$key] = "%C3%" . $value;
			}

			$raw_url_encoded = str_replace(
									$res,
									$arr_unicoded,
									$raw_url_encoded
						);
		}
		
		# Return decoded  raw url encoded data 
		return rawurldecode($raw_url_encoded);
	}
	
?>