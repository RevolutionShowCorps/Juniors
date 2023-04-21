<?php

$genders = array(
	array(
		"id"=>1,
		"name"=>"Male"
	),
	array(
		"id"=>2,
		"name"=>"Female"
	)
);

$member = array(
	"firstName"=>"Luke",
	"lastName"=>"Taylor",
	"gender"=>1,
	"dateOfBirth"=>new DateTime("1999-04-09"),
	"medical"=>"N/A",
	"allergies"=>"N/A",
	"lastTetanus"=>new DateTime("2012-01-01"),
	"canDressWounds"=>true,
	"canAdministerMedication"=>true,
	"contacts"=> array(
		array(
			"firstName"=>"Ruth",
			"lastName"=>"Taylor",
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
		),
		array(
			"firstName"=>"Steve",
			"lastName"=>"Taylor",
			"relationship"=>"Dad",
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
	),
	"doctor"=> array(
		"name"=>"Doctor Who?",
		"surgery"=>"A Surgery",
		"phone"=>"01234567890"
	)
);

$name = $member['firstName'] . " " . $member['lastName'];
?>
<!doctype html>
<html lang="en">

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>
			<?php echo $name ?> | Revolution Juniors
		</title>
		<link href="css/bootstrap.min.css" rel="stylesheet" />
		<link href="css/main.css" rel="stylesheet" />
	</head>

	<body>
		<div class="container">
			<h1>
				<?php echo $name ?>
			</h1>
			<p><a href="subs.php">Subs balance: £0 &gt;</a></p>
			<div class="card mb-3">
				<div class="card-header">Personal Details</div>
				<div class="card-body">
					<div class="row row-cols-1 row-cols-md-2">
						<div class="col">
							<div class="form-floating mb-3">
								<input type="text" class="form-control" id="fname" placeholder="First Name" value="<?php echo $member['firstName'] ?>" required>
								<label for="fname">First Name</label>
							</div>
						</div>

						<div class="col">
							<div class="form-floating mb-3">
								<input type="text" class="form-control" id="lname" placeholder="Last Name" value="<?php echo $member['lastName'] ?>" required>
								<label for="lname">Last Name</label>
							</div>
						</div>

						<div class="col">
							<div class="form-floating mb-3">
								<select class="form-select" id="gender" required>
									<option value="">-- Please Select --</option>
									<?php foreach($genders as $gender){ ?>
									<option value="<?php echo $gender['id'] ?>" <?php if($gender['id']==$member['gender']){?>selected<?php } ?>>
										<?php echo $gender['name'] ?>
									</option>
									<?php } ?>
								</select>
								<label for="gender">Gender</label>
							</div>
						</div>

						<div class="col">
							<div class="form-floating mb-3">
								<input type="date" class="form-control" id="dob" placeholder="Date of Birth" value="<?php echo $member['dateOfBirth']->format('Y-m-d') ?>" required>
								<label for="dob">Date of Birth</label>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="card mb-3">
				<div class="card-header">Medical Details</div>
				<div class="card-body">
					<div class="form-floating mb-3">
						<textarea class="form-control" placeholder="E.g. Asthma" id="medical"><?php echo $member['medical'] ?></textarea>
						<label for="medical">Medical Conditions</label>
					</div>

					<div class="form-floating mb-3">
						<textarea class="form-control" placeholder="E.g.Nuts" id="allergies"><?php echo $member['allergies'] ?></textarea>
						<label for="allergies">Allergies</label>
					</div>

					<div class="row row-cols-1 row-cols-md-2">
						<div class="col">
							<div class="form-floating mb-3">
								<input type="date" class="form-control" id="tetanus" placeholder="Last Tetanus Jab" value="<?php echo $member['lastTetanus']->format('Y-m-d') ?>" required>
								<label for="tetanus">Last Tetanus Jab</label>
							</div>
						</div>

						<div class="col">
							<div class="form-check form-switch">
								<input class="form-check-input" type="checkbox" role="switch" id="wounds" <?php if($member['canDressWounds']){?>checked<?php } ?>>
								<label class="form-check-label" for="wounds">Consent to clean/dress wounds?</label>
							</div>
							<div class="form-check form-switch">
								<input class="form-check-input" type="checkbox" role="switch" id="medication" <?php if($member['canAdministerMedication']){?>checked<?php } ?>>
								<label class="form-check-label" for="medication">Consent to administer paracetamol/ibuprofen?</label>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="text-center my-3">
				<button class="btn btn-lg btn-success">Save</button>
			</div>

			<div class="row row-cols-1 row-cols-md-2">
				<?php 
					for($i = 0; $i < count($member['contacts']); $i++){ 
						$contact = $member['contacts'][$i]; 
				?>
				<div class="col">
					<div class="card mb-3">
						<div class="card-header d-flex align-items-center">Contact
							<?php echo $i + 1 ?> <button class="btn btn-primary ms-auto">Edit</button>
						</div>
						<table class="table table-hover details">
							<tbody>
								<tr>
									<td>Name</td>
									<td>
										<?php echo $contact['firstName'] . " " . $contact['lastName'] ?>
									</td>
								</tr>
								<tr>
									<td>Relationship</td>
									<td>
										<?php echo $contact['relationship'] ?>
									</td>
								</tr>
								<tr>
									<td>Mobile</td>
									<td>
										<?php echo $contact['mobile'] ?>
									</td>
								</tr>
								<tr>
									<td>Landline</td>
									<td>
										<?php echo $contact['landline'] ?>
									</td>
								</tr>
								<tr>
									<td>Email</td>
									<td><a href="mailto:<?php echo $contact['email'] ?>">
											<?php echo $contact['email'] ?>
										</a></td>
								</tr>
								<tr>
									<td>Address</td>
									<td>
										<?php echo $contact['address']['line1'] ?><br />
										<?php echo $contact['address']['line2'] ?><br />
										<?php echo $contact['address']['city'] ?><br />
										<?php echo $contact['address']['postcode'] ?>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<?php } ?>

				<div class="col">
					<div class="card">
						<div class="card-header d-flex align-items-center">Doctor <button class="btn btn-primary ms-auto">Edit</button></div>
						<table class="table table-hover details">
							<tbody>
								<tr>
									<td>Name</td>
									<td>
										<?php echo $member['doctor']['name'] ?>
									</td>
								</tr>
								<tr>
									<td>Surgery</td>
									<td>
										<?php echo $member['doctor']['surgery'] ?>
									</td>
								</tr>
								<tr>
									<td>Phone</td>
									<td>
										<?php echo $member['doctor']['phone'] ?>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</body>

</html>