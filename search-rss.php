<?php
require_once('require/class.Connection.php');
require_once('require/class.Spotter.php');
require_once('require/class.Language.php');
$Spotter = new Spotter();
if (isset($_GET['start_date'])) {
	//for the date manipulation into the query
	if($_GET['start_date'] != "" && $_GET['end_date'] != ""){
		$start_date = date("Y-m-d",strtotime($_GET['start_date']))." 00:00:00";
		$end_date = date("Y-m-d",strtotime($_GET['end_date']))." 00:00:00";
		$sql_date = $start_date.",".$end_date;
	} else if($_GET['start_date'] != ""){
		$start_date = date("Y-m-d",strtotime($_GET['start_date']))." 00:00:00";
		$sql_date = $start_date;
	} else if($_GET['start_date'] == "" && $_GET['end_date'] != ""){
		$end_date = date("Y-m-d H:i:s", strtotime("2014-04-12")).",".date("Y-m-d",strtotime($_GET['end_date']))." 00:00:00";
		$sql_date = $end_date;
	} else $sql_date = '';
} else $sql_date = '';

if (isset($_GET['highest_altitude'])) {
	//for altitude manipulation
	if($_GET['highest_altitude'] != "" && $_GET['lowest_altitude'] != ""){
		$end_altitude = filter_input(INPUT_GET,'highest_altitude',FILTER_SANITIZE_NUMBER_INT);
		$start_altitude = filter_input(INPUT_GET,'lowest_altitude',FILTER_SANITIZE_NUMBER_INT);
		$sql_altitude = $start_altitude.",".$end_altitude;
	} else if($_GET['highest_altitude'] != ""){
		$end_altitude = filter_input(INPUT_GET,'highest_altitude',FILTER_SANITIZE_NUMBER_INT);
		$sql_altitude = $end_altitude;
	} else if($_GET['highest_altitude'] == "" && $_GET['lowest_altitude'] != ""){
		$start_altitude = filter_input(INPUT_GET,'lowest_altitude',FILTER_SANITIZE_NUMBER_INT).",60000";
		$sql_altitude = $start_altitude;
	} else $sql_altitude = '';
} else $sql_altitude = '';

//calculuation for the pagination
if(!isset($_GET['limit'])) {
	if (!isset($_GET['number_results'])) {
		$limit_start = 0;
		$limit_end = 25;
		$absolute_difference = 25;
	} else {
		if ($_GET['number_results'] > 1000){
			$_GET['number_results'] = 1000;
		}
		$limit_start = 0;
		$limit_end = filter_input(INPUT_GET,'number_results',FILTER_SANITIZE_NUMBER_INT);
		$absolute_difference = filter_input(INPUT_GET,'number_results',FILTER_SANITIZE_NUMBER_INT);
	}
}  else {
	$limit_explode = explode(",", $_GET['limit']);
	$limit_start = filter_var($limit_explode[0],FILTER_SANITIZE_NUMBER_INT);
	$limit_end = filter_var($limit_explode[1],FILTER_SANITIZE_NUMBER_INT);
}

$absolute_difference = abs($limit_start - $limit_end);
$limit_next = $limit_end + $absolute_difference;
$limit_previous_1 = $limit_start - $absolute_difference;
$limit_previous_2 = $limit_end - $absolute_difference;

if ($_GET['download'] == "true")
{
	header('Content-disposition: attachment; filename="flightairmap.rss"');
}

header('Content-Type: application/rss+xml; charset=utf-8');

$date = date("D, d M Y H:i:s T", time());
if (isset($_GET['sort'])) $sort = $_GET['sort'];
else $sort = '';
$q = filter_input(INPUT_GET,'q',513);
$id = filter_input(INPUT_GET,'id',FILTER_SANITIZE_NUMBER_INT);
$registration = filter_input(INPUT_GET,'registratrion',513);
$aircraft = filter_input(INPUT_GET,'aircraft',513);
$manufacturer = filter_input(INPUT_GET,'manufacturer',513);
$highlights = filter_input(INPUT_GET,'highlights',513);
$airline = filter_input(INPUT_GET,'airline',513);
$airline_country = filter_input(INPUT_GET,'airline_country',513);
$airline_type = filter_input(INPUT_GET,'airline_type',513);
$airport = filter_input(INPUT_GET,'airport',513);
$airport_country = filter_input(INPUT_GET,'airport_country',513);
$callsign = filter_input(INPUT_GET,'callsign',513);
$owner = filter_input(INPUT_GET,'owner',513);
$pilot_id = filter_input(INPUT_GET,'pilot_id',513);
$pilot_name = filter_input(INPUT_GET,'pilot_name',513);
$departure_airport_route = filter_input(INPUT_GET,'departure_airport_route',513);
$arrival_airport_route = filter_input(INPUT_GET,'arrival_airport_route',513);
if ($id != '') {
	$spotter_array = $Spotter->getSpotterDataByID($id);
} else {
	$spotter_array = $Spotter->searchSpotterData($q,$registration,$aircraft,strtolower(str_replace("-", " ", $manufacturer)),$highlights,$airline,$airline_country,$airline_type,$airport,$airport_country,$callsign,$departure_airport_route,$arrival_airport_route,$owner,$pilot_id,$pilot_name,$sql_altitude,$sql_date,$limit_start.",".$absolute_difference,$sort,'');
} 
print '<?xml version="1.0" encoding="UTF-8" ?>';
print '<rss xmlns:flightairmap="http://'.$_SERVER['HTTP_HOST'].''.htmlentities($_SERVER['REQUEST_URI']).'" version="2.0">';

print '<channel>';
print '<title>FlightAirMap RSS Feed</title>';
print '<link>http://www.flightairmap.fr/</link>';
print '<description>The latest airplanes</description>';
print '<language>en-ca</language>';
print '<lastBuildDate>'.$date.'</lastBuildDate>';

if (!empty($spotter_array)) {
	foreach($spotter_array as $spotter_item) {
		print '<item>';
		print '<title>'.$spotter_item['ident'].' '.$spotter_item['airline_name'].' | '.$spotter_item['registration'].' '.$spotter_item['aircraft_name'].' ('.$spotter_item['aircraft_type'].') | '.$spotter_item['departure_airport'].' - '.$spotter_item['arrival_airport'].'</title>';
		print '<link>http://www.flightairmap.fr/flightid/'.$spotter_item['spotter_id'].'</link>';
		print '<guid isPermaLink="false">http://www.flightairmap.fr/flightid/'.$spotter_item['spotter_id'].'</guid>';
		print '<description>Ident: '.$spotter_item['ident'].' | Registration: '.$spotter_item['registration'].' | Aircraft: '.$spotter_item['aircraft_name'].' ('.$spotter_item['aircraft_type'].') | Airline: '.$spotter_item['airline_name'].' | Coming From: '.$spotter_item['departure_airport_city'].', '.$spotter_item['departure_airport_name'].', '.$spotter_item['departure_airport_country'].' ('.$spotter_item['departure_airport'].') | Flying to: '.$spotter_item['arrival_airport_city'].', '.$spotter_item['arrival_airport_name'].', '.$spotter_item['arrival_airport_country'].' ('.$spotter_item['arrival_airport'].') | Flew nearby on: '.date("M j, Y, g:i a T", strtotime($spotter_item['date_iso_8601'])).'</description>';
		print '<pubDate>'.$date.'</pubDate>';
		print '</item>';
	}
}
print '</channel>';
print '</rss>';
?>