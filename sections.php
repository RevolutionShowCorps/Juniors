<?php

$sections = array(
	array(
		"id"=>1,
		"name"=>"Brass",
		"members"=>array(
			array(
				"name"=>"Jacob Gill",
				"role"=>array(
					"id"=>1, 
					"name"=>"Member"
				)
			),
			array(
				"name"=>"Oliver Archaki",
				"role"=>array(
					"id"=>1,
					"name"=>"Member"
				)
			),
			array(
				"name"=>"Albie Jaques",
				"role"=>array(
					"id"=>1, 
					"name"=>"Member"
				)
			),
			array(
				"name"=>"Sofia Cumbo",
				"role"=>array(
					"id"=>1,
					"name"=>"Member"
				)
			),
			array(
				"name"=>"Evangeline Pedder-Stratton",
				"role"=>array(
					"id"=>1,
					"name"=>"Member"
				)
			),
			array(
				"name"=>"Preeyan Mistry",
				"role"=>array(
					"id"=>1,
					"name"=>"Member"
				)
			),
			array(
				"name"=>"Sam Martin",
				"role"=>array(
					"id"=>4,
					"name"=>"Caption Head"
				)
			),
			array(
				"name"=>"Megan Mouncey",
				"role"=>array(
					"id"=>2,
					"name"=>"Instructor"
				)
			),
			array(
				"name"=>"Megan Spencer",
				"role"=>array(
					"id"=>2,
					"name"=>"Instructor"
				)
			),
			array(
				"name"=>"Oliver Richardson",
				"role"=>array(
					"id"=>2,
					"name"=>"Instructor"
				)
			),
			array(
				"name"=>"Mike Seymour",
				"role"=>array(
					"id"=>2,
					"name"=>"Instructor"
				)
			),
			array(
				"name"=>"Phillip Sorrenson",
				"role"=>array(
					"id"=>3,
					"name"=>"Junior Instructor"
				)
			)
		)
	)
);

$title = "Section Administration";
require_once('head.php');
?>

<body>
	<div class="container">
		<h1>
			<?php echo $title ?>
		</h1>

		<?php foreach($sections as $section){ ?>
		<div class="card">
			<div class="card-header"><?php echo $section['name'] ?></div>
			<div class="card-body">
				<div class="row row-cols-1 row-cols-md-2">
					<div class="col">
						<div class="card mb-3">
							<div class="card-header button-header">Members <button class="btn btn-primary">Add</button></div>
							<div class="list-group list-group-flush">
								<?php 
								foreach($section['members'] as $member){ 
									if($member['role']['id'] > 1){
										continue;
									}
									?>
								<div class="list-group-item"><?php echo $member['name'] ?> <span class="hover float-end"><a href="#" class="edit-member" data-name="<?php echo $member['name'] ?>" data-section='{"name": "<?php echo $section['name'] ?>", "id": <?php echo $section['id'] ?>, "roleID": <?php echo $member['role']['id'] ?>}'>Edit</a> | <a href="member.php">View</a></span></div>
								<?php } ?>
								</div>
						</div>
					</div>

					<div class="col">
						<div class="card mb-3">
							<div class="card-header button-header">Instructors <button class="btn btn-primary">Add</button></div>
							<div class="list-group list-group-flush">
								<?php 
								foreach($section['members'] as $member){ 
									if($member['role']['id'] == 1){
										continue;
									}
									?>
								<div class="list-group-item"><?php echo $member['name'] ?> 
									<?php if($member['role']['id'] > 2){ ?>
										<span class="badge text-bg-primary"><?php echo $member['role']['name'] ?>	</span>
									<?php } ?>
									<span class="hover float-end"><a href="#" class="edit-member" data-name="<?php echo $member['name'] ?>" data-section="<?php echo $section['name'] ?>">Edit</a> | <a href="member.php">View</a></span></div>
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

	<script src="js/bootstrap.min.js"></script>
	<script>
		const editModal = new bootstrap.Modal(document.getElementById("editModal"), {
			backdrop: 'static'
		});

		function showEditModal(name, section){
			document.getElementById("editName").innerHTML = name;
			document.getElementById("editSection").innerHTML = section.name;
			document.getElementById("editNewSection").value = section.id;
			document.getElementById("editRole").value = section.roleID;
			editModal.show();
		}

		Array.from(document.getElementsByClassName("edit-member")).forEach(el => el.addEventListener("click", e => {
			e.preventDefault();
			showEditModal(el.dataset.name, JSON.parse(el.dataset.section))
		}));
	</script>
</body>

</html>