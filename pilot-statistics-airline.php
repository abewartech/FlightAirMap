<?php
require_once('require/class.Connection.php');
require_once('require/class.Spotter.php');
require_once('require/class.Language.php');
if (!isset($_GET['pilot'])) {
        header('Location: '.$globalURL.'/pilot');
        die();
}
$Spotter = new Spotter();
$sort = filter_input(INPUT_GET,'sort',513);
$pilot = filter_input(INPUT_GET,'pilot',513);
$year = filter_input(INPUT_GET,'year',FILTER_SANITIZE_NUMBER_INT);
$month = filter_input(INPUT_GET,'month',FILTER_SANITIZE_NUMBER_INT);
$filter = array();
if ($year != '') $filter = array_merge($filter,array('year' => $year));
if ($month != '') $filter = array_merge($filter,array('month' => $month));
$spotter_array = $Spotter->getSpotterDataByPilot($pilot,"0,1", $sort,$filter);

if (!empty($spotter_array))
{
	$title = sprintf(_("Most Common Airlines of %s"),$spotter_array[0]['pilot_name']);
	require_once('header.php');
	print '<div class="info column">';
	print '<h1>'.$spotter_array[0]['pilot_name'].'</h1>';
//	print '<div><span class="label">'._("Ident").'</span>'.$spotter_array[0]['ident'].'</div>';
//	print '<div><span class="label">'._("Airline").'</span><a href="'.$globalURL.'/airline/'.$spotter_array[0]['airline_icao'].'">'.$spotter_array[0]['airline_name'].'</a></div>'; 
	print '</div>';

	include('pilot-sub-menu.php');
	print '<div class="column">';
	print '<h2>'._("Most Common Airlines").'</h2>';
	print '<p>'.sprintf(_("The statistic below shows the most common airlines of flights owned by <strong>%s</strong>."),$spotter_array[0]['pilot_name']).'</p>';

	$airline_array = $Spotter->countAllAirlinesByPilot($pilot,$filter);
	if (!empty($airline_array))
	{
		print '<div class="table-responsive">';
		print '<table class="common-airline table-striped">';
		print '<thead>';
		print '<th></th>';
		print '<th></th>';
		print '<th>'._("Airline").'</th>';
		print '<th>'._("Country").'</th>';
		print '<th>'._("# of times").'</th>';
		print '<th></th>';
		print '</thead>';
		print '<tbody>';
		$i = 1;
		foreach($airline_array as $airline_item)
		{
			print '<tr>';
			print '<td><strong>'.$i.'</strong></td>';
			print '<td class="logo">';
			print '<a href="'.$globalURL.'/airline/'.$airline_item['airline_icao'].'"><img src="';
			if ($globalIVAO && @getimagesize($globalURL.'/images/airlines/'.$airline_item['airline_icao'].'.gif'))
			{
				print $globalURL.'/images/airlines/'.$airline_item['airline_icao'].'.gif';
			} elseif (@getimagesize($globalURL.'/images/airlines/'.$airline_item['airline_icao'].'.png'))
			{
				print $globalURL.'/images/airlines/'.$airline_item['airline_icao'].'.png';
			} else {
				print $globalURL.'/images/airlines/placeholder.png';
			}
			print '" /></a>';
			print '</td>';
			print '<td>';
			print '<a href="'.$globalURL.'/airline/'.$airline_item['airline_icao'].'">'.$airline_item['airline_name'].' ('.$airline_item['airline_icao'].')</a>';
			print '</td>';
			print '<td>';
			print '<a href="'.$globalURL.'/country/'.strtolower(str_replace(" ", "-", $airline_item['airline_country'])).'">'.$airline_item['airline_country'].'</a>';
			print '</td>';
			print '<td>';
			print $airline_item['airline_count'];
			print '</td>';
			print '<td><a href="'.$globalURL.'/search?airline='.$airline_item['airline_icao'].'&pilot_name='.$spotter_array[0]['pilot_name'].'">'._("Search flights").'</a></td>';
			print '</tr>';
			$i++;
		}
		print '<tbody>';
		print '</table>';
		print '</div>';
	}
	print '</div>';
} else {
	$title = _("Pilot");
	require_once('header.php');
	print '<h1>'._("Error").'</h1>';
	print '<p>'._("Sorry, this pilot is not in the database. :(").'</p>'; 
}

require_once('footer.php');
?>