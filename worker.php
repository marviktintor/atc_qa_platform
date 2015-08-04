<?php

/* $_POST['action']="query";
$_POST['intent'] = "query_all_questions"; */

	
	include 'dbconfig/db_utils.php';
	
	if(isset($_POST['action']) && isset($_POST['intent'])){
		
		if($_POST['action'] == "insert"){
			
			
			if($_POST['intent'] == "login"){
				login();
			}
			if($_POST['intent'] == "signup"){
				signup();
			}
			if($_POST['intent'] == "post_question"){
				postquestion();
			}
			if($_POST['intent'] == "post_question_answer"){
				postquestionAnswer();
			}
			
			if($_POST['intent'] == "like_answer"){
				likeAnswer();
			}
			if($_POST['intent'] == "unlike_answer"){
				unlikeAnswer();
			}
			if($_POST['intent'] == "favorite_answer"){
				favorateAnswer();
			}
		}
		
		if($_POST['action']=="query"){
			if($_POST['intent'] == "query_all_questions"){
				queryAllQuestion();
			}
			if($_POST['intent'] == "get_question_answers"){
				queryQuestionAnswers();
			}
			
			if($_POST['intent'] == "search"){
				search();
			}
			
		}
	}else{
		
		if(!isset($_POST['action']) ){
			echo "UNKNOWN ACTION";
		}
		if(!isset($_POST['intent'])){
			echo "UNKNOWN INTENT";
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function signup(){
		$email= $_POST['email'];
		$password = $_POST['password'];
		$dbutils = new db_utils();
		
		$table = "users";
		$columns = array("email", "password");
		$records= array($email,$password);
		if($dbutils->is_exists($table, $columns, $records) == 0){
			$dbutils->insert_records($table, $columns, $records);
		}
	
		
		$users = $dbutils->query($table, $columns, $records);
		echo $userid = $users[0]['id_user'];
	}
	function login(){
		$email= $_POST['email'];
		$password = $_POST['password'];
		$dbutils = new db_utils();
		
		$table = "users";
		$columns = array("email", "password");
		$records= array($email,$password);
		
		if($dbutils->is_exists($table, $columns, $records)!=0){
			$users = $dbutils->query($table, $columns, $records);
			echo $userid = $users[0]['id_user'];
		}else{ echo "-1";}
		
	}
	
	function postquestion(){
		
		$dbutils = new db_utils();
		
		$question= $_POST['question'];
		$tags=$_POST['tags'];
		$user = $_POST['user'];
		
		$table = "questions";
		$columns = array("id_user", "question", "tags");
		$records= array($user,$question,$tags);
		
		if($dbutils->is_exists($table, $columns, $records) ==0){
			$dbutils->insert_records($table, $columns, $records);
		}
		
		
	}
	
	function queryQuestionAnswers(){
		$dbutils = new db_utils();
		$question_id = $_POST['question_id'];
		
		$table = "answers";
		$columns = array("id_question");
		$records= array($question_id);
		$answers = $dbutils->query($table, $columns, $records);
		
		if(count($answers)>0){
			
			for($i = 0;$i <count($answers);$i++){
				$answer_id =  $answers[$i]['id_answer'];
				$commit_time = $answers[$i]['commit_time'];
				$answer = $answers[$i]['answer'];
				$poster = get_question_poster($answers[$i]['id_user']);
				
				print_answer($answer_id,getHumanFriendlyTime($commit_time),$answer,get_answer_likes($answer_id),get_answer_unlikes($answer_id),get_answer_favorites($answer_id),$poster);	
			}
		}else{
			echo '<section class="card" ><h1>There no Answers for this Question</h1></section>';
		}
		
	}
	function queryAllQuestion(){
		$dbutils = new db_utils();
		$table = "questions";
		$columns = array();
		$records= array();
		
		$questions = $dbutils->query($table, $columns, $records);
		if(count($questions) == 0){
			echo '<section class="card" ><h1>There no Question</h1></section>';
		}else{
			
			for($i = 0; $i <count($questions); $i++){
				$id_question = $questions[$i]['id_question'];
				$id_user =  $questions[$i]['id_user'];
				$question =  $questions[$i]['question'];
				$tags =  $questions[$i]['tags'];
				$commit_time =  $questions[$i]['commit_time'];
				
				
				$answers = get_question_answer_count($id_question);
				$poster = get_question_poster($id_user);
				print_question($id_question,getHumanFriendlyTime($commit_time), $question, $answers, $tags, $poster);
				
			}
			
		}
	}
	
	function get_question_poster($id_user){
		$table = "users";
		$columns = array("id_user");
		$records= array($id_user);
		
		$dbutils = new db_utils();
		$users = $dbutils->query($table, $columns, $records);
		
		return $users[0]['email'];
	}
	function get_question_answer_count($id_question){
				
		$table = "answers";
		$columns = array("id_question");
		$records= array($id_question);
		
		$dbutils = new db_utils();
		
		return $dbutils->is_exists($table, $columns, $records);
	}
	
	function postquestionAnswer(){

		$dbutils = new db_utils();
		$question_id = $_POST['question_id'];
		$answer =  $_POST['answer'];
		$user  =  $_POST['user'];
		
		$table = "answers";
		$columns = array("id_question", "id_user", "answer");
		$records= array($question_id,$user,$answer);
		
		if($dbutils->is_exists($table, $columns, $records) == 0){
			$dbutils->insert_records($table, $columns, $records);
		}
		$table = "answers";
		$columns = array("id_question");
		$records= array($question_id);
		$answers = $dbutils->query($table, $columns, $records);
		
		if(count($answers)>0){
			
			for($i = 0;$i <count($answers);$i++){
				$answer_id =  $answers[$i]['id_answer'];
				$commit_time = $answers[$i]['commit_time'];
				$answer = $answers[$i]['answer'];
				$poster = get_question_poster($answers[$i]['id_user']);
				
				print_answer($answer_id,getHumanFriendlyTime($commit_time),$answer,get_answer_likes($answer_id),get_answer_unlikes($answer_id),get_answer_favorites($answer_id),$poster);	
				
			}
		}else{
			echo '<section class="card" ><h1>There no Answers for this Question</h1></section>';
		}
		
		
		
		
		
	}
	
	function get_answer_unlikes($answer_id){
				
		$dbutils = new db_utils();
		
		$table = "impressions";
		$columns = array("id_answer");
		$records= array($answer_id);
		
		$unlikes = $dbutils->query($table, $columns, $records);
		$count = 0;
		for($i = 0;$i<count($unlikes);$i++){
			$unlike = $unlikes[$i]['unlikes'];
			$count += $unlike;
		}
		return $count;
	}
	function get_answer_favorites($answer_id){
		$dbutils = new db_utils();
		
		$table = "impressions";
		$columns = array("id_answer");
		$records= array($answer_id);
		
		$favorites = $dbutils->query($table, $columns, $records);
		$count = 0;
		for($i = 0;$i<count($favorites);$i++){
			$favorite = $favorites[$i]['favorite'];
			$count += $favorite;
		}
		return $count;
	}
	function get_answer_likes($answer_id){
		$dbutils = new db_utils();
		
		$table = "impressions";
		$columns = array("id_answer");
		$records= array($answer_id);
		
		$likes = $dbutils->query($table, $columns, $records);
		$count = 0;
		for($i = 0;$i<count($likes);$i++){
			$like = $likes[$i]['likes'];
			$count += $like;
		}
		return $count;
	}
	
	function likeAnswer() {
		
		$answer_id = $_POST['answer_id'];
		$user = $_POST['user'];
		
		$dbutils = new db_utils();
		
		$table = "impressions";
		$columns = array("id_answer","id_user","likes");
		$records= array($answer_id,$user,"1");
		
		if($dbutils->is_exists($table, $columns, $records) == 0){
			$table = "impressions";
			$columns = array("id_answer","id_user","likes","unlikes","favorite");
			$records= array($answer_id,$user,"1","0","0");
			
			$dbutils->insert_records($table, $columns, $records);
		}
		
		$table = "impressions";
		$columns = array("id_answer","id_user","unlikes");
		$records= array($answer_id,$user,"1");
		
		if($dbutils->is_exists($table, $columns, $records) == 1){
			$table = "impressions";
			$columns = array("id_answer","id_user","unlikes");
			$records= array($answer_id,$user,"1");
				
			$dbutils->delete_record($table, $columns, $records);
		}
		
	}
	function unlikeAnswer() {
		$answer_id = $_POST['answer_id'];
		$user = $_POST['user'];
		
		$dbutils = new db_utils();
		
		$table = "impressions";
		$columns = array("id_answer","id_user","unlikes");
		$records= array($answer_id,$user,"1");
		
		if($dbutils->is_exists($table, $columns, $records) == 0){
			$table = "impressions";
			$columns = array("id_answer","id_user","likes","unlikes","favorite");
			$records= array($answer_id,$user,"0","1","0");
				
			$dbutils->insert_records($table, $columns, $records);
		}
		
		$table = "impressions";
		$columns = array("id_answer","id_user","likes");
		$records= array($answer_id,$user,"1");
		
		if($dbutils->is_exists($table, $columns, $records) == 1){
			$table = "impressions";
			$columns = array("id_answer","id_user","likes");
			$records= array($answer_id,$user,"1");
				
			$dbutils->delete_record($table, $columns, $records);
		}
	}
	function favorateAnswer() {
		$answer_id = $_POST['answer_id'];
		$user = $_POST['user'];
		
		$dbutils = new db_utils();
		
		$table = "impressions";
		$columns = array("id_answer","id_user","favorite");
		$records= array($answer_id,$user,"1");
		
		if($dbutils->is_exists($table, $columns, $records) == 0){
			$table = "impressions";
			$columns = array("id_answer","id_user","likes","unlikes","favorite");
			$records= array($answer_id,$user,"0","0","1");
				
			$dbutils->insert_records($table, $columns, $records);
		}else{
			$dbutils->delete_record($table, $columns, $records);
		}
		
		
	}
	
	function search(){
		$search_key = $_POST['search_key'];
		
		$dbutils = new db_utils();
		$table = "questions";
		$columns = array("question","tags");
		$records= array($search_key,$search_key);
		
		$questions = $dbutils->search($table, $columns, $records);
		if(count($questions) == 0){
			echo '<section class="card" ><h1>There are no questions with the tags or words you specified</h1></section>';
		}else{
				
			for($i = 0; $i <count($questions); $i++){
				$id_question = $questions[$i]['id_question'];
				$id_user =  $questions[$i]['id_user'];
				$question =  $questions[$i]['question'];
				$tags =  $questions[$i]['tags'];
				$commit_time =  $questions[$i]['commit_time'];
		
				$answers = get_question_answer_count($id_question);
				$poster = get_question_poster($id_user);
				print_question($id_question,getHumanFriendlyTime($commit_time), $question, $answers, $tags, $poster);
				
				
		
			}
				
		}
		
	}
	

	function print_answer($answer_id,$time,$anwser,$likes,$dislikes,$favorites,$poster){
	
		$answer_html = '<div class="card minimal-margin minimal-padding hoverable" >
			<h6 class="right" style="color:#00b8d4">'.$time.'</h6><br />
			<h6 >'.$anwser.'</h6>
			<div class=""><span class="left" style="color:#3e2723">
			<span style="color:#00b0ff">'.$likes.'<img onclick="like('.$answer_id.');" class="impressions" src="images/like.png"/></span>
			<span style="color:#e57373">'.$dislikes.'<img onclick="unlike('.$answer_id.');" class="impressions" src="images/unlike.png"/></span>
			<span style="color:#ef6c00;">'.$favorites.'<img onclick="favorite('.$answer_id.');" class="impressions" src="images/favorite.png"/></span>
			</span>
			<span style="color:#00897b"class="right">'.$poster.'</span></div>
			</div>';
		
		echo $answer_html;
	}
	
	
	function print_question($id_question,$time,$question,$answers,$tags,$poster){
	
		$answers_count = 0;
		if($answers==1){
			$answers_count = $answers." Answer";
		}
		if($answers>1){
			$answers_count = $answers." Answers";
		}
		
		$question_html = '<div onclick="loadAnswersFor('.$id_question.')" class="halign-wrapper hoverable card" style="padding:10px; margin:10px">
							<h6 ><span  class="right-align " style="color:#eeeeee">'.$answers_count.'</span>
								<span class="right " style="color:#00b8d4">'.$time.'</span></h6>
								<h5 style="color:#4acaa8;"><span style="font-size:18px;">'.$question.'</span></h5>
							
							<div class="">
							<span class="left" style="color:#4dd0e1">'.$tags.'</span>
							
							<span class="right" style="color:#00897b">'.$poster.'</span>
							
							</div></div>'; 
	
		echo $question_html;
	}
	
	function getHumanFriendlyTime($time){ return $time;}
?>