<!DOCTYPE HTML>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Login</title>
		<link rel="icon" href="login.ico">
		<style>
			.welcome{
			text-align:center;
			font-size:14px;
			font-style:arial;
			}

			.container{
			max-width:500px;
			max-height:500px;
			border:3px solid black;
			border-radius:16px;
			background-color:white;
			}

			body{
			background-color:MintCream;
			padding-left:32%;
			padding-top:10%;
			}
			.forgotpassword{
				padding-left:85px;text-decoration:underline;color:black;
			}
			.forgotpassword:hover{
				text-decoration:underline;color:red;
			}
			.register{
				text-decoration:underline;color:black;padding:10px;
			}
			.register:hover{
				text-decoration:underline;color:green;
			}
			.loginbutton{
				width:100px;height:35px;text-align:center;border:2px solid black;border-radius:7px;color:white;background-color:black;
			}
			.loginbutton:hover{
				background-color:purple;
				color:white;
			}
		</style>
	</head>
	
	<body>
		<?php 
			require 'config.php';
			if(!empty($_SESSION["id"])){
				header("Location: /Web_Blog_Application/index.php");
			}
			//Assigning the input data to variables.
			$email = $password = "";
			$email_err = $password_err ="";
			$db_conn = $db_name = $db_password = $db_servername = $db_status = $db_username = $db_query = $db_result_set = $db_email_validation = $db_password_validation = "";
			
			if ($_SERVER["REQUEST_METHOD"] == "POST"){
								
								
				if(empty($_POST["email"]))
					$email_err = "Email is required";
				else{
					$email = modifyInputData($_POST["email"]);
					if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
						$email_err = "Mail is in Incorrect Format. Correct Format is: something@something.com";
					}
				}
				
				if(empty($_POST["password"]))
					$password_err = "Password is Required";
				else{
					$password = modifyInputData($_POST["password"]);
					if(strlen($password)<8){
						$password_err = "The length of Password should be greater than 8";
					}
				}
				
								
				// Importing all the data into Database Table
				$db_servername = "localhost:3306";
				$db_username = "root";
				$db_password = "";
				$db_name = "blog_web_application";
				
				// connect to DB
				$db_conn = mysqli_connect($db_servername, $db_username, $db_password, $db_name);
				
				// Checking the connection
				if(!$db_conn){
					die("Connection Failed !! Check your DB Connection".mysqli_connect_error);
				}
				
				// Taking the data from DB for Validation
				$db_query = "select Email, Password from Bloggers where Email = '".$email."';";
				if(mysqli_query($db_conn, $db_query)){
					$db_result_set = mysqli_query($db_conn, $db_query);
				}
				else{
					$db_status = "<span style="."font-family:Sans-serif;color:white;background-color:red;"."><b>Error: ".$db_query."<br>".mysqli_connect_error."</b></span>";
				}
				if(mysqli_num_rows($db_result_set)>0){
					while($row = mysqli_fetch_assoc($db_result_set)){
						$db_email_validation = $row["Email"];
						$db_password_validation = $row["Password"];
					}
				}
				else{
					$db_email_validation = $db_password_validation = "";
				}
				
				// Check whether the input values still have some issues.
				if($email_err or $password_err){
					$db_status = "<span style="."font-family:Sans-serif;color:white;background-color:red;"."><b>Please Enter the Valid details.</b></span>";
				}
				
				// Validate whether the email is correct or not
				else if(strcmp($email, $db_email_validation) != 0){
					$db_status = "<span style="."font-family:Sans-serif;color:white;background-color:red;"."><b>Incorrect Email. Please check !!</b></span>";
				}
				
				// Validate whether the password is correct or not
				else if(strcmp($password, $db_password_validation) != 0){
					$db_status = "<span style="."font-family:Sans-serif;color:white;background-color:red;"."><b>Incorrect Password.. Please check !!</b></span>";
				}
				
				// If Everything is fine, Successfully logged in
				else{
					// Showing success message
					$db_status = "<span style="."font-family:Sans-serif;color:white;background-color:green;"."><b>Logged in Successfully... </b>Redirecting to Home Page..</span>";
					$_SESSION["login"] = true;
					$_SESSION["id"] = $email;
					//Closing the DB Connection
					mysqli_close($db_conn);
					header('refresh:3; url=http://localhost:8012/Web_Blog_Application/index.php');	
				}
		}
		
		// writing the function for removing special chars if any
		function modifyInputData($data){
			$data = trim($data);
			$data = stripslashes($data);
			$data = htmlspecialchars($data);
			return $data;
			}
		?>
		<div class="container">
			<div class="welcome">
				<h1><b style="font-family: Arial, Helvetica, sans-serif;">Welcome to BLOGS !!</b></h1>
				<?php echo $db_status;?>
				<h2 style="color:red;"> To continue your stories, Please login..</h2>
			</div>
			<div>
				<form action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> method="POST">
					<div style="padding-left:18%;">
						<input type="text" name="email" id="email" value="<?php echo $email; ?>" placeholder="Email Address" style="padding:10px;font-size:15px;border:3px solid black;border-radius:7px;width:300px;height:25px">
					</div>
						<span style="padding-left:18%;color:red;"><?php echo $email_err; ?></span>
					<br>
					<div style="padding-left:18%;">
						<input type="password" name="password" id="password" value="<?php echo $password; ?>" placeholder="Password" style="padding:10px;font-size:15px;border:3px solid black;border-radius:7px;width:300px;height:25px">
					</div>
						<span style="padding-left:18%;color:red;"><?php echo $password_err; ?></span>
					<br>
					<div style="padding-left:18%;">
						<input type="submit" id="login" name="login" value="Login" class="loginbutton">
						<a href="forgotpassword.php" class="forgotpassword" target="_blank"><b>Forgot Password ??</b></a>
					</div><br>
					<div style="padding-left:23%;padding-bottom:20px;padding-top:10px;">
						<b style="font-family:sans-serif;font-size:20px;">New User??<a href="register.php" class="register" target="_blank">Register Here!!</a></b>
					</div>
				</form>
			</div>
		</div>
	</body>
</html>