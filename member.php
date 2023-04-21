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
				<?php for($i = 1; $i <= 2; $i++){ ?>
				<div class="col">
					<div class="card mb-3">
						<div class="card-header">Contact
							<?php echo $i ?>
						</div>
						<!-- <div class="card-body"> -->
						<table class="table details">
							<tbody>
								<tr>
									<td>Name</td>
									<td>Ruth Taylor</td>
								</tr>
								<tr>
									<td>Address</td>
									<td>A House<br />
										A Place<br />
										A City<br />
										POST CODE
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