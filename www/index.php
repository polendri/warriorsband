<?php

/*
 *  index.php
 *
 *  This page contains the banner at the top of the page, and the navigation
 *  sidebar. It accepts `page` via GET, and the value of `page` defines what
 *  content will be displayed.
 */

session_start(); 

// If the request URL doesn't start with "www.", redirect with "www." added.
// This is necessary because "warriorsband.com" and "www.warriorsband.com" 
// count as separate domains, and thus have separate PHP sessions. Without 
// this, users who log in on "warriorsband.com" will redirect to 
// "www.warriorsband.com" and have to log in again.
if (substr($_SERVER['HTTP_HOST'],0,4) !== 'www.') {
  header('Location: http://www.' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
  exit();
}

require_once($_SERVER['DOCUMENT_ROOT'].'/config/display.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');
require($_SERVER['DOCUMENT_ROOT'].'/auth/timeout.php');
?>

<!DOCTYPE HTML>
<html>
  <head>
    <title>UW Warriors Band</title>
    <link href="/config/style.css" rel="stylesheet" type="text/css" />
    <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon"/>
  </head>

  <body>
    <table id="header" class="contenttable">
      <tr>
        <td class="contenttd logotd">
          <a href="/"><img id="logo" class="logo" src="/images/logo.png" alt="Warriors Band" /></a>
        </td>
        <td class="contenttd headertd">
          <div id="header_text" class="center">
            <img class="logo" style="vertical-align:middle" src="/images/uw_crest.png" />
            <span class="header1">University of Waterloo</span><br />
            <span class="header2">WARRIORS BAND</span><br />
            <span class="header3">"One of the Bands in Canada"</span>
          </div>
        </td>
      </tr>
    </table>
    <hr />
    <?php print_eventreminder(); ?>
    <?php print_msg(); ?>
    <table class="contenttable">
      <tr>
        <td id="navigation" class="contenttd navigation">
          <table class="navigation">
            <tr><th class="center">Navigation</th></tr>
            <tr <?php echo row_color() ?> ><td>
              <a href="/">Home</a>
            </td></tr>
            <tr <?php echo row_color() ?> ><td>
              <a href="/?page=about">About</a>
            </td></tr>
            <tr <?php echo row_color() ?> ><td>
              <a href="/?page=history">History</a>
            </td></tr>
            <tr <?php echo row_color() ?> ><td>
              <a href="/?page=albumlist">Photos</a>
            </td></tr>
            <tr <?php echo row_color() ?> ><td>
              <a href="/?page=events">Events</a>
            </td></tr>
            <tr <?php echo row_color() ?> ><td>
              <a href="/?page=jointheband">Join the Band</a>
            </td></tr>
<?php
if (logged_in()) {
  row_color(TRUE); ?>
            <tr><th class="center">Member links</th></tr>
            <tr <?php echo row_color() ?> ><td>
              <a href="/?page=users">Member list</a>
            </td></tr>
            <tr <?php echo row_color() ?> ><td>
              <a href="/?page=profile">View/Edit your profile</a>
            </td></tr>
            <tr <?php echo row_color() ?> ><td>
              <a href="/?page=bugreport">Comments / Bug Reports</a>
            </td></tr>
            <tr <?php echo row_color() ?> ><td>
              <a href="/auth/logout.php">Logout</a>
            </td></tr>
<?php
  if (user_type_greater_eq(2)) {
    row_color(TRUE); ?>
            <tr><th class="center">Exec links</th></tr>
<?php
    if (auth_register_user()) { ?>
            <tr <?php echo row_color() ?> ><td>
              <a href="/?page=register">Register new user</a>
            </td></tr>
<?php
    } if (auth_edit_events()) { ?>
            <tr <?php echo row_color() ?> ><td>
              <a href="/?page=event">Create new event</a>
            </td></tr>
<?php
    }
  }
  if (user_type_greater_eq(3)) {
    row_color(TRUE); ?>
            <tr><th class="center">Admin links</th></tr>
<?php
    if (auth_upload_photos()) { ?>
            <tr <?php echo row_color() ?> ><td>
              <a href="/?page=uploadphotos">Upload photos</a>
            </td></tr>
<?php
    } if (auth_access_admin_panel()) { ?>
            <tr <?php echo row_color() ?> ><td>
              <a href="/?page=adminpanel">Admin panel</a>
            </td></tr>
<?php
    }
  }
} else { ?>
            <tr <?php echo row_color() ?> ><td>
              <a href="/?page=login">Login</a>
            </td></tr>
<?php
}
row_color(TRUE); ?>
          </table>
        </td>
        <td id="content" class="contenttd" valign="top">
<?php
if (isset($_GET['page'])) {
  switch ($_GET['page']) {
    case "403":
      require($_SERVER['DOCUMENT_ROOT'].'/403.php');
      break;
    case "about":
      require($_SERVER['DOCUMENT_ROOT'].'/about.php');
      break;
    case "adminpanel":
      require($_SERVER['DOCUMENT_ROOT'].'/admin/adminpanel.php');
      break;
    case "album":
      require($_SERVER['DOCUMENT_ROOT'].'/albums/album.php');
      break;
    case "albumlist":
      require($_SERVER['DOCUMENT_ROOT'].'/albums/albumlist.php');
      break;
    case "bugreport":
      require($_SERVER['DOCUMENT_ROOT'].'/bugreport.php');
      break;
    case "changepass":
      require($_SERVER['DOCUMENT_ROOT'].'/auth/changepass.php');
      break;
    case "event":
      require($_SERVER['DOCUMENT_ROOT'].'/events/event.php');
      break;
    case "eventresponses":
      require($_SERVER['DOCUMENT_ROOT'].'/events/eventresponses.php');
      break;
    case "events":
      require($_SERVER['DOCUMENT_ROOT'].'/events/events.php');
      break;
    case "history":
      require($_SERVER['DOCUMENT_ROOT'].'/history/history.php');
      break;
    case "history1":
      require($_SERVER['DOCUMENT_ROOT'].'/history/annotated.php');
      break;
    case "history2":
      require($_SERVER['DOCUMENT_ROOT'].'/history/playboy.php');
      break;
    case "history3":
      require($_SERVER['DOCUMENT_ROOT'].'/history/queen.php');
      break;
    case "history4":
      require($_SERVER['DOCUMENT_ROOT'].'/history/olympics.php');
      break;
    case "history5":
      require($_SERVER['DOCUMENT_ROOT'].'/history/halifax.php');
      break;
    case "jointheband":
      require($_SERVER['DOCUMENT_ROOT'].'/jointheband.php');
      break;
    case "login":
      require($_SERVER['DOCUMENT_ROOT'].'/auth/login.php');
      break;
    case "newevent":
      require($_SERVER['DOCUMENT_ROOT'].'/events/newevent.php');
      break;
    case "profile":
      require($_SERVER['DOCUMENT_ROOT'].'/users/profile.php');
      break;
    case "register":
      require($_SERVER['DOCUMENT_ROOT'].'/users/register.php');
      break;
    case "uploadphotos":
      require($_SERVER['DOCUMENT_ROOT'].'/albums/upload.php');
      break;
    case "users":
      require($_SERVER['DOCUMENT_ROOT'].'/users/users.php');
      break;
    default:
      require($_SERVER['DOCUMENT_ROOT'].'/404.php');
  }
} else { ?>
          <div class="center">
            Insert welcome message here!
            <br /><br />
            <span class="imageborder">
              <img src="/images/band_photo.jpg"/>
            </span>
          </div>
<?php } ?>
        </td>
      </tr>
      <tr>
        <td colspan="2" class="contenttd center">
          <br /><br />
          University of Waterloo | 200 University Ave. W. | Waterloo, Ontario, Canada | N2L 
          3G1
        </td>
      </tr>
    </table>
  </body>
</html>
