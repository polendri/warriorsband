<?php 

/*
 *  display.php
 *
 *  A collection of functions/strings which print messages, help format
 *  displayed content, print errors, or define the printing/displaying of data
 *  types
 */

//Print a message if one is requested
function print_msg() {
  if (isset($_GET['msg'])) {
    echo '<div class="msg">';
    switch ($_GET['msg']) {
      //Form submission error messages
      case "bademail":
        echo "Error: Invalid e-mail address.";
        break;
      case "duplicateemail":
        echo "Error: The e-mail address provided is already in use.";
        break;
      case "bademailpass":
        echo "Invalid e-mail address or password.";
        break;
      case "badpass":
        echo "Error: Invalid password.";
        break;
      case "passmismatch":
        echo "Error: The new passwords provided do not match.";
        break;
      case "passconstraints":
        echo "Error: Password must be between 6 and 64 characters long";
        break;
      case "emptyname":
        echo "Error: First and last name cannot be empty.";
        break;
      case "nonalphaname":
        echo "Error: First and last name must be letters and dashes only.";
        break;
      case "selfdowngrade":
        echo "Error: Cannot decrease your own user type. If this is really ";
        echo "your plan, delete the account and create a new one.";
        break;
      case "newuserupgrade":
        echo "Error: Cannot create a new user with equivalent or greater user type to your own.";
        break;
      case "maxlogins":
        echo "Error: Maximum number of login attempts exceeded. Try again in 30 minutes.";
        break;
      case "logoutfail":
        echo "Error: You must be logged in before you can log out.";
        break;
      case "sessiontimeout":
        echo "Your session has timed out; you'll have to log in again.";
        break;
      case "badtitle":
        echo "Error: Event titles must be between 4 and 255 characters long.";
        break;
      case "baddate":
        echo "Error: The date provided must be valid.";
        break;
      case "pastdate":
        echo "Error: Can't create an event in the past.";
        break;
      case "badtime":
        echo "Error: The time provided must be valid.";
        break;
      case "badlocation":
        echo "Error: Event locations must be at most 255 characters long.";
        break;
      case "commentrequired":
        echo "Error: If you answer \"Maybe\", you must provide a comment to elaborate (10 chars minimum).";
        break;
      case "bugreportfail":
        echo "Error: There was a problem sending the bug report.";
        break;
      case "jointhebandfail":
        echo "Error: There was a problem sending the form.";
        break;
      case "emptymessage":
        echo "Error: Message cannot be empty.";
        break;
      case "notificationemailfail":
        echo "Error: There was a problem sending the event notification e-mails.";
        break;
      case "registrationfail":
        echo "Error sending registration email.";
        break;
      case "invalidalbumnameerror":
        echo "Error: Album name must be non-empty and at most 64 characters.";
        break;
      case "invalidalbumdescriptionerror":
        echo "Error: Album description must be at most 256 characters.";
        break;
      case "fileuploaderror":
        echo "Error: An unkown error occurred during file upload.";
        break;
      case "ziperror":
        echo "Error: Photo album upload file must be a ZIP archive.";
        break;
      case "albumsizeerror":
        echo "Error: Photo album file size must be at most 20MB.";
        break;
      case "albumexistserror":
        echo "Error: A photo album with that name already exists.";
        break;
      case "unsupportedimageformaterror":
        echo "Error: Uploaded archive contains an unsupported image type.";
        break;
      //Form submission success messages
      case "profileupdatesuccess":
        echo "Profile updated successfully.";
        break;
      case "registrationsuccess":
        echo "Registration e-mail sent. New user registered successfully!";
        break;
      case "userdeletesuccess":
        echo "Account deleted successfully.";
        break;
      case "passwordresetsuccess":
        echo "User's password was reset successfully.";
        break;
      case "eventdeletesuccess":
        echo "Event deleted successfully.";
        break;
      case "logoutsuccess":
        echo "Logged out successfully.";
        break;
      case "eventupdatesuccess":
        echo "Event updated successfully.";
        break;
      case "eventcreatesuccess":
        echo "Event created successfully.";
        break;
      case "responserecorded":
        echo "Attendance response recorded successfully.";
        break;
      case "bugreportsuccess":
        echo "Comment / Bug Report sent successfully.";
        break;
      case "jointhebandsuccess":
        echo "Form sent successfully.";
        break;
      case "photouploadsuccess":
        echo "Photo album uploaded successfully.";
        break;
      case "albumdeletesuccess":
        echo "Photo album deleted successfully.";
        break;
      //Confirmation messages
      case "confirmdelete":
        echo "If you are CERTAIN this should be deleted, click the delete ";
        echo "button again and it will be PERMANENTLY deleted.";
        break;
      case "confirmpasswordreset":
        echo "If you are CERTAIN this user's password should be reset, click ";
        echo "the reset button again and it will be reset by e-mail.";
        break;
      default:
        echo "Error: Unknown msg code.";
        break;
    }
    echo '</div>';
  } else {
    echo '<br />';
  }
}
//Print the reminder for responding to events
function print_eventreminder() {
  if (isset($_SESSION['responses']) && $_SESSION['responses'] > 0) {
    echo '<div class="msg">';
    echo 'There are are ' . $_SESSION['responses'] . ' event(s) that you ';
    echo 'haven\'t responded to yet! Click ';
    echo '<a href="?page=events">here</a> to respond.';
    echo '</div>';
  }
}


//Functions for converting database codes to printable strings

//Converts user_type to a printable string
function user_type_to_str($user_type) {
  if ($user_type == 1) {
    return "Member";
  } elseif ($user_type == 2) {
    return "Exec";
  } elseif ($user_type == 3) {
    return "Admin Exec";
  } elseif ($user_type == 4) {
    return "Admin";
  } else {
    return "";
  }
}
//Convert a term code to a printable string
function term_to_str($term) {
  if ($term == 1) {
    return "1A";
  } elseif ($term == 2) {
    return "1A Co-op";
  } elseif ($term == 3) {
    return "1B";
  } elseif ($term == 4) {
    return "1B Co-op";
  } elseif ($term == 5) {
    return "1B Summer";
  } elseif ($term == 6) {
    return "2A";
  } elseif ($term == 7) {
    return "2A Co-op";
  } elseif ($term == 8) {
    return "2B";
  } elseif ($term == 9) {
    return "2B Co-op";
  } elseif ($term == 10) {
    return "2B Summer";
  } elseif ($term == 11) {
    return "3A";
  } elseif ($term == 12) {
    return "3A Co-op";
  } elseif ($term == 13) {
    return "3B";
  } elseif ($term == 14) {
    return "3B Co-op";
  } elseif ($term == 15) {
    return "3B Summer";
  } elseif ($term == 16) {
    return "4A";
  } elseif ($term == 17) {
    return "4A Co-op";
  } elseif ($term == 18) {
    return "4B";
  } elseif ($term == 19) {
    return "4B Co-op";
  } elseif ($term == 20) {
    return "4B Summer";
  } elseif ($term == 21) {
    return "Grad";
  } elseif ($term == 22) {
    return "Other";
  } else {
    return "";
  }
}
//Convert an instrument code to a printable string
function instrument_to_str($instrument) {
  if ($instrument == 1) {
    return "Piccolo";
  } elseif ($instrument == 2) {
    return "Flute";
  } elseif ($instrument == 3) {
    return "Clarinet";
  } elseif ($instrument == 4) {
    return "Alto Saxophone";
  } elseif ($instrument == 5) {
    return "Tenor Saxophone";
  } elseif ($instrument == 6) {
    return "Baritone Saxophone";
  } elseif ($instrument == 7) {
    return "French Horn";
  } elseif ($instrument == 8) {
    return "Trumpet";
  } elseif ($instrument == 9) {
    return "Baritone";
  } elseif ($instrument == 10) {
    return "Trombone";
  } elseif ($instrument == 11) {
    return "Sousaphone";
  } elseif ($instrument == 12) {
    return "Bass Drum";
  } elseif ($instrument == 13) {
    return "Snare Drum";
  } elseif ($instrument == 14) {
    return "Auxiliary Percussion";
  } elseif ($instrument == 15) {
    return "Other";
  } elseif ($instrument == 16) {
    return "Too many to list";
  } else {
    return "";
  }
}
//Convert on_campus code to a printable string
function on_campus_to_str($on_campus) {
  if ($on_campus == 0) {
    return "No - this member won't be making it to band events this term";
  } elseif ($on_campus == 1) {
    return "Yes - this member will be around for some/all band events this term!";
  } else {
    return "";
  }
}
//Convert response code to a printable string
function response_to_str($response) {
  if ($response == 1) {
    return "Yes";
  } elseif ($response == 2) {
    return "No";
  } elseif ($response == 3) {
    return "Maybe";
  } else {
    return "";
  }
}
//Converts an event status code to a printable string
function event_status_to_str($status, $long = FALSE) {
  if ($status == 1) {
    $output = "Active";
    if ($long) {
      $output .= " (open to attendance responses from members)";
    }
  } elseif ($status == 2) {
    $output = "Inactive";
    if ($long) {
      $output .= " (not open to attendance responses from members)";
    }
  } else {
    $output = "";
  }
  return $output;
}

//Functions which print the content of email messages
function email_footer() {
  return implode("\r\n", array(
    "",
    "",
    "Warriors Band",
    "",
    "",
    "",
    "This is an automated message - if you have any questions, you can reply to",
    "this e-mail and we'll be happy to get back to you."));
}

function registration_email_subject() {
  return "WarriorsBand.com Registration";
}
function registration_email_message($temp_password, $submitter_name, $submitter_comment) {
  $message = implode("\r\n", array(
    "One of your band execs, $submitter_name, has registered your e-mail address",
    "for a warriorsband.com account. To use your account:",
    "",
    "1. Visit http://www.warriorsband.com/?page=login",
    "2. Log in with your e-mail address and the following temporary password:",
    "",
    "    $temp_password",
    "",
    "3. On the page that appears, change your password (6 characters minimum)",
    "",
    "Welcome to the band!",
    "",
    "",
    ""));
  $comment = "";
  if (!empty($submitter_comment)) {
    $comment = implode("\r\n", array(
      "--------------------",
      "$submitter_name's comment:",
      "$submitter_comment",
      "---------------------",
      "",
      "",
      ""));
  }
  $extra_footer = implode("\r\n", array(
    "",
    "If you did not request this e-mail, or if it was sent to you in error, you",
    "can disregard it and this will be the only e-mail you receive from us."));
  return $message . $comment . email_footer() . $extra_footer;
}
function jointheband_email_subject($name) {
  return "\"Join the Band\" website message from $name";
}
function jointheband_email_message($name, $email, $msg) {
  return implode("\r\n", array(
    "$name ($email) has filled out the \"Join the Band\" comment",
    "form on the Warriors Band website. Here's the content of their message:",
    "",
    "--------------------",
    "$msg",
    "--------------------",
    "",
    "",
    "Warriors Band",
    "",
    "This is an automated message."));
}
function event_notification_email_subject($event) {
  return "WarriorsBand.com: New upcoming event: $event";
}
function event_notification_email_message() {
  $message = implode("\r\n", array(
    "There's a new upcoming event on WarriorsBand.com. Please log in at",
    "http://www.warriorsband.com/?page=login and let us know if you can make it!",
    "",
    "Thanks,"));
  return $message . email_footer();
}

//Outputs 'class="alt"' on even-numbered rows, so that it can be used to define the 
//class of rows and ".alt" can be given a different colour in the css style
function row_color($reset = FALSE) {
  static $row_count = 0;
  if ($reset == TRUE) {
    $row_count = 0;
    return;
  }
  if ($odd = ++$row_count % 2) return '';
  else return 'class="alt"';
}

function selected($item, $selecteditem) {
  if (isset($selecteditem) && ($item == $selecteditem)) {
    echo 'selected="selected"';
  }
}
function checked($item, $checkeditem) {
  if (isset($checkeditem) && ($item == $checkeditem)) {
    echo 'checked="checked"';
  }
}
function passwordreset_email_subject() {
  return "WarriorsBand.com Password Reset";
}
function passwordreset_email_message($temp_password, $submitter_name) {
  $message = implode("\r\n", array(
    "One of your band execs/admins, $submitter_name, has initiated a password",
    "reset for your warriorsband.com account.",
    "",
    "If you requested this, follow the directions below to log in to your",
    "account. If you did not request this, I guess you should go yell at",
    "$submitter_name.",
    "",
    "To log in with your reset password:",
    "",
    "1. Visit http://www.warriorsband.com/?page=profile",
    "2. Log in with your e-mail address and the following temporary password:",
    "",
    "    $temp_password",
    "",
    "3. On the page that appears, change your password (6 characters minimum)",
    "",
    "",
    "If you have any issues, reply to this e-mail or contact a band exec.",
    "",
    "",
    ""));
  return $message . email_footer();
}
