<?php
$contacts = array(
	array(
		"name"=>"Ruth Taylor",
		"relationship"=>"Mum",
		"mobile"=>"07234567890",
		"landline"=>"01234567890",
		"email"=>"test@mail.com",
		"address"=>array(
			"line1"=>"A House",
			"line2"=>"A Place",
			"city"=>"A City",
			"postcode"=>"POST CODE"
		)
	)
);
?>
<!doctype html>
<html lang="en">

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Luke Taylor | Revolution Juniors</title>
		<link href="css/bootstrap.min.css" rel="stylesheet" />

		<style>
			table.details td:nth-child(1) {
				text-align: right;
				font-weight: bold;
			}

			.card .table{
				margin-bottom: 0;
			}

			.card .table tr:last-child{
				border-bottom-color: transparent;
			}
		</style>
	</head>

	<body>
		<div class="container">
			<h1>Luke Taylor</h1>
			<div class="card mb-3">
				<div class="card-header">Personal Details</div>
				<div class="card-body">
					<div class="row row-cols-1 row-cols-md-2">
						<div class="col">
							<div class="form-floating mb-3">
								<input type="text" class="form-control" id="fname" placeholder="First Name" value="Luke" required>
								<label for="fname">First Name</label>
							</div>
						</div>

						<div class="col">
							<div class="form-floating mb-3">
								<input type="text" class="form-control" id="lname" placeholder="Last Name" value="Taylor" required>
								<label for="lname">Last Name</label>
							</div>
						</div>

						<div class="col">
							<div class="form-floating mb-3">
								<select class="form-select" id="gender" required>
									<option value="">-- Please Select --</option>
									<option value="1" selected>Male</option>
									<option value="2">Female</option>
								</select>
								<label for="gender">Gender</label>
							</div>
						</div>

						<div class="col">
							<div class="form-floating mb-3">
								<input type="date" class="form-control" id="dob" placeholder="Date of Birth" value="1999-04-09" required>
								<label for="dob">Date of Birth</label>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row row-cols-1 row-cols-lg-2">
				<?php 
					for($i = 0; $i < count($contacts); $i++){ 
						$contact = $contacts[$i]; 
				?>
				<div class="col">
					<div class="card mb-3">
						<div class="card-header">Contact <?php echo $i + 1 ?> </div>
						<!-- <div class="card-body"> -->
						<table class="table table-hover details">
							<tbody>
								<tr>
									<td>Name</td>
									<td><?php echo $contact['name'] ?></td>
								</tr>
								<tr>
									<td>Relationship</td>
									<td><?php echo $contact['relationship'] ?></td>
								</tr>
								<tr>
									<td>Mobile</td>
									<td><?php echo $contact['mobile'] ?></td>
								</tr>
								<tr>
									<td>Landline</td>
									<td><?php echo $contact['landline'] ?></td>
								</tr>
								<tr>
									<td>Email</td>
									<td><a href="mailto:<?php echo $contact['email'] ?>"><?php echo $contact['email'] ?></a></td>
								</tr>
								<tr>
									<td>Address</td>
									<td><?php echo $contact['address']['line1'] ?><br />
										<?php echo $contact['address']['line2'] ?><br />
										<?php echo $contact['address']['city'] ?><br />
										<?php echo $contact['address']['postcode'] ?>
									</td>
								</tr>
							</tbody>
						</table>
						<!-- </div> -->
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</body>

</html>