<?php
require_once("../secure.php");

require_once(__DIR__ . "/../lib/DB.php");
require_once(__DIR__ . "/../lib/Member.php");
require_once(__DIR__ . "/../lib/Contact.php");
require_once(__DIR__ . "/../lib/Section.php");

require_once(__DIR__ . "/../lib/Validation.php");

$validationErrors = array();

function validateData(){
	global $validationErrors;
	$validationErrors = array_merge(Validation::checkRequiredFields(array("fname", "lname", "gender", "dob"), $_POST), Validation::checkDateFields(array("dob", "tetanus"), $_POST));
	return empty($validationErrors);
}

function updateContact($con){
	$contact = Contact::getById($_POST['contactID']);
	if($contact == null){
		return false;
	}

	$contact->firstName = $_POST['fname'];
	$contact->lastName = $_POST['lname'];
	$contact->mobile = $_POST['mobile'];
	$contact->landline = $_POST['landline'];
	$contact->email = $_POST['email'];

	Contact::update($contact, $_POST['relationship'], $_GET['id'], $con);

	return true;
}

function updateMember($member, $con){
	$member->firstName = $_POST['fname'];
	$member->lastName = $_POST['lname'];
	$member->genderID = $_POST['gender'];
	$member->DOB = Utils::toDateTime($_POST['dob']);
	$member->medicalConditions = $_POST['medical'];
	$member->allergies = $_POST['allergies'];
	$member->lastTetanus = Utils::toDateTime($_POST['tetanus']);
	$member->canDressWounds = isset($_POST['wounds']);
	$member->canAdministerMedication = isset($_POST['medication']);

	Member::update($member, $con);
}

$con = DB::connect();
$creating = false;
$id = isset($_GET['id']) ? $_GET['id'] : null;
$member = Member::getByID($id, $con);

if($member == NULL){
	$creating = true;
	$member = MemberDto::getForCreation();
}

if(!empty($_POST)){
	$result = false;

	if(isset($_POST['update'])){
		if(validateData()){
			if($creating){
				$member = Member::create($_POST['fname'], $_POST['lname'], $_POST['gender'], Utils::toDateTime($_POST['dob']), $_POST['medical'], $_POST['allergies'], Utils::toDateTime($_POST['tetanus']), isset($_POST['wounds']), isset($_POST['medication']), $con);
				$id = $member->ID;
			} else {
				updateMember($member, $con);
			}

			Section::addMember($_POST['section'], $_POST['role'], $member->ID, $con);

			$result = true;
		}
	} else if(isset($_POST['contact'])){
		if(empty(Validation::checkRequiredFields(array("fname", "lname", "relationship"), $_POST))){
			if($_POST['contactID'] == -1){
				Contact::createForMember($_GET['id'], $_POST['fname'], $_POST['lname'], $_POST['mobile'], $_POST['landline'], $_POST['email'], $_POST['relationship'], $con);
				$result = true;
			} else {
				$result = updateContact($con);
			}
		}

	} else if(isset($_POST['doctor'])){
		if(empty(Validation::checkRequiredFields(array("fname", "lname", "surgery"), $_POST))){
			if($_POST['doctorID'] == -1){
				DB::executeQuery("INSERT INTO Doctors (FirstName, LastName, PhoneNumber, SurgeryName) VALUES (?, ?, ?, ?)", $con, "ssss", $_POST['fname'], $_POST['lname'], $_POST['phone'], $_POST['surgery']);

				DB::executeQuery("UPDATE Members SET DoctorID = ? WHERE ID = ?", $con, "is", $con->insert_id, $_GET['id']);
			} else {
				DB::executeQuery("UPDATE Doctors SET FirstName = ?, LastName = ?, PhoneNumber = ?, SurgeryName = ? WHERE ID = ?", $con, "ssssi", $_POST['fname'], $_POST['lname'], $_POST['phone'], $_POST['surgery'], $_POST['doctorID']);
			}

			$result = true;
		}

	}

	if($result){
		header("Location: member.php?saved=1&id=" . $id);
		die();
	}
}

$genders = DB::executeQuery("SELECT * FROM Genders ORDER BY Name", $con);
$sections = Section::getAll(false, $con);
$relationships = DB::executeQuery("SELECT * FROM RelationshipTypes ORDER BY SortOrder, Name", $con);
$roles = DB::executeQuery("SELECT * FROM Roles ORDER BY Name", $con);

DB::close($con);

$title = $member->ID == -1 ? "Create Member" : $member->fullName();
require_once('../head.php');
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
			<?php } else if(isset($_GET['saved'])){ ?>
			<div class="alert alert-success">Member saved!</div>
			<?php } ?>

			<div class="card mb-3">
				<div class="card-header">Personal Details</div>
				<div class="card-body">
					<div class="row row-cols-1 row-cols-md-2">
						<div class="col">
							<div class="form-floating mb-3">
								<input type="text" class="form-control" id="fname" name="fname" placeholder="First Name" value="<?php echo $member->firstName ?>" required>
								<label for="fname">First Name</label>
							</div>
						</div>

						<div class="col">
							<div class="form-floating mb-3">
								<input type="text" class="form-control" id="lname" name="lname" placeholder="Last Name" value="<?php echo $member->lastName ?>" required>
								<label for="lname">Last Name</label>
							</div>
						</div>

						<div class="col">
							<div class="form-floating mb-3">
								<select class="form-select" id="gender" name="gender" required>
									<option value="">-- Please Select --</option>
									<?php foreach($genders as $gender){ ?>
									<option value="<?php echo $gender['ID'] ?>" <?php if($gender['ID']==$member->genderID){?>selected
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
								<input type="date" class="form-control" id="dob" name="dob" placeholder="Date of Birth" <?php if($member->DOB != null){ ?>value="<?php echo $member->DOB->format('Y-m-d') ?>"<?php } ?>required>
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
						<textarea class="form-control" placeholder="E.g. Asthma" id="medical" name="medical"><?php echo $member->medicalConditions ?></textarea>
						<label for="medical">Medical Conditions</label>
					</div>

					<div class="form-floating mb-3">
						<textarea class="form-control" placeholder="E.g.Nuts" id="allergies" name="allergies"><?php echo $member->allergies ?></textarea>
						<label for="allergies">Allergies</label>
					</div>

					<div class="row row-cols-1 row-cols-md-2">
						<div class="col">
							<div class="form-floating mb-3">
								<input type="date" class="form-control" id="tetanus" name="tetanus" placeholder="Last Tetanus Jab" <?php if($member->lastTetanus != null){ ?>value="<?php echo $member->lastTetanus->format('Y-m-d') ?>"<?php } ?>>
								<label for="tetanus">Last Tetanus Jab</label>
							</div>
						</div>

						<div class="col">
							<div class="form-check form-switch">
								<input class="form-check-input" type="checkbox" role="switch" id="wounds" name="wounds" <?php if($member->canDressWounds){?>checked
								<?php } ?>>
								<label class="form-check-label" for="wounds">Consent to clean/dress wounds?</label>
							</div>
							<div class="form-check form-switch">
								<input class="form-check-input" type="checkbox" role="switch" id="medication" name="medication" <?php if($member->canAdministerMedication){?>checked
								<?php } ?>>
								<label class="form-check-label" for="medication">Consent to administer paracetamol/ibuprofen?</label>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="card">
				<div class="card-header">Section</div>
				<div class="card-body">
					<div class="form-floating mb-3">
						<select name="section" id="section" class="form-control" required>
							<option value="">-- Please Select --</option>
							<?php foreach($sections as $section){ ?>
							<option value="<?php echo $section->ID ?>"<?php if((isset($_GET['section']) && $_GET['section'] == $section->ID) || (!isset($_GET['section']) && isset($member->section) && $member->section->ID == $section->ID)){ ?> selected<?php } ?>><?php echo $section->name ?></option>
							<?php } ?>
						</select>
						<label for="section">Section</label>
					</div>

					<div class="form-floating mb-3">
						<select name="role" id="role" class="form-control" required>
							<option value="">-- Please Select --</option>
							<?php foreach($roles as $role){ ?>
							<option value="<?php echo $role['ID'] ?>"<?php if(isset($member->section) && $role['ID'] == $member->section->role->ID){ ?> selected<?php } ?>><?php echo $role['Name'] ?></option>
							<?php } ?>
						</select>
						<label for="role">Role</label>
					</div>
				</div>
			</div>

			<div class="text-center my-3">
				<button name="update" class="btn btn-lg btn-success">Save</button>
			</div>
		</form>

		<?php 
		if($member->ID != -1){
			if(count($member->contacts) == 0){ 
		?>
		<div class="alert alert-danger">No contacts registered! <button class="btn btn-primary add-contact">Add One Now</button></div>
		<?php 
			}

			if($member->doctor == null){
				?>
		<div class="alert alert-danger">No doctor's surgery on record! <button class="btn btn-primary add-doctor">Add One Now</button></div>
		<?php } ?>


		<div class="row row-cols-1 row-cols-md-2">
			<?php 
						for($i = 0; $i < count($member->contacts); $i++){ 
							$contact = $member->contacts[$i]; 
					?>
			<div class="col mb-3">
				<div class="card h-100">
					<div class="card-header d-flex align-items-center">Contact
						<?php echo $i + 1 ?> <button data-contact='<?php echo json_encode($contact) ?>' class="btn btn-primary edit-contact ms-auto">Edit</button>
					</div>
					<table class="table table-hover details">
						<tbody>
							<tr>
								<td>Name</td>
								<td>
									<?php echo $contact->fullName() ?>
								</td>
							</tr>
							<tr>
								<td>Relationship</td>
								<td>
									<?php echo $contact->relationship->name ?>
								</td>
							</tr>
							<tr>
								<td>Mobile</td>
								<td>
									<?php echo $contact->mobile ?>
								</td>
							</tr>
							<tr>
								<td>Landline</td>
								<td>
									<?php echo $contact->landline ?>
								</td>
							</tr>
							<tr>
								<td>Email</td>
								<td><a href="mailto:<?php echo $contact->email ?>">
										<?php echo $contact->email ?>
									</a></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<?php 
				}
	
				if(count($member->contacts) == 1){
					?>
			<div class="col mb-3">
				<div class="card card-body h-100 d-flex align-items-center justify-content-center">
					<button class="btn btn-primary add-contact stretched-link">Add a Second Contact</button>
				</div>
			</div>
			<?php
				}
			
				if($member->doctor != null){
				?>

			<div class="col mb-3">
				<div class="card">
					<div class="card-header button-header">Doctor <button data-doctor='<?php echo json_encode($member->doctor) ?>' class="btn btn-primary edit-doctor">Edit</button></div>
					<table class="table table-hover details">
						<tbody>
							<tr>
								<td>Name</td>
								<td>
									<?php echo $member->doctor['FirstName'] . " " . $member->doctor['LastName'] ?>
								</td>
							</tr>
							<tr>
								<td>Surgery</td>
								<td>
									<?php echo $member->doctor['SurgeryName'] ?>
								</td>
							</tr>
							<tr>
								<td>Phone</td>
								<td>
									<?php echo $member->doctor['PhoneNumber'] ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<?php } ?>
		</div>
		<?php } ?>
	</div>

	<form method="post">
		<div class="modal fade" id="contact" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h2 class="modal-title fs-5" id="contactTitle">Add A Contact</h2>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<div class="row row-cols-1 row-cols-lg-2">
							<div class="col">
								<div class="form-floating mb-3">
									<input type="text" class="form-control" id="cfname" name="fname" placeholder="First Name" required>
									<label for="cfname">First Name</label>
								</div>
							</div>

							<div class="col">
								<div class="form-floating mb-3">
									<input type="text" class="form-control" id="clname" name="lname" placeholder="Last Name" required>
									<label for="clname">Last Name</label>
								</div>
							</div>

							<div class="col">
								<div class="form-floating mb-3">
									<input type="text" class="form-control" id="cmobile" name="mobile" placeholder="Mobile Phone">
									<label for="cmobile">Mobile Phone</label>
								</div>
							</div>

							<div class="col">
								<div class="form-floating mb-3">
									<input type="text" class="form-control" id="clandline" name="landline" placeholder="Landline">
									<label for="clandline">Landline</label>
								</div>
							</div>
						</div>

						<div class="form-floating mb-3">
							<input type="text" class="form-control" id="cemail" name="email" placeholder="Email Address">
							<label for="cemail">Email Address</label>
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

	<form method="post">
		<div class="modal fade" id="doctor" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h2 class="modal-title fs-5" id="doctorTitle">Add A Doctor's Surgery</h2>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<div class="row row-cols-1 row-cols-lg-2">
							<div class="col">
								<div class="form-floating mb-3">
									<input type="text" class="form-control" id="dfname" name="fname" placeholder="First Name" required>
									<label for="dfname">First Name</label>
								</div>
							</div>

							<div class="col">
								<div class="form-floating mb-3">
									<input type="text" class="form-control" id="dlname" name="lname" placeholder="Last Name" required>
									<label for="dlname">Last Name</label>
								</div>
							</div>

							<div class="col">
								<div class="form-floating mb-3">
									<input type="text" class="form-control" id="dphone" name="phone" placeholder="Phone Number">
									<label for="dphone">Phone Number</label>
								</div>
							</div>
						</div>

						<div class="form-floating mb-3">
							<input type="text" class="form-control" id="surgery" name="surgery" placeholder="Surgery" required>
							<label for="surgery">Surgery</label>
						</div>

						<input type="hidden" name="doctorID" id="doctorID" />
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
						<button name="doctor" class="btn btn-success">Save</button>
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

		function editContact(el) {
			const data = JSON.parse(el.dataset.contact);
			showContactModal(data.ID, `Edit Contact ${data.firstName} ${data.lastName}`, data.firstName, data.lastName, data.mobile, data.landline, data.email, data.relationship.ID);
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

		function editDoctor(el) {
			const data = JSON.parse(el.dataset.doctor);
			showDoctorModal(data.ID, "Edit Doctor", data.FirstName, data.LastName, data.PhoneNumber, data.SurgeryName);
		}

		function addDoctor() {
			showDoctorModal(-1, "Add a Doctor's Surgery", "", "", "", "");
		}

		function showDoctorModal(id, title, firstName, lastName, phone, surgery) {
			document.getElementById("doctorID").value = id;
			document.getElementById("doctorTitle").innerHTML = title;
			document.getElementById("dfname").value = firstName;
			document.getElementById("dlname").value = lastName;
			document.getElementById("dphone").value = phone;
			document.getElementById("surgery").value = surgery;

			doctorModal.show();
		}

		const contactModal = new bootstrap.Modal(document.getElementById("contact"), {
			backdrop: 'static'
		});

		const doctorModal = new bootstrap.Modal(document.getElementById("doctor"), {
			backdrop: 'static'
		});

		Array.from(document.getElementsByClassName("add-contact")).forEach(el => el.addEventListener("click", addContact));
		Array.from(document.getElementsByClassName("edit-contact")).forEach(el => el.addEventListener("click", () => editContact(el)));

		Array.from(document.getElementsByClassName("add-doctor")).forEach(el => el.addEventListener("click", addDoctor));
		Array.from(document.getElementsByClassName("edit-doctor")).forEach(el => el.addEventListener("click", () => editDoctor(el)));
	</script>
</body>

</html>