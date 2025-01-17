<?php
require_once('require/class.Connection.php');
require_once('require/class.Language.php');
require_once('require/class.Spotter.php');
require_once('require/class.SpotterArchive.php');
$Spotter=new Spotter();
if (isset($_GET['start_date'])) {
	//for the date manipulation into the query
	if($_GET['start_date'] != "" && $_GET['end_date'] != ""){
		$start_date = date("Y-m-d", strtotime($_GET['start_date']))." 00:00:00";
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
if(!isset($_GET['limit']))
{
	if (!isset($_GET['number_results']))
	{
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
	header('Content-disposition: attachment; filename="flightairmap.geojson"');
}

header('Content-Type: application/json');

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

$output = '{';
	$output .= '"type": "FeatureCollection",';
	$output .= '"features": [';
if (!empty($spotter_array)) {
	foreach($spotter_array as $spotter_item) {
		//waypoint plotting
		$output .= '{';  
		$output .= '"type": "Feature",';
		$output .= '"properties": {';
		$output .= '"id": "'.$spotter_item['spotter_id'].'",';
		$output .= '"ident": "'.$spotter_item['ident'].'",';
		$output .= '"registration": "'.$spotter_item['registration'].'",';
		$output .= '"aircraft_icao": "'.$spotter_item['aircraft_type'].'",';
		$output .= '"aircraft_name": "'.$spotter_item['aircraft_name'].'",';
		$output .= '"aircraft_manufacturer": "'.$spotter_item['aircraft_manufacturer'].'",';
		$output .= '"airline_name": "'.$spotter_item['airline_name'].'",';
		$output .= '"airline_icao": "'.$spotter_item['airline_icao'].'",';
		$output .= '"airline_iata": "'.$spotter_item['airline_iata'].'",';
		$output .= '"airline_country": "'.$spotter_item['airline_country'].'",';
		$output .= '"airline_callsign": "'.$spotter_item['airline_callsign'].'",';
		$output .= '"airline_type": "'.$spotter_item['airline_type'].'",';
		$output .= '"departure_airport_city": "'.$spotter_item['departure_airport_city'].'",';
		$output .= '"departure_airport_country": "'.$spotter_item['departure_airport_country'].'",';
		$output .= '"departure_airport_iata": "'.$spotter_item['departure_airport_iata'].'",';
		$output .= '"departure_airport_icao": "'.$spotter_item['departure_airport_icao'].'",';
		$output .= '"departure_airport_latitude": "'.$spotter_item['departure_airport_latitude'].'",';
		$output .= '"departure_airport_longitude": "'.$spotter_item['departure_airport_longitude'].'",';
		$output .= '"departure_airport_altitude": "'.$spotter_item['departure_airport_altitude'].'",'; 
		$output .= '"arrival_airport_city": "'.$spotter_item['arrival_airport_city'].'",';
		$output .= '"arrival_airport_country": "'.$spotter_item['arrival_airport_country'].'",';
		$output .= '"arrival_airport_iata": "'.$spotter_item['arrival_airport_iata'].'",';
		$output .= '"departure_airport_icao": "'.$spotter_item['arrival_airport_icao'].'",';
		$output .= '"arrival_airport_latitude": "'.$spotter_item['arrival_airport_latitude'].'",';
		$output .= '"arrival_airport_longitude": "'.$spotter_item['arrival_airport_longitude'].'",';
		$output .= '"arrival_airport_altitude": "'.$spotter_item['arrival_airport_altitude'].'",';
		if ($spotter_item['latitude'] != 0 && $spotter_item['latitude'] != 0) {
			$output .= '"latitude": "'.$spotter_item['latitude'].'",';
			$output .= '"longitude": "'.$spotter_item['longitude'].'",';
			$output .= '"altitude": "'.$spotter_item['altitude'].'",';
			$output .= '"ground_speed": "'.$spotter_item['ground_speed'].'",';
			$output .= '"heading": "'.$spotter_item['heading'].'",';
			$output .= '"heading_name": "'.$spotter_item['heading_name'].'",';
		}
		$output .= '"date": "'.date("c", strtotime($spotter_item['date_iso_8601'])).'"';
		$output .= '}';
		/*
		if ($spotter_item['waypoints'] != '') {
			$waypoint_pieces = explode(' ', $spotter_item['waypoints']);
			$waypoint_pieces = array_chunk($waypoint_pieces, 2);
			$output .= ',"geometry": {';
			$output .= '"type": "LineString",';
			$output .= '"coordinates": [';
			foreach ($waypoint_pieces as $waypoint_coordinate) {
				$output .= '[';
				$output .=  $waypoint_coordinate[1].', ';
				$output .=  $waypoint_coordinate[0];
				$output .= '],';
			}
			$output = substr($output, 0, -1);
			$output .= ']';
			$output .= '}';
		}
		*/
		$SpotterArchive = new SpotterArchive();
		$archive_data = $SpotterArchive->getAllArchiveSpotterDataById($spotter_item['flightaware_id']);
		if (!empty($archive_data)) {
			$output .= ',"geometry": {';
			$output .= '"type": "LineString",';
			$output .= '"coordinates": [';
			foreach ($archive_data as $coord_data) {
				$output .= '[';
				$output .=  $coord_data['longitude'].', ';
				$output .=  $coord_data['latitude'];
				$output .= '],';
			}
			$output = substr($output, 0, -1);
			$output .= ']';
			$output .= '}';
		}
		
		$output .= '},';
/*		//location of aircraft
		$output .= '{';  
		$output .= '"type": "Feature",';
		$output .= '"properties": {';
		$output .= '"id": "'.$spotter_item['spotter_id'].'",';
		$output .= '"ident": "'.$spotter_item['ident'].'",';
		$output .= '"registration": "'.$spotter_item['registration'].'",';
		$output .= '"aircraft_icao": "'.$spotter_item['aircraft_type'].'",';
		$output .= '"aircraft_name": "'.$spotter_item['aircraft_name'].'",';
		$output .= '"aircraft_manufacturer": "'.$spotter_item['aircraft_manufacturer'].'",';
		$output .= '"airline_name": "'.$spotter_item['airline_name'].'",';
		$output .= '"airline_icao": "'.$spotter_item['airline_icao'].'",';
		$output .= '"airline_iata": "'.$spotter_item['airline_iata'].'",';
		$output .= '"airline_country": "'.$spotter_item['airline_country'].'",';
		$output .= '"airline_callsign": "'.$spotter_item['airline_callsign'].'",';
		$output .= '"airline_type": "'.$spotter_item['airline_type'].'",';
		$output .= '"departure_airport_city": "'.$spotter_item['departure_airport_city'].'",';
		$output .= '"departure_airport_country": "'.$spotter_item['departure_airport_country'].'",';
		$output .= '"departure_airport_iata": "'.$spotter_item['departure_airport_iata'].'",';
		$output .= '"departure_airport_icao": "'.$spotter_item['departure_airport_icao'].'",';
		$output .= '"departure_airport_latitude": "'.$spotter_item['departure_airport_latitude'].'",';
		$output .= '"departure_airport_longitude": "'.$spotter_item['departure_airport_longitude'].'",';
		$output .= '"departure_airport_altitude": "'.$spotter_item['departure_airport_altitude'].'",'; 
		$output .= '"arrival_airport_city": "'.$spotter_item['arrival_airport_city'].'",';
		$output .= '"arrival_airport_country": "'.$spotter_item['arrival_airport_country'].'",';
		$output .= '"arrival_airport_iata": "'.$spotter_item['arrival_airport_iata'].'",';
		$output .= '"departure_airport_icao": "'.$spotter_item['arrival_airport_icao'].'",';
		$output .= '"arrival_airport_latitude": "'.$spotter_item['arrival_airport_latitude'].'",';
		$output .= '"arrival_airport_longitude": "'.$spotter_item['arrival_airport_longitude'].'",';
		$output .= '"arrival_airport_altitude": "'.$spotter_item['arrival_airport_altitude'].'",';
		$output .= '"latitude": "'.$spotter_item['latitude'].'",';
		$output .= '"longitude": "'.$spotter_item['longitude'].'",';
		$output .= '"altitude": "'.$spotter_item['altitude'].'",';
		$output .= '"ground_speed": "'.$spotter_item['ground_speed'].'",';
		$output .= '"heading": "'.$spotter_item['heading'].'",';
		$output .= '"heading_name": "'.$spotter_item['heading_name'].'",';
		$output .= '"date": "'.date("c", strtotime($spotter_item['date_iso_8601'])).'"';
		$output .= '},';
		$output .= '"geometry": {';
		$output .= '"type": "Point",';
		$output .= '"coordinates": [';
		$output .=  $spotter_item['longitude'].', ';	
		$output .=  $spotter_item['latitude'];
		$output .= ']';
		$output .= '}';
		$output .= '},';
*/
	}
}
$output  = substr($output, 0, -1);
$output .= ']';
$output .= '}';
print $output;
?>