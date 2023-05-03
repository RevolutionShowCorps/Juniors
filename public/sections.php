<?php
require_once("../secure.php");

require_once("../lib/Section.php");

$sections = Section::getAll();

if(isset($_POST['create'])){
	Section::create($_POST['name']);
	header("Location: ?saved=1");
}

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
		<div class="card">
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
								foreach($section->members as $member){ 
									if($member['role']['id'] > 1){
										continue;
									}
									?>
								<div class="list-group-item">
									<?php echo $member['name'] ?> <span class="hover float-end"><a href="#" class="edit-member" data-name="<?php echo $member['name'] ?>" data-section='{"name": "<?php echo $section->name ?>", "id":
											<?php echo $section->ID ?>, "roleID":
											<?php echo $member['role']['id'] ?>}'>Edit
										</a> | <a href="member.php">View</a></span>
								</div>
								<?php } ?>
							</div>
						</div>
					</div>

					<div class="col">
						<div class="card mb-3">
							<div class="card-header button-header">Instructors <button class="btn btn-primary add-member" data-section="<?php echo $section->ID ?>">Add</button></div>
							<div class="list-group list-group-flush">
								<?php 
								foreach($section->members as $member){ 
									if($member['role']['id'] == 1){
										continue;
									}
									?>
								<div class="list-group-item">
									<?php echo $member['name'] ?>
									<?php if($member['role']['id'] > 2){ ?>
									<span class="badge text-bg-primary">
										<?php echo $member['role']['name'] ?>
									</span>
									<?php } ?>
									<span class="hover float-end"><a href="#" class="edit-member" data-name="<?php echo $member['name'] ?>" data-section="<?php echo $section->fullName ?>">Edit</a> | <a href="member.php">View</a></span>
								</div>
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
						<select class="form-select" id="editNewSection" required>
							<option value="">-- Please Select --</option>
							<option value="1">Brass</option>
						</select>
						<label for="section">New Section</label>
					</div>

					<div class="form-floating mb-3">
						<select class="form-select" id="editRole" required>
							<option value="">-- Please Select --</option>
							<option value="1">Member</option>
							<option value="2">Instructor</option>
							<option value="3">Junior Instructor</option>
							<option value="3">Caption Head</option>
						</select>
						<label for="role">Role</label>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary">Save changes</button>
				</div>
			</div>
		</div>
	</div>

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
						<h1 class="modal-title fs-4">Create Section</h1>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<div class="list-group list-group-horizontal">
							<a class="list-group-item list-group-item-action" id="newMember" href="member.php?section=">New Member</a>
							<a class="list-group-item list-group-item-action">Existing Member</a>
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

		function showEditModal(name, section) {
			document.getElementById("editName").innerHTML = name;
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
			showEditModal(el.dataset.name, JSON.parse(el.dataset.section))
		}));

		Array.from(document.getElementsByClassName("add-member")).forEach(el => el.addEventListener("click", () => showAddModal(el.dataset.section)));
	</script>
</body>

</html>