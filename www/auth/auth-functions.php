<?php

/*
 *  auth-functions.php
 *
 *  Contains functions to sanitize inputs and functions to decide whether a
 *  user has permission to perform a given task.
 */

//Hash a password securely
//(NOTE: mcrypt wasn't available on the server at the time of writing this. If things 
//have changed and you are able to get mcrypt on the PHP install, you should just use
//the mcrypt line instead of all the file crap.)
function hash_password($input) {
  $fp = @fopen('/dev/urandom','rb');
  if ($fp === FALSE) {
    echo "can't read /dev/urandom";
    exit();
  }
  $salt = bin2hex(@fread($fp, 32));
  @fclose($fp);
  //$salt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM)); 
  $hash = hash("sha256", $salt . $input); 
  $final = $salt . $hash; 
  return $final;
}

//Sanitize user input
function sanitize($data) {
  $data=trim($data);
  $data=htmlspecialchars($data);
  $data=mysql_real_escape_string($data);
  return $data;
}

//Format inputted text (at the moment, just replaces newlines with spaces)
function format_text($text) {
  return str_replace("\\r\\n"," ",$text);
}

//Check if a user is logged in
function logged_in() {
  if (isset($_SESSION['logged_in'])) {
    return $_SESSION['logged_in'];
  } else { return FALSE; }
}

//If verbose errors is set in the config, prints the provided
//error string and exits. Otherwise, prints a generic message and exits.
function error_and_exit($err) {
  global $verbose_errors;
  if ($verbose_errors) {
    echo "Error: $err";
  } else {
    echo "Error";
  }
  exit();
}
//Print an error and exit in an html-formatted way
function print_and_exit($msg) {
  echo "<div class=\"center\">$msg</div>";
  exit();
}

//If the provious SQL query ended in error, print the error
//(if verbose_errors is set), and exit.
function handle_sql_error($mysqli) {
  if ($mysqli->errno) {
    error_and_exit("SQL error: " . $mysqli->error);
  }
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

// Can the user register a new user?
function auth_register_user() {
  return user_type_greater_eq(2);
}

// Can the user view emails on the member list?
function auth_view_emails() {
  return user_type_greater_eq(2);
}

// Can the user view photos?
function auth_view_photos() {
  return TRUE;
}

// Can the user upload photos?
function auth_upload_photos() {
  return user_type_greater_eq(3);
}

// Can the user delete photos?
function auth_delete_photos() {
  return user_type_greater_eq(2);
}

// Profile authentication functions

// Can the user view this profile?
function auth_view_profile($user_id, $user_type) {
  return TRUE;
}
// Can the user edit a part of this profile?
function auth_edit_profile($user_id, $user_type) {
  return (is_same_user($user_id) || 
    (user_type_greater_than($user_type) && user_type_greater_eq(3)));
}
// Can the user view the profile e-mail address?
function auth_view_email($user_id, $user_type) {
  return (is_same_user($user_id) || user_type_greater_eq(2));
}
// Can the user edit the profile e-mail address?
function auth_edit_email($user_id, $user_type) {
  return (user_type_greater_eq(3) &&
    (is_same_user($user_id) || user_type_greater_than($user_type)));
}
// Can the user edit the profile password?
function auth_edit_password($user_id, $user_type) {
  return is_same_user($user_id);
}
// Can the user view the profile name?
function auth_view_name($user_id, $user_type) {
  return TRUE;
}
// Can the user edit the profile first name?
function auth_edit_name($user_id, $user_type) {
  return (user_type_greater_eq(3) && 
    (is_same_user($user_id) || user_type_greater_than($user_type)));
}
// Can the user view the profile user type?
function auth_view_user_type($user_id, $user_type) {
  return user_type_greater_eq(2);
}
// Can the user edit the profile user type?
function auth_edit_user_type($user_id, $user_type) {
  return (user_type_greater_eq(3) &&
    (is_same_user($user_id) || user_type_greater_than($user_type)));
}
// Can the user view the misc. info? (program, instrument, etc)
function auth_view_misc_info($user_id, $user_type) {
  return TRUE;
}
// Can the user edit the misc. info? (program, instrument, etc)
function auth_edit_misc_info($user_id, $user_type) {
  return (is_same_user($user_id) || 
    (user_type_greater_than($user_type) && user_type_greater_eq(3)));
}
// Can the user delete this profile?
function auth_delete_account($user_id, $user_type) {
  return (user_type_greater_eq(3) || user_type_greater_than($user_id));
}
// Can the user perform a password reset on this profile?
function auth_password_reset($user_id, $user_type) {
  return (user_type_greater_eq(3) || user_type_greater_than($user_id));
}

// Event authentication functions

// Can the user view events?
function auth_view_events() {
  return TRUE;
}
// Can the user edit events?
function auth_edit_events() {
  return user_type_greater_eq(2);
}
// Can the user delete events?
function auth_delete_events() {
  return user_type_greater_eq(2);
}
// Can the user view the full list of event responses?
function auth_view_responses() {
  return user_type_greater_eq(2);
}

// Access to the admin panel
// (THERE IS REALLY NO REASON WHY THIS SHOULD EVER ALLOW NON-ADMIN USERS)
function auth_access_admin_panel() {
  return user_type_greater_eq(3);
}
?>
