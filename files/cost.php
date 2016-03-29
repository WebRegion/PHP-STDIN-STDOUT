<?php 
//$time_start = microtime(true);  // для показа времени выполнения раскомментировать 2,74,75,76 строки

$city=array();
$stats=array();

function loadcsv($name) { // загружаем таблицы
$spr=array();	
$fp = fopen(__DIR__."/tmp/".$name.".csv", "r"); 
while (!feof($fp)) { 
  $spr[] = fgetcsv($fp, 1024, ";");
}
unset ($spr[0]);
return $spr;	
}

function kopaem($elem,$line) { // копаем вглубь, ищем родителя
  global $city;	
  $parent_id = null;
  foreach ($city as $key_city=>$val_city) {
	  if ($val_city[2]==$elem) {
		$parent_id=$val_city[3]; 
		break;
	  }	
	}
  $line=$parent_id.",".$line;	
  poisk($line);
}

function find_table($arr,$city,$sobj) { // ищем данные в таблице статс
	$cena=null;
  	foreach ($arr as $key_arr=>$val_arr) {
	  if ($val_arr[3]==$city) {
		if ($val_arr[0]<=$sobj && $sobj<=$val_arr[1]) { 
		   $cena=$val_arr[2];
		   break;
		}  
	  }	
	}
	return $cena;	
}

function poisk($lines) { // основная функция поиска
	global $stats;
	global $findstart;
	global $print_log;
	$line=explode(",",$lines);
	if ((is_numeric($line[1])) && (is_numeric($line[0]))) {
	   $stoimost=find_table($stats,$line[0],$line[1]);
		if ($stoimost==null) {
		  if ($line[0]<>'0') { kopaem($line[0],$line[1]); }
		  else {fwrite(STDOUT, (($print_log)?$findstart."\r\n":"")."false\r\n");}	
	  	} 
		else 
		{ fwrite(STDOUT, (($print_log)?$findstart."\r\n":"").$stoimost*$line[1]."\r\n"); }
	} else { fwrite(STDOUT,(($print_log)?$findstart." - ":"")."ошибка ввода\r\nНеверно введены входные данные\r\n");} 	
}

$stats=loadcsv('cost');
$city=loadcsv('city');

if (isset($argv[1])) {	
  $print_log=true;
  $findstart=$argv[1]; 
  poisk($argv[1]);
} 
else {
  while ($line = trim(fgets(STDIN))) {
	$print_log=false;  
	$findstart=$line; 
    poisk($line);
  }
}
/*$time_end = microtime(true);
$time = $time_end - $time_start;
fwrite(STDOUT,"Скрипт выполнялся $time секунд\n");*/
?>