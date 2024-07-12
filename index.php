<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>MY BLOGS</title>
		<link rel="stylesheet" href="templatestyles.css">
		<script>
			function show(id){
				document.getElementById(id).style.display='block';
			}
			function selectStory(email, title){
				var xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					document.getElementById("content").innerHTML = this.responseText;
				}
				};
				xmlhttp.open("GET","getBlogContent.php?email="+email+"& title="+title,true);
				xmlhttp.send();
				
				//document.getElementById("content").style.display='block';
			}
			function warning(){
				alert("Login needed to read this content !!");
			}
		</script>
		<style>
			.header{
				background-color:SeaGreen;
				top:0;
				width:100%;
				padding:10px;
				position:relative;
				overflow:hidden;
				border-bottom:3px solid black;
			}
			
			.blogTitle{
				font-size:30px;
				padding-left:20px;
				font-family:Sans-serif;
				color:white;
			}
			
			.header a{
				float:right;
				color:white;
				font-family:Sans-serif;
				text-align:center;
				padding:10px;
				text-decoration:none;
			}
			
			.header a:hover{
				background-color:black;
				color:white;
			}
			
			.searchbox{
				font-family:Sans-serif;
				height:30px;
				padding:12px;
				width:550px;
				margin:auto;
				border:1px solid;
			}
			
			.searchbutton{
				width:70px;
				height:30px;
				background-color:white;
				color:black;
			}
			
			.searchbutton:hover{
				background-color:black;
				color:white;
			}
			
			.footer{
				background-color:SeaGreen;
				position:relative;
				width:100%;
				bottom:0;
				padding:10px;
				border-top:3px solid black;
				overflow:hidden;
			}
			
			.footer a{
				float:left;
				color:white;
				font-family:Sans-serif;
				text-align:center;
				padding:10px;
				text-decoration:none;
			}
			
			.footer a:hover{
				text-decoration:underline;
				color:white;
			}
			
			*{
				box-sizing:border-box;
			}
			
			body{
				margin:0;
			}
			
			.column{
				float:right;
				border-left:3px solid black;
			}
			
			.bloglist{
				width:30%;
				height:100vh;
				background-color:grey;
				color:white;
			}
			
			.blogcontent{
				width:70%;
				height:100vh;
			}
			
			.container{
				width:100%;
				height:100%;
				overflow:hidden;
			}
			
			.iframe{
				width:70vw;
				height:100vh;
				resize:width;
			}
			
			.maincontainer{
				width:100%;
				height:100%;
				overflow:hidden;
				background-color:white;
			}
			
			.row:after{
				content:"";
				clear:both;
				display:table;
			}
		</style>
	</head>
	
	<body>
		<?php
			// Assigning variables
			$email = $conn = $result_set = $Username = $db_status = $loginbutton_visible = $signupbutton_visible = $logoutbutton_visible = $createblogbutton_visible = "";
			$bloglist = $blogcontent = $blogtitle = $blogdescription = $otherslist = "";
			$blogtitle_err = $blogdescription_err = "";
			
			// Including Config.php file for session_start
			require 'config.php';
			
			// If session is started with some users, access the blog items and creating their own blogs.
			if(!empty($_SESSION['id'])){
				// Welcoming the User
				$email = $_SESSION['id'];
				$db_conn = mysqli_connect("localhost:3306", "root", "", "blog_web_application");
				$result_set = mysqli_query($db_conn, "SELECT First_Name from bloggers where Email = '".$email."';");
				$row = mysqli_fetch_assoc($result_set);
				$Username = 'Welcome, '.$row["First_Name"];
				$author = $row["First_Name"];
				
				// Assigning appropriate buttons. Logout and Create Own Blog buttons are shown only if user logged in.
				$logoutbutton_visible = '<a id="logoutbutton" href="logout.php"><b>Logout</b></a>';
				$createblogbutton_visible = ' <input type="button" onclick="show('."'createblog'".')" 
				style="width:450px;height:80px;font-size:20px;background-color:black;color:white;font-family:Sans-serif;" 
				onMouseOver="this.style.color='."'green'".'" onMouseOut="this.style.color='."'white'".'" value="Create Your Own Blog!!"></button>';
				
				// Storing Your post in DB
				if($_SERVER["REQUEST_METHOD"] == "POST"){
					if (empty($_POST["title"])){
						$blogtitle_err = "Blog Title is required";
					}
					else {
						$blogtitle = modifyInputData($_POST["title"]);
					}
					
					if(empty($_POST["story"]))
						$blogdescription_err = "We are eager to see your Story";
					else
						$blogdescription = modifyInputData($_POST["story"]);
					
					// Checking the connection
					if(!$db_conn){
						die("Connection Failed !! Check your DB Connection".mysqli_connect_error);
					}
					
					
					if($blogtitle_err or $blogdescription_err){
						$db_status = "<span style="."font-family:Sans-serif;color:white;background-color:red;"."><b>Please Complete Your Story !!</b></span>";
					}
					
					else{
						$db_query = "INSERT INTO Blogs(Title, Author, Email, Story) VALUES
						('".$blogtitle."', '".$author."', '".$email."', '".$blogdescription."');";
						if(mysqli_query($db_conn, $db_query)){
							// Showing success message
							$db_status = "<span style="."font-family:Sans-serif;color:white;background-color:green;"."><b>Kudos !! You posted your blog !!</b></span>";
						}
						else{
							$db_status = "<span style="."font-family:Sans-serif;color:white;background-color:red;"."><b>Error: ".$db_query."<br>".mysqli_connect_error."</b></span>";
						}	
					}
				}
				
				// List your Blogs
				$db_query = "SELECT Email, Title FROM Blogs where Email = '".$email."';";
				$result_set = mysqli_query($db_conn, $db_query);
				if(mysqli_num_rows($result_set) == 0){
					$bloglist = '<b style="background-color:red;color:white;">You have no Stories till now... Click on button above to post your story...</b>';
				}
				else{
					$bloglist = '<b style="font-size:30px;">Your Stories</b><br><ul>';
					while($row = mysqli_fetch_assoc($result_set)){
						$bloglist = $bloglist.'<li style="padding-bottom:15px;><a href="#content" onclick="selectStory('."'".$row["Email"]."'".', '."'".$row["Title"]."'".')" style="text-decoration:none;color:white;" 
						onMouseOver="this.style.color='."'darkblue'".'"; onMouseOut="this.style.color='."'white'".'";>'.$row["Title"].'</a></li>';
					}
					$bloglist = $bloglist.'</ul><br>
					<b style="font-family:Sans-serif; font-size:20px;">------------------------- End of the Line ------------------</b>';
				}
				
				// List Others Blogs
				$db_query= "SELECT Title, Email, Author FROM Blogs where Email != '".$email."';";
				$result_set = mysqli_query($db_conn, $db_query);
				if(mysqli_num_rows($result_set) == 0){
					$otherslist = '<br><b style="padding-top:10px;font-size:30px">Other Blogger Stories</b><br><b style="background-color:red;color:white;">No Other Stories by others...</b>';
				}
				else{
					$otherslist = '<b style="padding-top:10px;font-size:30px">Other Blogger Stories</b><br><ul>';
					while($row = mysqli_fetch_assoc($result_set)){
						$otherslist = $otherslist.'<li style="padding-bottom:15px;"><a href="#content" onclick="selectStory('."'".$row["Email"]."'".', '."'".$row["Title"]."'".')" style="text-decoration:none;color:white;" 
						onMouseOver="this.style.color='."'darkblue'".'"; onMouseOut="this.style.color='."'white'".'";>'.$row["Title"].'</a><br>
						<b style="padding-left:80px;"> -By: '.$row["Author"].'</b></li>';
					}
					$otherslist = $otherslist.'</ul>';
				}
				
				// Display default Blog
				$db_query= "SELECT MIN(Blog_Id) AS Blog_ID, Author, Title, Email, Story From Blogs where Email = '".$email."' GROUP BY Email ORDER BY Blog_Id;";
				$result_set = mysqli_query($db_conn, $db_query);
				if(mysqli_num_rows($result_set) > 0){
					$row = mysqli_fetch_assoc($result_set);
					$blogcontent = '<b style="font-family:Sans-serif;font-size:50px;">'.$row["Title"].'</b><br>
									<b style="font-family:Sans-serif;font-size:20px;">By: '.$row["Author"].'</b><br><br>
									<br><b style=";font-family:Sans-serif;font-size:30px;">'.$row["Story"].'</b>';
				}
				else{
					$blogcontent = '<b style="font-size:30px;background-color:red;color:white;">You have no Stories till now... Click on Create button to post your story...</b>';
				}
				mysqli_close($db_conn);
				
			}
			else{
				// Assigning appropriate buttons. Login and Signup buttons are shown only if user logged out / none of the users logged in..
				$loginbutton_visible = '<a id="loginbutton" href="login.php"><b>Login</b></a>';
				$signupbutton_visible = '<a id="signupbutton" href="register.php"><b>Sign Up</b></a>';
				$db_conn = mysqli_connect("localhost:3306", "root", "", "blog_web_application");
				
				// List Others Blogs
				$db_query= "SELECT Email, Title, Author FROM Blogs;";
				$result_set = mysqli_query($db_conn, $db_query);
				if(mysqli_num_rows($result_set) == 0){
					$otherslist = 'No Stories...';
				}
				else{
					$otherslist = '<b style="padding-top:10px;font-size:30px">All Stories</b><br><ul>';
					while($row = mysqli_fetch_assoc($result_set)){
						$otherslist = $otherslist.'<li style="padding-bottom:15px;"><a href="#content" onclick="warning()" style="text-decoration:none;color:white;" 
						onMouseOver="this.style.color='."'darkblue'".'"; onMouseOut="this.style.color='."'white'".'";>'.$row["Title"].'</a><br>
						<b style="padding-left:80px;"> -By: '.$row["Author"].'</b></li>';
					}
					$otherslist = $otherslist.'</ul>';
				}
				
				// Display default Blog
				$db_query= "SELECT MIN(Blog_Id) AS Blog_ID, Author, Title, Email, Story From Blogs GROUP BY Email ORDER BY Blog_Id;";
				$result_set = mysqli_query($db_conn, $db_query);
				if(mysqli_num_rows($result_set) > 0){
					$row = mysqli_fetch_assoc($result_set);
					$blogcontent = '<b style="font-family:Sans-serif;font-size:50px;">'.$row["Title"].'</b><br>
									<b style="font-family:Sans-serif;font-size:20px;">By: '.$row["Author"].'</b><br><br>
									<br><b style="color: transparent;text-shadow: 0 0 15px rgba(0,0,0,0.5);font-family:Sans-serif;font-size:30px;">'.$row["Story"].'</b>';
				}
				else{
					$blogcontent = '<b style="font-size:30px;background-color:red;color:white;">Nobody Using this Blog :(</b>';
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
		<div class="maincontainer">
			<!-- Header -->
			<div class="header">
				<h1 class="blogTitle"><b>MY BLOGS</b><b style="text-align:center;font-family:Sans-serif; color:white;float:right;"><?php echo $Username; ?></b></h1>
				<input id="searchbox" name="searchbox" class="searchbox" type="text" placeholder="Search Blogs...">
				<input type="button" id="searchbutton" name="searchbutton" class="searchbutton" value="Search">
				<?php echo $logoutbutton_visible; ?>
				<?php echo $signupbutton_visible; ?>
				<?php echo $loginbutton_visible; ?>
			</div>
			
			<!-- Main Section -->
			<div class="container">
				<div class="row">
					<!-- List of blogs which displayed on right side -->
					<div class="column bloglist">
						<span><?php echo $createblogbutton_visible; ?></span>
						<div>
							<div style="font-size:20px;font-family:Sans-serif;"><b><?php echo $bloglist;?></b></div>
						</div>
						<div>
							<div style="font-size:20px;font-family:Sans-serif;"><b><?php echo $otherslist;?></b></div>
						</div>
					</div>
					
					<!-- Blogs expansion of corresponding blog name -->
					<div class="column blogcontent">
						<div id="createblog" style="display:none;height:100vw;">
							<div style="text-align:center;padding-top:20px;"><b style="color:green;font-family:Sans-serif;font-size:35px;">Write and Post Your Blog!!</b></div>
							<div style="padding-left:20px;"><form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
								<br><label for="title"><b style="font-family:Sans-serif;font-size:20px;">Your Title:</b></label><br>
								<input type="text" id="title" name="title" value="" placeholder="Give a Title of your story" style="border:3px solid black; width:500px;height:30px;"><br>
								
								<br><label for="story"><b style="font-family:Sans-serif;font-size:20px;">Your Story:</b></label><br>
								<textarea id="story" name="story" value="" placeholder="Describe Your story.." style="border:3px solid black; width:700px;height:400px;"></textarea><br>
								
								<br><input type="submit" id="submit" name="submit" value="POST" style="border:3px solid black; border-radius:3px; width:100px; height:30px; background-color:green;color:white;">
								<br><?php echo $db_status ;?>
							</form></div>
						</div>
						<div id="content" style="padding:20px;">
							<?php 
								echo $blogcontent;
							?>
						</div>
						<!-- <iframe src="#" class="iframe" frameborder="0"></iframe> -->
					</div>
				</div>
			</div>
			
			<!-- Footer -->
			<div class="footer">
				<a href="termsandconditions.php" target="_blank"><b>Terms & Conditions</b></a>
				<a href="privacypolicy.php" target="_blank"><b>Privacy Policy</b></a>
			</div>
		</div>
	</body>
</html>