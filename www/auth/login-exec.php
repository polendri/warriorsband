<?php

/*
 *  login-exec.php
 *
 *  Receives a login form and attempts login validation.
 *  If successful, redirects to the requested URL, or to the homepage if none is provided. 
 *  If not successful, redirects back to the login form.
 */

session_start();

//require user configuration and database connection parameters
require_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/database.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');

//Redirect back to the login page with the appropriate redirect and error codes
function redirect_and_exit($error) {
  global $redirect_page, $domain;
  if (isset($redirect_page)) {
    $redirect_url = "$domain?page=login&redirect_page=$redirect_page";
    if (isset($error)) {
      $redirect_url = $redirect_url . "&msg=$error";
    }
  } else {
    $redirect_url = "$domain?page=login";
    if (isset($error)) {
      $redirect_url = $redirect_url . "&msg=$error";
    }
  }
  header("Location: $redirect_url");
  exit();
}

//Set the redirect page if one was provided
if (isset($_POST['redirect_page'])) {
  $redirect_page = sanitize($_POST['redirect_page']);
}

//Set default to not validated
if (!isset($_SESSION['logged_in'])) {
  $_SESSION['logged_in'] = FALSE;
}

//Exit if the user is not already logged in
if ($_SESSION['logged_in'] == TRUE) {
  error_and_exit("Already logged in");
}
//Exit if the an email or password was not provided
if (!isset($_POST["password"]) || !isset($_POST["email"])) {
  error_and_exit("Bad data (email or password missing)");
}

//Sanitize the submitted email and password
$email=sanitize($_POST["email"]);
$pass=sanitize($_POST["password"]);

//Get user info for the provided email address
$user_row = $mysqli->query(
  "SELECT *" .
  "FROM `users` " .
  "WHERE `email`='$email'"
  )->fetch_assoc();
handle_sql_error($mysqli);

//Make sure a user exists with this e-mail
if (!$user_row) {
  redirect_and_exit("bademailpass");
}

//Check if the user has attempted too many logins too recently
$last_login_attempt = strtotime($user_row['last_login_attempt']);
if ($user_row['login_attempts'] >= $max_login_attempts
  && (time() - $last_login_attempt) < $login_cooldown) {
  redirect_and_exit("maxlogins");
}

//Update last login attempt time, and number of login attempts
$mysqli->query(
  "UPDATE `users` " .
  "SET `last_login_attempt`=NOW(), `login_attempts`=`login_attempts`+1 " .
  "WHERE `email`='$email'");
handle_sql_error($mysqli);

//Make sure the password is valid
if (!valid_password($pass, $user_row['password'])) {
  redirect_and_exit("bademailpass");
}

//Update last login time and login attempts
$mysqli->query(
  "UPDATE `users` " .
  "SET `last_login`=NOW(), `login_attempts`=0 " .
  "WHERE `email`='$email'");
handle_sql_error($mysqli);

//Get the number of events which can be responded to
$num_events_row = $mysqli->query(
  "SELECT COUNT(*) " .
  "FROM `events` " .
  "WHERE `status`='1' AND `date`>=NOW()"
  )->fetch_row();
handle_sql_error($mysqli);
//Get the number of these events the user has already responded to
$num_responses_row = $mysqli->query(
  "SELECT COUNT(*) " .
  "FROM `events` " .
  "INNER JOIN `event_responses` ON `events`.`event_id`=`event_responses`.`event_id` " .
  "WHERE `status`='1' AND `date`>=NOW() AND `user_id`='" . $user_row['user_id'] . "'"
  )->fetch_row();
handle_sql_error($mysqli);

//The number of responses left to make is the difference
$responses = $num_events_row[0] - $num_responses_row[0];

//Regenerate session id prior to setting any session variable
//to mitigate session fixation attacks
session_regenerate_id();

//Set various session variables associated with a successful login
$_SESSION['logged_in'] = TRUE;
$_SESSION['LAST_ACTIVITY'] = time();
$_SESSION['user_id'] = intval($user_row['user_id']);
$_SESSION['user_type'] = intval($user_row['user_type']);
$_SESSION['first_name'] = $user_row['first_name'];
$_SESSION['responses'] = intval($responses);

//The user has logged in successfully. Redirect.
//If the user has an "needs password change" status,
//redirect to the change password page.
if ($user_row['status'] == 3) {
  $redirect_url = "$domain?page=changepass";
} elseif (isset($_POST['redirect_page'])) {
  $redirect_url = "$domain?page=" . htmlspecialchars($redirect_page);
} else {
  $redirect_url = $domain;
}
header("Location: $redirect_url");
exit();
?>
