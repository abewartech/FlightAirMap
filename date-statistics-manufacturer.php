<?php
require_once('require/class.Connection.php');
require_once('require/class.Spotter.php');
require_once('require/class.Language.php');
$Spotter = new Spotter();
$sort = filter_input(INPUT_GET,'sort',513);
$date = filter_input(INPUT_GET,'date',513);
$spotter_array = $Spotter->getSpotterDataByDate($date,"0,1", $sort);

if (!empty($spotter_array))
{
	$title = sprintf(_("Most Common Aircraft Manufacturer on %s"),date("l F j, Y", strtotime($spotter_array[0]['date_iso_8601'])));

	require_once('header.php');
	print '<div class="select-item">';
	print '<form action="'.$globalURL.'/date" method="post" class="form-inline">';
	print '<div class="form-group">';
	print '<label for="datepickeri">'._("Select a Date").'</label>';
	print '<div class="input-group date" id="datepicker">';
	print '<input type="text" class="form-control" id="datepickeri" name="date" value="" />';
	print '<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>';
	print '</div>';
	print '<button type="submit"><i class="fa fa-angle-double-right"></i></button>';
	print '</div>';
	print '</form>';
	print '</div>';
	print '<script type="text/javascript">$(function () { $("#datepicker").datetimepicker({ format: "YYYY-MM-DD", defaultDate: "'.$date.'" }); }); </script>';
	print '<br />';
	print '<div class="info column">';
	print '<h1>'.sprintf(_("Flights from %s"),date("l F j, Y", strtotime($spotter_array[0]['date_iso_8601']))).'</h1>';
	print '</div>';

	include('date-sub-menu.php');
	print '<div class="column">';
	print '<h2>'._("Most Common Aircraft Manufacturer").'</h2>';
	print '<p>'.sprintf(_("The statistic below shows the most common Aircraft Manufacturer of flights on <strong>%s</strong>."),date("l F j, Y", strtotime($spotter_array[0]['date_iso_8601']))).'</p>';

	$manufacturers_array = $Spotter->countAllAircraftManufacturerByDate($date);
	if (!empty($manufacturers_array))
	{
		print '<div class="table-responsive">';
		print '<table class="common-manufacturer table-striped">';
		print '<thead>';
		print '<th></th>';
		print '<th>'._("Aircraft Manufacturer").'</th>';
		print '<th>'._("# of times").'</th>';
		print '<th></th>';
		print '</thead>';
		print '<tbody>';
		$i = 1;
		foreach($manufacturers_array as $manufacturer_item)
		{
			print '<tr>';
			print '<td><strong>'.$i.'</strong></td>';
			print '<td>';
			print '<a href="'.$globalURL.'/manufacturer/'.strtolower(str_replace(" ", "-", $manufacturer_item['aircraft_manufacturer'])).'">'.$manufacturer_item['aircraft_manufacturer'].'</a>';
			print '</td>';
			print '<td>';
			print $manufacturer_item['aircraft_manufacturer_count'];
			print '</td>';
			print '<td><a href="'.$globalURL.'/search?manufacturer='.strtolower(str_replace(" ", "-", $manufacturer_item['aircraft_manufacturer'])).'&start_date='.$date.'+00:00&end_date='.$date.'+23:59">'._("Search flights").'</a></td>';
			print '</tr>';
			$i++;
		}
		print '<tbody>';
		print '</table>';
		print '</div>';
	}
	print '</div>';
} else {
	$title = _("Unknown Date");
	require_once('header.php');
	print '<h1>'._("Error").'</h1>';
	print '<p>'._("Sorry, this date does not exist in this database. :(").'</p>'; 
}

require_once('footer.php');
?>