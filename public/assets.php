<?php
require_once("../lib/Asset.php");
require_once("../lib/Utils.php");

$filters = array("All", "Available Only", "Hired Out Only");
switch(@$_GET['filter']){
	case 1:
		$assets = Asset::getAvailable();
		break;

	case 2:
		$assets = Asset::getLoanedOut();
		break;

	default:
		$assets = Asset::getAll();
}

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
			<label for="filter" class="input-group-text">Show</label>
			<select class="form-control" id="filter">
				<?php for($i = 0; $i < count($filters); $i++){ ?>
					<option value="<?php echo $i ?>"<?php if(@$_GET['filter'] == $i){?> selected<?php } ?>><?php echo $filters[$i] ?></option>
				<?php } ?>
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
						<div class="card-footer text-bg-<?php echo isset($asset->currentHire) ? "warning" : "success" ?>">
							<?php echo isset($asset->currentHire) ? "Currently on hire with " . $asset->currentHire->member->fullName() : "Available for use" ?>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
		<?php } ?>
	</div>

	<script>
		document.getElementById("filter").addEventListener("change", function(e){
			document.location.href = `?filter=${this.value}`;
		});
	</script>
</body>
</html>