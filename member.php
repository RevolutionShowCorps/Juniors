<?php
require_once("secure.php");

require_once("DB.php");

$validationErrors = array();

function validateData(){
	global $validationErrors;

	$requiredFields = array("fname", "lname", "gender", "dob");
	$dates = array("dob", "tetanus");

	foreach($requiredFields as $field){
		if(!isset($_POST[$field]) || trim($_POST[$field]) == ""){
			$validationErrors[$field] = "This field is required";
		}
	}

	foreach($dates as $dateField){
		if(isset($_POST[$dateField])){
			$test = new DateTime($_POST[$dateField]);
			if($test == null){
				$validationErrors[$dateField] = "Invalid date";
			}
		}
	}

	return empty($validationErrors);
}

function updateMember(){
	if(!validateData()){
		return false;
	}

	if($_POST['tetanus'] == ""){
		$_POST['tetanus'] = null;
	}

	DB::executeQuery("UPDATE Members SET FirstName = ?, LastName = ?, GenderID = ?, DateOfBirth = ?, MedicalDetails = ?, Allergies = ?, LastTetanus = ?, CanDressWounds = ?, CanAdministerMedication = ? WHERE ID = ?", null, "ssissssiis", $_POST['fname'], $_POST['lname'], $_POST['gender'], $_POST['dob'], $_POST['medical'], $_POST['allergies'], $_POST['tetanus'], isset($_POST['wounds']), isset($_POST['medication']), $_GET['id']);
	
	return true;
}

if(!isset($_GET['id'])){
	header("Location: member.php?id=cbe5eee0-e68e-11ed-b2ea-04bf1b5a7502");
	die();
} 

$id = $_GET['id'];

$con = DB::connect();

$member = DB::executeQueryForSingle("SELECT * FROM Members WHERE ID = ?", $con, "s", $id);

if($member == NULL){
	header("Location: member.php");
	die();
}

if(!empty($_POST) && isset($_POST['update'])){
	if(updateMember()){
		header("Location: member.php?id=" . $id);
		die();
	}
}

$genders = DB::executeQuery("SELECT * FROM Genders ORDER BY Name", $con);
$member['contacts'] = array();
$member['doctor'] = array();

DB::close($con);

$member['DateOfBirth'] = new DateTime($member['DateOfBirth']);

if($member['LastTetanus'] != null){
	$member['LastTetanus'] = new DateTime($member['LastTetanus']);
}

$title = $member['FirstName'] . " " . $member['LastName'];
require_once('head.php');
?>

<body>
	<div class="container">
		<h1>
			<?php echo $title ?>
		</h1>
		<p><a href="subs.php">Subs balance: £0 &gt;</a></p>

		<form method="post">
			<?php if(!empty($validationErrors)){ ?>
				<div class="alert alert-danger">Invalid details. Please try again</div>
			<?php } ?>

			<div class="card mb-3">
				<div class="card-header">Personal Details</div>
				<div class="card-body">
					<div class="row row-cols-1 row-cols-md-2">
						<div class="col">
							<div class="form-floating mb-3">
								<input type="text" class="form-control" id="fname" name="fname" placeholder="First Name" value="<?php echo $member['FirstName'] ?>" required>
								<label for="fname">First Name</label>
							</div>
						</div>

						<div class="col">
							<div class="form-floating mb-3">
								<input type="text" class="form-control" id="lname" name="lname" placeholder="Last Name" value="<?php echo $member['LastName'] ?>" required>
								<label for="lname">Last Name</label>
							</div>
						</div>

						<div class="col">
							<div class="form-floating mb-3">
								<select class="form-select" id="gender" name="gender" required>
									<option value="">-- Please Select --</option>
									<?php foreach($genders as $gender){ ?>
									<option value="<?php echo $gender['ID'] ?>" <?php if($gender['ID']==$member['GenderID']){?>selected
										<?php } ?>>
										<?php echo $gender['Name'] ?>
									</option>
									<?php } ?>
								</select>
								<label for="gender">Gender</label>
							</div>
						</div>

						<div class="col">
							<div class="form-floating mb-3">
								<input type="date" class="form-control" id="dob" name="dob" placeholder="Date of Birth" value="<?php echo $member['DateOfBirth']->format('Y-m-d') ?>" required>
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
						<textarea class="form-control" placeholder="E.g. Asthma" id="medical" name="medical"><?php echo $member['MedicalDetails'] ?></textarea>
						<label for="medical">Medical Conditions</label>
					</div>

					<div class="form-floating mb-3">
						<textarea class="form-control" placeholder="E.g.Nuts" id="allergies" name="allergies"><?php echo $member['Allergies'] ?></textarea>
						<label for="allergies">Allergies</label>
					</div>

					<div class="row row-cols-1 row-cols-md-2">
						<div class="col">
							<div class="form-floating mb-3">
								<input type="date" class="form-control" id="tetanus" name="tetanus" placeholder="Last Tetanus Jab" <?php if($member['LastTetanus'] !=null){ ?>value="
								<?php echo $member['LastTetanus']->format('Y-m-d') ?>"
								<?php } ?>>
								<label for="tetanus">Last Tetanus Jab</label>
							</div>
						</div>

						<div class="col">
							<div class="form-check form-switch">
								<input class="form-check-input" type="checkbox" role="switch" id="wounds" name="wounds" <?php if($member['CanDressWounds']){?>checked
								<?php } ?>>
								<label class="form-check-label" for="wounds">Consent to clean/dress wounds?</label>
							</div>
							<div class="form-check form-switch">
								<input class="form-check-input" type="checkbox" role="switch" id="medication" name="medication" <?php if($member['CanAdministerMedication']){?>checked
								<?php } ?>>
								<label class="form-check-label" for="medication">Consent to administer paracetamol/ibuprofen?</label>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="text-center my-3">
				<button name="update" class="btn btn-lg btn-success">Save</button>
			</div>
		</form>

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
					<div class="card-header button-header">Doctor <button class="btn btn-primary">Edit</button></div>
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