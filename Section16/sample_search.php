<?php
if(isset($_GET["search"])){
	$search_query = $_GET["search"];
	$search_terms = explode(" ", $search_query);
	$conn_string = "";
	$db = new PDO($conn_string, $username, $password);
	$query = "SELECT * from MyTable";
	
	if(count($search_terms) > 0){
		$query .= "where";
		$numTerms = count($search_terms);
		for($i = 0; $i < $numTerms; $++){
			if($i > 0 && $i != $numTerms){
				$query .= " OR";
			}
			//TODO may need to adjust how %% are handled with placeholders
			// %% may need to be inlcuded with the binded value
			$query .= " name like %:term$i%";
		}
		$stmt = $db->prepare($query);
		$i = 0;
		foreach($search_terms as $term){
			$stmt->bindValue(":term$i", $term);
			$i++;
		}
		//Example resulting query "select * from MyTable where name like %one% OR name like %two% etc"
		$r = $stmt->execute();
		return $stmt->fetchAll();
	}
}
