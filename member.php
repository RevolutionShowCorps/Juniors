<?php
require_once("secure.php");

require_once("DB.php");

$validationErrors = array();

function get_guid() {
    $data = PHP_MAJOR_VERSION < 7 ? openssl_random_pseudo_bytes(16) : random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // Set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // Set bits 6-7 to 10
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function checkRequiredFields($fields, $array){
	$validationErrors = array();

	foreach($fields as $field){
		if(!isset($array[$field]) || trim($array[$field]) == ""){
			$validationErrors[$field] = "This field is required";
		}
	}

	return $validationErrors;
}

function validateData(){
	global $validationErrors;
	$dates = array("dob", "tetanus");

	$validationErrors = checkRequiredFields(array("fname", "lname", "gender", "dob"), $_POST);

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

function createContact($con){
	if(!empty(checkRequiredFields(array("fname", "lname", "relationship"), $_POST))){
		return false;
	}
	
	$contactID = get_guid();
	DB::executeQuery("INSERT INTO Contacts (ID, FirstName, LastName, Mobile, Landline, Email) VALUES (?, ?, ?, ?, ?, ?)", $con, "ssssss", $contactID, $_POST['fname'], $_POST['lname'], $_POST['mobile'], $_POST['landline'], $_POST['email']);

	DB::executeQuery("INSERT INTO MemberContacts (MemberID, ContactID, RelationshipTypeID) VALUES (?, ?, ?)", $con, "ssi", $_GET['id'], $contactID, $_POST['relationship']);

	return true;
}

function updateMember($con){
	if(!validateData()){
		return false;
	}

	if($_POST['tetanus'] == ""){
		$_POST['tetanus'] = null;
	}

	DB::executeQuery("UPDATE Members SET FirstName = ?, LastName = ?, GenderID = ?, DateOfBirth = ?, MedicalDetails = ?, Allergies = ?, LastTetanus = ?, CanDressWounds = ?, CanAdministerMedication = ? WHERE ID = ?", $con, "ssissssiis", $_POST['fname'], $_POST['lname'], $_POST['gender'], $_POST['dob'], $_POST['medical'], $_POST['allergies'], $_POST['tetanus'], isset($_POST['wounds']), isset($_POST['medication']), $_GET['id']);
	
	return true;
}

if(!isset($_GET['id'])){
	header("Location: member.php?id=cbe5eee0-e68e-11ed-b2ea-04bf1b5a7502");
	die();
} 
$con = DB::connect();

$member = DB::executeQueryForSingle("SELECT * FROM Members WHERE ID = ?", $con, "s", $_GET['id']);

if($member == NULL){
	header("Location: member.php");
	die();
}

if(!empty($_POST)){
	if(isset($_POST['update'])){
		if(updateMember($con)){
			header("Location: member.php?saved=1&id=" . $_GET['id']);
			die();
		}
	} else if(isset($_POST['contact'])){
		if($_POST['contactID'] == -1){
			$result = createContact($con);
		} else {
			$result = updateContact($con);
		}

		if($result){
			header("Location: member.php?saved=1&id=" . $_GET['id']);
			die();
		}
	}
}

$genders = DB::executeQuery("SELECT * FROM Genders ORDER BY Name", $con);
$relationships = DB::executeQuery("SELECT * FROM RelationshipTypes ORDER BY SortOrder, Name", $con);

$member['contacts'] = DB::executeQuery("SELECT c.*, r.Name AS Relationship FROM MemberContacts mc INNER JOIN Contacts c ON c.ID = mc.ContactID INNER JOIN RelationshipTypes r ON r.ID = mc.RelationshipTypeID WHERE mc.MemberID = ? ORDER BY r.SortOrder, c.LastName, c.FirstName", $con, "s", $_GET['id']);
$member['doctor'] = null;

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

		<?php if(count($member['contacts']) == 0){ ?>
		<div class="alert alert-danger">No contacts registered! <button class="btn btn-primary add-contact">Add One Now</button></div>
		<?php 
		}

		if($member['doctor'] == null){
			?>
		<div class="alert alert-danger">No doctor's surgery on record! <button class="btn btn-primary">Add One Now</button></div>
		<?php } ?>


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
									<?php echo $contact['FirstName'] . " " . $contact['LastName'] ?>
								</td>
							</tr>
							<tr>
								<td>Relationship</td>
								<td>
									<?php echo $contact['Relationship'] ?>
								</td>
							</tr>
							<tr>
								<td>Mobile</td>
								<td>
									<?php echo $contact['Mobile'] ?>
								</td>
							</tr>
							<tr>
								<td>Landline</td>
								<td>
									<?php echo $contact['Landline'] ?>
								</td>
							</tr>
							<tr>
								<td>Email</td>
								<td><a href="mailto:<?php echo $contact['Email'] ?>">
										<?php echo $contact['Email'] ?>
									</a></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<?php 
			}
			
			if($member['doctor'] != null){
			?>

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
			<?php } ?>
		</div>
	</div>

	<form method="post">
		<div class="modal fade" id="contact" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h2 class="modal-title fs-5" id="contactTitle">Add A Contact</h1>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<div class="row row-cols-1 row-cols-lg-2">
							<div class="col">
								<div class="form-floating mb-3">
									<input type="text" class="form-control" id="cfname" name="fname" placeholder="First Name" required>
									<label for="fname">First Name</label>
								</div>
							</div>

							<div class="col">
								<div class="form-floating mb-3">
									<input type="text" class="form-control" id="clname" name="lname" placeholder="Last Name" required>
									<label for="lname">Last Name</label>
								</div>
							</div>

							<div class="col">
								<div class="form-floating mb-3">
									<input type="text" class="form-control" id="cmobile" name="mobile" placeholder="Mobile Phone">
									<label for="mobile">Mobile Phone</label>
								</div>
							</div>

							<div class="col">
								<div class="form-floating mb-3">
									<input type="text" class="form-control" id="clandline" name="landline" placeholder="Landline">
									<label for="landline">Landline</label>
								</div>
							</div>
						</div>

						<div class="form-floating mb-3">
							<input type="text" class="form-control" id="cemail" name="email" placeholder="Email Address">
							<label for="email">Email Address</label>
						</div>

						<hr />

						<div class="form-floating">
							<select id="relationship" name="relationship" class="form-control" required>
								<option value="">-- Please Select --</option>
								<?php foreach($relationships as $rel){ ?>
								<option value="<?php echo $rel['ID'] ?>">
									<?php echo $rel['Name'] ?>
								</option>
								<?php } ?>
							</select>
							<label for="relationship">Relationship to Member</label>
						</div>

						<input type="hidden" name="contactID" id="contactID" />
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
						<button name="contact" class="btn btn-success">Save</button>
					</div>
				</div>
			</div>
		</div>
	</form>

	<script src="js/bootstrap.min.js"></script>
	<script>
		function addContact() {
			showContactModal(-1, "Add A Contact", "", "", "", "", "", "");
		}

		function showContactModal(id, title, firstName, lastName, mobile, landline, email, relationship) {
			document.getElementById("contactID").value = id;
			document.getElementById("contactTitle").innerHTML = title;
			document.getElementById("cfname").value = firstName;
			document.getElementById("clname").value = lastName;
			document.getElementById("cmobile").value = mobile;
			document.getElementById("clandline").value = landline;
			document.getElementById("cemail").value = email;
			document.getElementById("relationship").value = relationship;

			contactModal.show();
		}

		const contactModal = new bootstrap.Modal(document.getElementById("contact"), {
			backdrop: 'static'
		});

		Array.from(document.getElementsByClassName("add-contact")).forEach(el => el.addEventListener("click", addContact));
	</script>
</body>

</html>