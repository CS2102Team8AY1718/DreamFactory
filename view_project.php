<!DOCTYPE html>
<html>

<body>

<?php
session_start();
require 'connect.php';

    if(isset($_GET["project_id"]))
    {
        $project_id = $_GET["project_id"];
        $sql= "Select u.full_name, p.title, p.image_url, p.description, p.end_datetime, p.category, p.funding_goal, SUM(f.amount) as total from projects p natural join fundings f natural join users u where project_id='$project_id'";
		//check sql for error
		if ($result = $conn->query($sql)){
			//Check result is not null and store vars
			if ($result->num_rows != 0){
				$row = $result->fetch_assoc();
				$full_name=$row['full_name'];
				$title=$row['title'];
				$image_url=$row['image_url'];
				echo($image_url);
				$description=$row['description'];
				$end_datetime=$row['end_datetime'];
				$category=$row['category'];
				$funding_goal=$row['funding_goal'];
				$total=$row['total'];
				
			}else{
				echo "project does not exist";
			}
		}else{
			echo "An sql error has occurred";
		}
    }
	
	
	echo ("title:"); echo($title);
	echo '<br>';
	echo ('<img src="'.$image_url.'">');
	echo '<br>';
	echo ("owner:"); echo($full_name);
	echo '<br>';
	echo ("description:"); echo($description);
	echo '<br>';
	echo ("category:"); echo($category);
	echo '<br>';
	echo ("Current Amount:"); echo($total);
	echo '<br>';
	echo ("Funding Goal:"); echo($funding_goal);
	echo '<br>';
	echo ("end date:"); echo($end_datetime);
	
	
?>

<img 

</body>
</html>