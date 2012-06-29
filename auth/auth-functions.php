<?php

/*
 *  auth-functions.php
 *
 *  Contains functions used to authenticate users and make decisions based on 
 *  their credentials.
 */

//Hash a password securely
function hash_password($input)
{
  $salt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM)); 
  $hash = hash("sha256", $salt . $input); 
  $final = $salt . $hash; 
  return $final;
}

//Sanitize user input
function sanitize($data){
  $data=trim($data);
  $data=htmlspecialchars($data);
  $data=mysql_real_escape_string($data);
  return $data;
}

//Check if a user is logged in
function logged_in() {
  if (isset($_SESSION['logged_in'])) {
    return $_SESSION['logged_in'];
  } else { return FALSE; }
}

//Display a generic permissions error and exit
function deny_and_exit() {
  echo "Permission denied.";
  exit();
}

//Display a generic error message and exit
function error_and_exit() {
  echo "Error";
  exit();
}

//Functions for input authentication
function valid_password($password, $correctpassword) {
  $salt = substr($correctpassword, 0, 64);
  $correcthash = substr($correctpassword, 64, 64);
  $userhash = hash("sha256", $salt . $password);
  return ($userhash == $correcthash);
}
function valid_email($email)
{
  $isValid = true;
  $atIndex = strrpos($email, "@");
  if (is_bool($atIndex) && !$atIndex)
  {
    $isValid = false;
  }
  else
  {
    $domain = substr($email, $atIndex+1);
    $local = substr($email, 0, $atIndex);
    $localLen = strlen($local);
    $domainLen = strlen($domain);
    if ($localLen < 1 || $localLen > 64)
    {
      // local part length exceeded
      $isValid = false;
    }
    else if ($domainLen < 1 || $domainLen > 255)
    {
      // domain part length exceeded
      $isValid = false;
    }
    else if ($local[0] == '.' || $local[$localLen-1] == '.')
    {
      // local part starts or ends with '.'
      $isValid = false;
    }
    else if (preg_match('/\\.\\./', $local))
    {
      // local part has two consecutive dots
      $isValid = false;
    }
    else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
    {
      // character not valid in domain part
      $isValid = false;
    }
    else if (preg_match('/\\.\\./', $domain))
    {
      // domain part has two consecutive dots
      $isValid = false;
    }
    else if
      (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
      str_replace("\\\\","",$local)))
    {
      // character not valid in local part unless 
      // local part is quoted
      if (!preg_match('/^"(\\\\"|[^"])+"$/',
        str_replace("\\\\","",$local)))
      {
        $isValid = false;
      }
    }
    if ($isValid && !(checkdnsrr($domain,"MX") || 
      checkdnsrr($domain,"A")))
    {
      // domain not found in DNS
      $isValid = false;
    }
  }
  return $isValid;
}
function valid_date($date) {
  return preg_match( '#^(?P<year>\d{2}|\d{4})([- /.])(?P<month>\d{1,2})\2(?P<day>\d{1,2})$#', $date, $matches )
    && checkdate($matches['month'],$matches['day'],$matches['year']);
}

//Functions which compare the requester against another user
function is_same_user($user_id) {
  if (isset($_SESSION['user_id'])) {
    return ($_SESSION['user_id'] == $user_id);
  } else { return FALSE; }
}
function user_type_eq($user_type) {
  if (isset($_SESSION['user_type'])) {
    return ($_SESSION['user_type'] == $user_type);
  } else { return FALSE; }
}
function user_type_greater_eq($user_type) {
  if (isset($_SESSION['user_type'])) {
    return ($_SESSION['user_type'] >= $user_type);
  } else { return FALSE; }
}
function user_type_greater_than($user_type) {
  if (isset($_SESSION['user_type'])) {
    return ($_SESSION['user_type'] > $user_type);
  } else { return FALSE; }
}
function user_type_less_eq($user_type) {
  if (isset($_SESSION['user_type'])) {
    return ($_SESSION['user_type'] <= $user_type);
  } else { return FALSE; }
}


/*
 * Functions which determine whether the requesting user is authorized to 
 * perform an action
 */

// Registration authentication functions

function auth_register_user() {
  return user_type_greater_eq(2);
}

// Member list authentication functions

//Can the user view emails on the member list?
function auth_view_emails() {
  return user_type_greater_eq(2);
}

// Profile authentication functions

//Can the user view this profile?
function auth_view_profile($user_id, $user_type) {
  return TRUE;
}
//Can the user edit a part of this profile?
function auth_edit_profile($user_id, $user_type) {
  return (is_same_user($user_id) || user_type_greater_eq(2));
}
//Can the user view the profile e-mail address?
function auth_view_email($user_id, $user_type) {
  return (is_same_user($user_id) || user_type_greater_eq(2));
}
//Can the user edit the profile e-mail address?
function auth_edit_email($user_id, $user_type) {
  return FALSE;
}
//Can the user edit the profile password?
function auth_edit_password($user_id, $user_type) {
  return is_same_user($user_id);
}
//Can the user view the profile first name?
function auth_view_first_name($user_id, $user_type) {
  return TRUE;
}
//Can the user edit the profile first name?
function auth_edit_first_name($user_id, $user_type) {
  return (user_type_greater_eq(2) && 
    (is_same_user($user_id) || user_type_greater_than($user_type)));
}
//Can the user view the profile last name?
function auth_view_last_name($user_id, $user_type) {
  return TRUE;
}
//Can the user edit the profile last name?
function auth_edit_last_name($user_id, $user_type) {
  return (user_type_greater_eq(2) && 
    (is_same_user($user_id) || user_type_greater_than($user_type)));
}
//Can the user view the profile user type?
function auth_view_user_type($user_id, $user_type) {
  return user_type_greater_eq(2);
}
//Can the user edit the profile user type?
function auth_edit_user_type($user_id, $user_type) {
  return (user_type_greater_eq(3) &&
    (is_same_user($user_id) || user_type_greater_than($user_type)));
}
//Can the user delete this profile?
function auth_delete_account($user_id, $user_type) {
  return (!is_same_user($user_id) && user_type_greater_eq(2) &&
    user_type_greater_than($user_type));
}

// Event authentication functions

//Can the user view events?
function auth_view_events() {
  return TRUE;
}
//Can the user edit events?
function auth_edit_events() {
  return user_type_greater_eq(2);
}
//Can the user delete events?
function auth_delete_events() {
  return user_type_greater_eq(2);
}
//Can the user view the full list of event responses?
function auth_view_responses() {
  return user_type_greater_eq(2);
}
?>
