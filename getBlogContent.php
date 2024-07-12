<?php

	$email = strval($_GET['email']);
	$title = strval($_GET['title']);
	
	$conn = mysqli_connect('localhost:3306','root','','blog_web_application');
	if (!$conn) {
	die('Could not connect: ' . mysqli_connect_error($conn));
	}
	
	$sql="SELECT Title, Author, Story FROM Blogs WHERE Email = '".$email."' and title = '".$title."';";
	$result = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_array($result))
		echo '<b style="font-family:Sans-serif;font-size:50px;">'.$row["Title"].'</b><br>
				<b style="font-family:Sans-serif;font-size:20px;">By: '.$row["Author"].'</b><br><br>
				<br><b style="font-family:Sans-serif;font-size:30px;">'.$row["Story"].'</b>';;
	
	mysqli_close($conn);

?>