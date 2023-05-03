<?php
require_once("../secure.php");

require_once("../lib/DB.php");
require_once("../lib/Section.php");

$con = DB::connect();

if(isset($_POST['create'])){
	Section::create($_POST['name'], $con);
	header("Location: ?saved=1");
} else if(isset($_POST['edit'])){
	Section::addMember($_POST['newSection'], $_POST['newRole'], $_POST['member'], $con);
	header("Location: ?saved=1");
}

$sections = Section::getAll(true, $con);
$roles = DB::executeQuery("SELECT * FROM Roles", $con);
DB::close($con);

$title = "Section Administration";
require_once('../head.php');
?>

<body>
	<div class="container">
		<h1>
			<?php echo $title ?>
			<button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">+ Add</button>
		</h1>

		<?php if(count($sections) == 0){ ?>
		<div class="alert alert-warning">No sections configured!</div>
		<?php
		}
		
		foreach($sections as $section){ 
		?>
		<div class="card mb-3">
			<div class="card-header">
				<?php echo $section->name ?>
			</div>
			<div class="card-body">
				<div class="row row-cols-1 row-cols-md-2">
					<div class="col">
						<div class="card mb-3">
							<div class="card-header button-header">Members <button class="btn btn-primary add-member" data-section="<?php echo $section->ID ?>">Add</button></div>
							<div class="list-group list-group-flush">
								<?php 
								$members = 0;
								foreach($section->members as $member){ 
									if($member->section->role->isStaff){
										continue;
									}
									$members++;
									?>
								<div class="list-group-item">
									<?php echo $member->fullName() ?> <span class="hover float-end"><a href="#" class="edit-member" data-member='{"name": "<?php echo $member->fullName() ?>", "id": "<?php echo $member->ID ?>"}' data-section='{"name": "<?php echo $section->name ?>", "id":
											<?php echo $section->ID ?>, "roleID":
											<?php echo $member->section->role->ID ?>}'>Edit
										</a> | <a href="member.php?id=<?php echo $member->ID ?>">View</a></span>
								</div>
								<?php 
								} 

								if($members == 0){
									?>
								<div class="list-group-item list-group-item-warning text-center">No members added</div>
								<?php } ?>
							</div>
						</div>
					</div>

					<div class="col">
						<div class="card mb-3">
							<div class="card-header button-header">Instructors <button class="btn btn-primary add-member" data-section="<?php echo $section->ID ?>">Add</button></div>
							<div class="list-group list-group-flush">
								<?php 
								$instructors = 0;
								foreach($section->members as $member){ 
									if(!$member->section->role->isStaff){
										continue;
									}
									$instructors++;
									?>
								<div class="list-group-item">
									<?php echo $member->fullName() ?>
									<span class="badge text-bg-primary">
										<?php echo $member->section->role->name ?>
									</span>
									<span class="hover float-end"><a href="#" class="edit-member" data-member='{"name": "<?php echo $member->fullName() ?>", "id": "<?php echo $member->ID ?>"}' data-section='{"name": "<?php echo $section->name ?>", "id":
											<?php echo $section->ID ?>, "roleID":
											<?php echo $member->section->role->ID ?>}'>Edit
										</a> | <a href="member.php?id=<?php echo $member->ID ?>">View</a></span>
								</div>
								<?php 
								}
								
								if($instructors == 0){
								?>
								<div class="list-group-item list-group-item-danger text-center">No instructors added</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>


	<!-- edit modal -->
	<form method="post">
		<div class="modal" id="editModal" tabindex="-1">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h1 class="modal-title fs-4">Edit Section for <span id="editName"></span></h1>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<p>Current section: <span class="fw-bold" id="editSection"></span></p>

						<div class="form-floating mb-3">
							<select class="form-select" id="editNewSection" name="newSection" required>
								<option value="">-- Please Select --</option>
								<?php foreach($sections as $section){ ?>
									<option value="<?php echo $section->ID ?>"><?php echo $section->name ?></option>
								<?php } ?>
							</select>
							<label for="editNewSection">New Section</label>
						</div>

						<div class="form-floating mb-3">
							<select class="form-select" id="editRole" name="newRole" required>
								<option value="">-- Please Select --</option>
								<?php foreach($roles as $role){ ?>
								<option value="<?php echo $role['ID'] ?>"><?php echo $role['Name'] ?></option>
								<?php } ?>
							</select>
							<label for="editRole">Role</label>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						<button name="edit" class="btn btn-primary">Save changes</button>
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" name="member" id="editMember" />
	</form>

	<!-- create modal -->
	<form method="post">
		<div class="modal" id="createModal" tabindex="-1">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h1 class="modal-title fs-4">Create Section</h1>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<div class="form-floating mb-3">
							<input type="text" name="name" id="name" class="form-control" placeholder="New Section Name" required />
							<label for="name">New Section Name</label>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						<button class="btn btn-primary" name="create">Create</button>
					</div>
				</div>
			</div>
		</div>
	</form>

	<!-- add modal -->
	<form method="post">
		<div class="modal" id="addModal" tabindex="-1">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h1 class="modal-title fs-4">Add Member</h1>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<div class="list-group list-group-horizontal text-center fs-2">
							<a class="list-group-item list-group-item-action py-5" id="newMember" href="member.php?section=">New Member</a>
							<a class="list-group-item list-group-item-action py-5">Existing Member</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>

	<script src="js/bootstrap.min.js"></script>
	<script>
		const editModal = new bootstrap.Modal(document.getElementById("editModal"), {
			backdrop: 'static'
		});

		const addModal = new bootstrap.Modal(document.getElementById("addModal"), {
			backdrop: 'static'
		});

		function showEditModal(member, section) {
			document.getElementById("editName").innerHTML = member.name;
			document.getElementById("editMember").value = member.id;
			document.getElementById("editSection").innerHTML = section.name;
			document.getElementById("editNewSection").value = section.id;
			document.getElementById("editRole").value = section.roleID;
			editModal.show();
		}

		function showAddModal(section){
			document.getElementById("newMember").href = `member.php?section=${section}`;
			addModal.show();
		}

		Array.from(document.getElementsByClassName("edit-member")).forEach(el => el.addEventListener("click", e => {
			e.preventDefault();
			showEditModal(JSON.parse(el.dataset.member), JSON.parse(el.dataset.section))
		}));

		Array.from(document.getElementsByClassName("add-member")).forEach(el => el.addEventListener("click", () => showAddModal(el.dataset.section)));
	</script>
</body>

</html>