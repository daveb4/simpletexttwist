<?php
$verb = $_SERVER['REQUEST_METHOD'];
$dbhandle = new PDO("sqlite:scrabble.sqlite") or die("Failed to open DB");
if (!$dbhandle) die ($error);
if ($verb == "POST"){
    $json = $_POST['myData'];
    
    $data = json_decode($json);
    
    $myrack = $data;
    $racks = [];
    for($i = 0; $i < pow(2, strlen($myrack)); $i++){
	    $ans = "";
	    for($j = 0; $j < strlen($myrack); $j++){
		    //if the jth digit of i is 1 then include letter
		    if (($i >> $j) % 2) {
		      $ans .= $myrack[$j];
		    }
	    }
	    if (strlen($ans) > 1){
  	        $racks[] = $ans;	
	    }
    }
    $racks = array_unique($racks);
    
    $newarr=[];
    foreach ($racks as $value){
        $query = "SELECT words FROM racks WHERE rack='" . strval($value) . "'";
        $statement = $dbhandle->prepare($query);
        $statement->execute();
        $res = $statement->fetchAll();
        foreach ($res as &$result){
            $wordsAt = explode("@@", $res[0]['words']);
            foreach ($wordsAt as &$word){
                array_push($newarr, $word);
            }
        }
    }
    
    
    $json = json_encode($newarr);
    header('HTTP/1.1 200 OK');
    header('Content-Type: application/json');
    echo($json);
}

?>
