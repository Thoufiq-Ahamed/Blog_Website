<!DOCTYPE HTML>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Register</title>
		<link rel="icon" href="register.png">
		<style>
			.heading{
				text-align:center;
				color:darkblue;
			}
			
			.Error{
				color:red;
			}
			
			.textbox{
					padding:10px;
					font-size:15px;
					border:3px solid black;
					border-radius:7px;
					width:300px;
					height:25px;
			}
			
			.submitbutton{
				background-color:black;
				color:white;
				width:200px;
				height:30px;
				border:3px solid black;
				border-radius:8px;
			}
			
			.submitbutton:hover{
				background-color:purple;
				color:white;
			}
					
			.container{
				padding:10px;
				max-width:600px;
				max-height:1000px;
				margin:auto;
				border:1px solid black;
				border-radius:16px;
				background-color:white;
			}

			body{
				background-color:MintCream;
				padding-left:80px;
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
		$firstname = $lastname = $email = $password = $confirmpassword = $passwordhint = $security ="";
		$firstname_err = $lastname_err = $email_err = $password_err = $confirmpassword_err = $passwordhint_err = $security_err = $termsandconditions_err ="";
		$db_conn = $db_name = $db_password = $db_servername = $db_status = $db_username = $db_query = $db_result_set = $db_email_validation = "";
		
		if ($_SERVER["REQUEST_METHOD"] == "POST"){
			if (empty($_POST["firstname"])){
				$firstname_err = "First Name is required";
			}
			else {
				$firstname = modifyInputData($_POST["firstname"]);
			}
			
			if(empty($_POST["lastname"]))
				$lastname_err = "Last Name is required";
			else
				$lastname = modifyInputData($_POST["lastname"]);
			
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
			
			if(empty($_POST["confirmpassword"]))
				$confirmpassword_err = "Confirm Password is required";
			else{
				$confirmpassword = modifyInputData($_POST["confirmpassword"]);
				if(strtolower($password) != strtolower($confirmpassword)){
					$confirmpassword_err = "Password not match!!";
				}
			}
			
			if(empty($_POST["passwordhint"]))
				$passwordhint_err = "Password Hint is required";
			else
				$passwordhint = modifyInputData($_POST["passwordhint"]);
			
			if(empty($_POST["security"]))
				$security_err = "Answering the security question is required";
			else
				$security = modifyInputData($_POST["security"]);
			
			if(empty($_POST["termsandconditions"]))
				$termsandconditions_err = "Terms & Conditions need to be Checked";
			
			// Importing all the data into Database Table
			$db_servername = "localhost:3306";
			$db_username = "root";
			$db_password = "";
			$db_name = "blog_web_application";
			$db_status = "";
			
			// connect to DB
			$db_conn = mysqli_connect($db_servername, $db_username, $db_password, $db_name);
			
			// Checking the connection
			if(!$db_conn){
				die("Connection Failed !! Check your DB Connection".mysqli_connect_error);
			}
			
			$db_query = "select Email from Bloggers where Email = '".$email."';";
			if(mysqli_query($db_conn, $db_query)){
				$db_result_set = mysqli_query($db_conn, $db_query);
			}
			else{
				$db_status = "<span style="."font-family:Sans-serif;color:white;background-color:red;"."><b>Error: ".$db_query."<br>".mysqli_connect_error."</b></span>";
			}
			if(mysqli_num_rows($db_result_set)>0){
				while($row = mysqli_fetch_assoc($db_result_set)){
					$db_email_validation = $row["Email"];
				}
			}
			else{
				$db_email_validation = "";
			}
			
			if($firstname_err or $lastname_err or $email_err or $password_err or $confirmpassword_err or $passwordhint_err or $security_err or $termsandconditions_err){
				$db_status = "<span style="."font-family:Sans-serif;color:white;background-color:red;"."><b>Please fill the form correctly.</b></span>";
			}
			// Validate whether the email is already present or not
			else if($email == $db_email_validation){
				$db_status = "<span style="."font-family:Sans-serif;color:white;background-color:red;"."><b>User Already Exists. Click the Login link below..</b></span>";
			}
			else{
				$db_query = "INSERT INTO Bloggers(First_Name, Last_Name, Email, Password, Confirm_Password, Password_Hint, Security_Answer) VALUES
				('".$firstname."', '".$lastname."', '".$email."', '".$password."', '".$confirmpassword."', '".$passwordhint."', '".$security."');";
				if(mysqli_query($db_conn, $db_query)){
					// Showing success message
					$db_status = "<span style="."font-family:Sans-serif;color:white;background-color:green;"."><b>Registered Successfully... </b>Redirecting to Login Page..</span>";
					//Closing the DB Connection
					mysqli_close($db_conn);
					header('refresh:3; url=http://localhost:8012/Web_Blog_Application/login.php');
				}
				else{
					$db_status = "<span style="."font-family:Sans-serif;color:white;background-color:red;"."><b>Error: ".$db_query."<br>".mysqli_connect_error."</b></span>";
				}	
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
			<div class="heading">
				<h2>Register for start your new Journey !!</h2>
				<?php echo $db_status;?>
			</div>
			
			<div><form  method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
				<div style="padding-left:18%;"><input type="text" id="firstname" name="firstname" class="textbox" value="<?php echo $firstname; ?>" placeholder="First Name"></div>
					<span style="padding-left:18%;" class="Error"><?php echo $firstname_err; ?></span>
				<br>
				<div style="padding-left:18%;"><input type="text" id="lastname" name="lastname" class="textbox" value="<?php echo $lastname; ?>" placeholder="Last Name"></div>
					<span style="padding-left:18%;" class="Error"><?php echo $lastname_err; ?></span>
				<br>
				<div style="padding-left:18%;"><input type="text" id="email" name="email" class="textbox" value="<?php echo $email; ?>" placeholder="Email Address"></div>
					<span style="padding-left:18%;" class="Error"><?php echo $email_err; ?></span>
				<br>
				<div style="padding-left:18%;"><input type="password" id="password" name="password" class="textbox" value="<?php echo $password; ?>" placeholder="New Password"></div>
					<span style="padding-left:18%;" class="Error"><?php echo $password_err; ?></span>
				<br>
				<div style="padding-left:18%;"><input type="password" id="confirmpassword" name="confirmpassword" class="textbox" value="<?php echo $confirmpassword; ?>" placeholder="Confirm Password"></div>
					<span style="padding-left:18%;" class="Error"><?php echo $confirmpassword_err; ?></span>
				<br>
				<div style="padding-left:18%;"><input type="text" id="passwordhint" name="passwordhint" class="textbox" value="<?php echo $passwordhint; ?>" placeholder="Hint For Your Password"></div>
				<span style="padding-left:18%;" class="Error"><?php echo $passwordhint_err; ?></span>
				<br>
				<div style="padding-left:18%;"><label><b style="font-family:Sans-serif;">Security Question: </b><b style="font-family:Sans-serif;">What is your Father's Maiden Name?</b></label>
				<br><input type="text" id="security" name="security" class="textbox" value="<?php echo $security;?>" placeholder="Answer for Security Question.."></div>
					<span style="padding-left:18%;" class="Error"><?php echo $security_err; ?></span>
				<br>
				<div style="padding-left:18%;"><input type="checkbox" id="termsandconditions" name="termsandconditions"><b style="font-family:Sans-serif;">Agree to <a href="termsandconditions.php" target="_blank">Terms & Conditions</a></b></div>
					<span style="padding-left:18%;" class="Error"><?php echo $termsandconditions_err; ?></span>
				<br>
				<div style="padding-left:18%"><input type="submit" id="submit" name="submit" class="submitbutton" value="Start a New Journey !!"></div><br>
				<div style="padding-left:25%"><b style="font-family:Sans-serif;">Already a member, <a href="login.php" target="_blank">Click Here</a> to Login</b></div><br>
			</form>
			</div>
		</div>
	</body>
</html>