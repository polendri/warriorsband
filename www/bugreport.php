<?php
/*
 *  bugreport.php
 *
 *  A form in which a user can enter a message to be sent to the site
 *  maintainer. This is one of the few pages which is both a form AND receives
 *  a form; I usually like to keep them separate, but this is a small and
 *  likely temporary page so I didn't bother with two files.
 *
 *  TODO: Validate comment string before emailing it!!! If someone was an ass
 *        they could be emailing books worth of content to the site maintainer.
 */

require_once($_SERVER['DOCUMENT_ROOT'].'/auth/auth.php'); // Require login
require_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/display.php');
// Include Pear library's Mail.php in order to send email
set_include_path(get_include_path().'/Sites/warriorsband.com/pear'.PATH_SEPARATOR);
require_once("Mail.php");

// Sent a message if one was posted
if (isset($_POST['comment'])) {
  // Set email headers and email login info
  $from = "Warriors Band Website<" . $email_username . ">";
  $to = "<$site_maintainer_email>";
  $subject = "Warriors Band Comment / Bug Report";
  $headers = array ('From' => $from, 
    'To' => $to,
    'Subject' => $subject);
  $smtp = Mail::factory('smtp',
    array ('host' => $email_host,
    'port' => $email_port,
    'auth' => true,
    'username' => $email_username,
    'password' => $email_password));

  // Set the email body based on the comment submitted
  // TODO: validate comment string
  $body = "Name: " . $_SESSION['first_name'] . "\n\nComment:\n" . $_POST['comment'];

  // Send the email
  $mail = $smtp->send($to, $headers, $body);

  // Check the result and report success/failure appropriately
  if (!PEAR::isError($mail)) {
    header("Location: $domain?page=bugreport&msg=bugreportsuccess");
  } else {
    header("Location: $domain?page=bugreport&msg=bugreportfail");
  }

  exit();
}
?>
<h3>Post Comment / Report Bug</h3>
<div class="ctext8">
  <p>Please post a bug report here if you run into any problems with the site: things not working 
  correctly, not displaying correctly, etc. Paul will get an e-mail about it and fix it.</p>

  <p>If you've got any suggestions for site features/layout, you can also post that here. Nothing 
  about the site is final, so suggestions are welcome!</p>
</div>
<br /><br />
<div class="center">
  <form action="/bugreport.php" method="POST">
    <textarea name="comment" rows="8" cols="80" maxlength="10000"></textarea>
    <br /><br />
    <input type="submit" value="Submit" />
  </form>
</div>
