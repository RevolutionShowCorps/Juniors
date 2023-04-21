<?php
$title = "Section Administration";
require_once('head.php');
?>

<body>
	<div class="container">
		<h1><?php echo $title ?></h1>

		<div class="card">
			<div class="card-header">Brass</div>
			<div class="card-body">
				<div class="row row-cols-1 row-cols-md-2">
					<div class="col">
						<div class="card mb-3">
							<div class="card-header button-header">Members <button class="btn btn-primary">Add</button></div>
							<div class="list-group list-group-flush">
								<div class="list-group-item">Jacob Gill <span class="hover float-end">Edit | <a href="member.php">View</a></span></div>
								<div class="list-group-item">Oliver Archaki <span class="hover float-end">Edit | <a href="member.php">View</a></span></div>
								<div class="list-group-item">Albie Jaques <span class="hover float-end">Edit | <a href="member.php">View</a></span></div>
								<div class="list-group-item">Sofia Cumbo <span class="hover float-end">Edit | <a href="member.php">View</a></span></div>
								<div class="list-group-item">Evangeline Pedder-Stratton <span class="hover float-end">Edit | <a href="member.php">View</a></span></div>
								<div class="list-group-item">Preeyan Mistry <span class="hover float-end">Edit | <a href="member.php">View</a></span></div>
							</div>
						</div>
					</div>

					<div class="col">
						<div class="card mb-3">
							<div class="card-header button-header">Instructors <button class="btn btn-primary">Add</button></div>
							<div class="list-group list-group-flush">
								<div class="list-group-item">Sam Martin <span class="badge text-bg-primary">Caption Head</span> <span class="hover float-end">Edit | <a href="member.php">View</a></span></div>
								<div class="list-group-item">Megan Mouncey <span class="hover float-end">Edit | <a href="member.php">View</a></span></div>
								<div class="list-group-item">Phillip Sorrenson <span class="hover float-end">Edit | <a href="member.php">View</a></span></div>
								<div class="list-group-item">Megan Spencer <span class="hover float-end">Edit | <a href="member.php">View</a></span></div>
								<div class="list-group-item">Oliver Richardson <span class="hover float-end">Edit | <a href="member.php">View</a></span></div>
								<div class="list-group-item">Mike Seymour <span class="hover float-end">Edit | <a href="member.php">View</a></span></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>

</html>