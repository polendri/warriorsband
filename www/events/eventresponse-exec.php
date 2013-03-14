<?php

/*
 *  eventresponse-exec.php
 *
 *  Validates and processes a user's attendance response to an event.
 */

session_start();
require_once($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/database.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');

//Ensure that the user is logged in
if (!logged_in()) {
  error_and_exit("You must be logged in to respond to an event.");
}

//Ensure that the required fields were provided
if (!isset($_POST['event_id']) || !isset($_POST['response'])) {
  error_and_exit("No event ID provided or no response provided.");
}

//Sanitize and validate inputs
$event_id = intval($_POST['event_id']);
$response = intval($_POST['response']);
if ($response < 1 || $response > 3) {
  error_and_exit("bad response value sent.");
}
if (isset($_POST['comment'])) {
  $comment = sanitize($_POST['comment']);
} else {
  $comment = "";
}
$valid_comment = FALSE;
if (strlen($comment) >= 10) {
  $valid_comment = TRUE;
}

//Ensure that if "maybe" was selected, a comment was provided
if ($response == 3 && $valid_comment == FALSE) {
  header("Location: $domain?page=event&event_id=$event_id&msg=commentrequired");
  exit();
}

//Check if the user has already responded to this event
$num_responses_row = $mysqli->query(
  "SELECT COUNT(*) " .
  "FROM `event_responses` " .
  "WHERE `event_id`='$event_id' AND `user_id`='" . $_SESSION['user_id'] . "'")->fetch_row();
handle_sql_error($mysqli);
if ($num_responses_row[0] == 0) {
  //Do an insert and update the reminder counter
  $mysqli->query(
    "INSERT INTO `event_responses` (`user_id`,`event_id`,`response`,`comment`) " .
    "VALUES ('" . $_SESSION['user_id'] . "','$event_id','$response','$comment')");
  handle_sql_error($mysqli);
  //Regenerate session id prior to setting any session variable
  //to mitigate session fixation attacks
  session_regenerate_id();
  $_SESSION['responses'] -= 1;
} else {
  //Do the update
  $mysqli->query(
    "UPDATE `event_responses` " .
    "SET `response`='$response',`comment`='$comment' " .
    "WHERE `event_id`='$event_id' AND `user_id`='" . $_SESSION['user_id'] . "'");
  handle_sql_error($mysqli);
}
header("Location: $domain?page=event&event_id=$event_id&msg=responserecorded");
exit();
