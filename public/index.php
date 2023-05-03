<?php
require_once(__DIR__ . "/../lib/DB.php");
session_start();

function doLogin($email, $password){
	$user = DB::executeQueryForSingle("SELECT * FROM Users WHERE Email = ?", null, "s", $email);
	if($user == null){
		return false;
	}

	if(!password_verify($password, $user['Password'])){
		return false;
	}

	$_SESSION['user'] = $user;
	return true;
}

$login = true;

if(isset($_POST['email']) && isset($_POST['password'])){
	$login = doLogin($_POST['email'], $_POST['password']);

	if($login){
		header("Location: sections.php");
		die();
	}
}

$title = "Log In";
require_once("../head.php")
?>

<body>
	<div class="container">
		<h1>
			<?php echo $title ?>
		</h1>

<?php if(!$login){ ?>
	<div class="alert alert-danger">Incorrect Email or Password. Please try again</div>
<?php } ?>

		<form method="post">
			<div class="form-floating mb-3">
				<input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
				<label for="email">Email address</label>
			</div>
			<div class="form-floating mb-3">
				<input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
				<label for="password">Password</label>
			</div>

			<div class="text-center">
				<button class="btn btn-success">Log In</button>
			</div>
		</form>
	</div>
</body>

</html>