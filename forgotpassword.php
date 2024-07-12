<!DOCTYPE HTML>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Change Your Password</title>
		<link rel="icon" href="passwordchange.ico">
		<style>
			body{
				background-color:MintCream;
			}
			
			.heading{
				padding:20px;
				font-size:32px;
				font-family:Sans-serif;
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
				width:125px;
				height:30px;
				border:3px solid black;
				border-radius:8px;
			}
			
			.submitbutton:hover{
				background-color:green;
				color:white;
			}
			
			.form{
				padding:20px;
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
			$security = $email = $password = $confirmpassword = "";
			$security_err = $email_err = $password_err = $confirmpassword_err = "";
			$db_conn = $db_name = $db_password = $db_servername = $db_status = $db_username = $db_query = $db_result_set = $db_email_validation = $db_security_validation = "";
			
			if($_SERVER["REQUEST_METHOD"] == "POST"){
				
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
				
				if(empty($_POST["security"]))
					$security_err = "Answering the security question is required";
				else
					$security = modifyInputData($_POST["security"]);
				
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
				
				// Extracting Data for Email and Security Validation
				$db_query = "select Email, Security_Answer from Bloggers where Email = '".$email."';";
				if(mysqli_query($db_conn, $db_query)){
					$db_result_set = mysqli_query($db_conn, $db_query);
				}
				else{
					$db_status = "<span style="."font-family:Sans-serif;color:white;background-color:red;"."><b>Error: ".$db_query."<br>".mysqli_connect_error."</b></span>";
				}
				if(mysqli_num_rows($db_result_set)>0){
					while($row = mysqli_fetch_assoc($db_result_set)){
						$db_email_validation = $row["Email"];
						$db_security_validation = $row["Security_Answer"];
					}
				}
				else{
					$db_email_validation = $db_security_validation = "";
				}
				
				// Check if any input has null values.
				if($password_err or $confirmpassword_err or $security_err or $email_err){
					$db_status = "<span style="."font-family:Sans-serif;color:white;background-color:red;"."><b>Please Enter all data.</b></span>";
				}
				
				// check whether the email is valid or not.
				else if(strcmp($email, $db_email_validation) != 0){
					$db_status = "<span style="."font-family:Sans-serif;color:white;background-color:red;"."><b>Incorrect Email. Please enter valid Email...</b></span>";
				}
				
				// check whether the security answer is valid or not
				else if(strcasecmp($security, $db_security_validation) != 0){
					$db_status = "<span style="."font-family:Sans-serif;color:white;background-color:red;"."><b>Incorrect Answer for Security Question. Please Check...</b></span>";
				}
				
				// If everything is fine, then password will be updated.
				else{
					$db_query = "update Bloggers set Password = '".$password."', Confirm_Password = '".$confirmpassword."' where Email = '".$email."';";
					if(mysqli_query($db_conn, $db_query)){
						// Showing success message
						$db_status = "<span style="."font-family:Sans-serif;color:white;background-color:green;"."><b>Password Changed Successfully... </b>Redirecting to Login Page..</span>";
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
		<div>
			<div class="heading">
				<b>Change Your Password</b>
			</div>
			
			<div class="form">
				<span style="padding-left:18%;"><?php echo $db_status;?></span>
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
					<div style="padding-left:18%;"><input type="text" id="email" name="email" class="textbox" value="<?php echo $email; ?>" placeholder="Email Address"></div>
						<span style="padding-left:18%;color:red;"><?php echo $email_err; ?></span>
					<br>
					<div style="padding-left:18%;"><label><b style="font-family:Sans-serif;">Security Question: </b><b style="font-family:Sans-serif;">What is your Father's Maiden Name?</b></label>
					<br><input type="text" id="security" name="security" class="textbox" value="<?php echo $security; ?>" placeholder="Answer for Security Question.."></div>
						<span style="padding-left:18%;color:red;"><?php echo $security_err; ?></span>
					<br>				
					<div style="padding-left:18%"><input id="password" name="password" type="password" class="textbox" value="<?php echo $password; ?>" placeholder="New Password"></div>
						<span style="padding-left:18%;color:red;"><?php echo $password_err; ?></span>
					<br>
					<div style="padding-left:18%"><input type="password" id="confirmpassword" name="confirmpassword" class="textbox" value="<?php echo $confirmpassword; ?>" placeholder="Confirm New Password"></div>
						<span style="padding-left:18%;color:red;"><?php echo $confirmpassword_err; ?></span>
					<br>
					<div style="padding-left:18%"><input type="submit" id="submit" name="submit" class="submitbutton" value="SUBMIT"></div>
				</form>
			</div>
		</div>
	</body>
</html>