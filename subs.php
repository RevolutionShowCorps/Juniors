<?php
$name = "Luke Taylor";
$title = "Sub History for " . $name;
require_once('head.php');
?>

<body>
	<div class="container">
		<h1>Sub History</h1>
		<h2 class="h4 fst-italic">
			<a href="member.php">&lt;
				<?php echo $name ?>
			</a>
		</h2>

		<div class="row row-cols-1 row-cols-md-3">
			<div class="col mb-3">
				<div class="card h-100 card-status text-bg-danger">
					<div class="card-header">Current Balance</div>
					<div class="card-body">
						£0
					</div>
					<div class="card-footer">
						<button class="btn btn-light">Top Up</button>
					</div>
				</div>
			</div>

			<div class="col mb-3">
				<div class="card h-100 card-status text-bg-primary">
					<div class="card-header">Subs Type</div>
					<div class="card-body">
						Weekly
					</div>
					<div class="card-footer">
						<button class="btn btn-light">Change</button>
					</div>
				</div>
			</div>

			<div class="col mb-3">
				<div class="card h-100 card-status text-bg-success">
					<div class="card-header">Total Paid</div>
					<div class="card-body">
						£500
					</div>
					<div class="card-footer">
						Since 24/06/2021
					</div>
				</div>
			</div>
		</div>

		<hr />

		<h3>Transactions</h3>

		<div class="row row-cols-1 row-cols-md-2 row-cols-lg-4">
			<?php for($i = 0; $i < 6; $i++){ ?>
			<div class="col">
				<div class="card card-body text-center mb-3">
					<h4>20/04/2023</h4>
					<table class="table details">
						<tbody>
							<tr>
								<td>Opening Balance</td>
								<td>£0.00</td>
							</tr>
							<tr>
								<td>Top Up (cash)</td>
								<td class="text-success">+£3.00</td>
							</tr>
							<tr>
								<td>Subs (weekly)</td>
								<td class="text-danger">-£3.00</td>
							</tr>
							<tr>
								<td>Closing Balance</td>
								<td>£0.00</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</body>

</html>