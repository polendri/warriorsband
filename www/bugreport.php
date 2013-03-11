<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/auth/auth.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/display.php');
set_include_path(get_include_path().'/Sites/warriorsband.com/pear'.PATH_SEPARATOR);
require_once("Mail.php");

if (isset($_POST['comment'])) {
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
  $body = "Name: " . $_SESSION['first_name'] . "\n\nComment:\n" . $_POST['comment'];

  $mail = $smtp->send($to, $headers, $body);

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
