<?php
$title = "Event Administration";
$year = 2023;
$month = 4;
$date = new DateTime($year . "-" . $month . "-01");
require_once('head.php');
?>

<body>
	<div class="container">
		<h1>
			<?php echo $title ?>
		</h1>

		<h2>Today's Events</h2>
		<div class="alert alert-danger">No events found</div>

		<hr />

		<h2>This Week's Events</h2>
		<div class="alert alert-danger">No events founds</div>

		<hr />

		<h2>This Month's Events</h2>
		<div class="alert alert-danger">No events found</div>

		<hr />

		<h2>Calendar</h2>
		<div class="card text-center">
			<div class="card-header fw-bold">
				<a href="?prevMonth">&lt;</a>
				April 2023
				<a href="?nextMonth">&gt;</a>
			</div>
			<table class="table table-bordered table-same-width">
				<thead>
					<tr>
						<th>M<span class="d-none d-md-inline-block">on</span></th>
						<th>T<span class="d-none d-md-inline-block">ue</span></th>
						<th>W<span class="d-none d-md-inline-block">ed</span></th>
						<th>T<span class="d-none d-md-inline-block">hu</span></th>
						<th>F<span class="d-none d-md-inline-block">ri</span></th>
						<th>S<span class="d-none d-md-inline-block">at</span></th>
						<th>S<span class="d-none d-md-inline-block">un</span></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<?php 
					$index = $date->format('N') - 1;
					for($i = 1; $i < $date->format('N'); $i++){ ?>
						<td class="text-muted">-</td>
						<?php 
					} 
					
					while($date->format("m") == $month){
					?>
						<td>
							<?php echo $date->format("d"); ?>
						</td>
						<?php
						$date = $date->modify("+1 day");
						$index++;

						if($index == 7){
							$index = 0;
							?>
					</tr>
					<tr>
						<?php
						}
					}
					?>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</body>

</html>