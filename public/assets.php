<?php
require_once("../lib/Asset.php");
require_once("../lib/Utils.php");

$assets = Asset::getAll();

$title = "Assets";
require_once('../head.php');
?>
<body>
	<div class="container">
		<h1><?php echo $title ?></h1>
		<?php if(count($assets) == 0){ ?>
			<div class="alert alert-warning">No assets registered</div>
		<?php } else { ?>

		<div class="input-group mb-3">
			<label for="sorter" class="input-group-text">Show</label>
			<select class="form-control" id="sorter">
				<option value="1">All</option>
			</select>
		</div>

		<div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 text-center">
			<?php foreach($assets as $asset){ ?>
				<div class="col mb-3">
					<div class="card h-100">
						<div class="card-header" style="background-color: <?php echo isset($asset->section) ? $asset->section->colour : '#DDD' ?>">Section: <?php echo isset($asset->section) ? $asset->section->name : "Not set" ?></div>
						<div class="card-body">
							<h2><?php echo $asset->name ?></h2>
							<p><?php echo Utils::truncateText($asset->description) ?></p>
							<p>Hire cost: £<?php echo $asset->hireCost ?></p>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
		<?php } ?>
	</div>
</body>
</html>