<?php $params=explode("&", $_SERVER['QUERY_STRING']); $name=""; $lastName=""; $file=""; $xyname=""; $xyposition=""; $position=""; $size=""; foreach($params as $param){$value=explode("=", $param); if($value[0]=="name"){$name=$value[1];}elseif($value[0]=="lastname"){$lastName=$value[1];}elseif($value[0]=="file"){$file=$value[1];}elseif($value[0]=="xyname"){$xyname=$value[1];}elseif($value[0]=="xyposition"){$xyposition=$value[1];}elseif($value[0]=="position"){$position=$value[1];}elseif($value[0]=="size"){$size=$value[1];}}if(strlen($name)==0 || strlen($lastName)==0){echo "Por favor completar campos obligatorios"; return;}require_once('pdf/fpdf.php'); require_once('pdf/fpdi.php'); $file=urldecode($file); $fullPathToFile=getcwd()."/images/".$file.".pdf"; $pdf=new FPDI(); $pdf->AddFont('Mtcorsva','','Mtcorsva.php'); $pdf->AddPage("L"); $pdf->setSourceFile($fullPathToFile); $tplIdx=$pdf->importPage(1); $pdf->useTemplate($tplIdx, 0, 0, 0, 0, true); $pdf->SetFont('Mtcorsva', '', 24); $pdf->SetTextColor(0, 0, 0); $posName=explode(",", $xyname); $pdf->SetXY($posName[0], $posName[1]); $pdf->Write(0, urlRawDecode($lastName).urlRawDecode($name)); $posPosition=explode(",", $xyposition); $pdf->SetXY($posPosition[0], $posPosition[1]); $pdf->Write(0, urlRawDecode($position)); if($size=="480" || $size=="320"){$pdf->Output();}else{$pdf->Output($file.".pdf", "D");}function urlRawDecode($raw_url_encoded){if(preg_match_all("/\%C3\%([A-Z0-9]{2})/i", $raw_url_encoded,$res)){$res=array_unique($res=$res[1]); $arr_unicoded=array(); foreach($res as $key=> $value){if($value=="A1"){$arr_unicoded[]=chr(0xe1);}elseif($value=="A9"){$arr_unicoded[]=chr(0xe9);}elseif($value=="AD"){$arr_unicoded[]=chr(0xed);}elseif($value=="B3"){$arr_unicoded[]=chr(0xf3);}elseif($value=="BA"){$arr_unicoded[]=chr(0xfa);}elseif($value=="B1"){$arr_unicoded[]=chr(0xf1);}elseif($value=="BC"){$arr_unicoded[]=chr(0xfc);}elseif($value=="81"){$arr_unicoded[]=chr(0xc1);}elseif($value=="89"){$arr_unicoded[]=chr(0xc9);}elseif($value=="8D"){$arr_unicoded[]=chr(0xcd);}elseif($value=="93"){$arr_unicoded[]=chr(0xd3);}elseif($value=="9A"){$arr_unicoded[]=chr(0xda);}elseif($value=="91"){$arr_unicoded[]=chr(0xd1);}elseif($value=="9C"){$arr_unicoded[]=chr(0xdc);}$res[$key]="%C3%" . $value;}$raw_url_encoded=str_replace( $res, $arr_unicoded, $raw_url_encoded );}return rawurldecode($raw_url_encoded);}?>