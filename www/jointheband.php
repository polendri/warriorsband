<?php
/*
 *  jointheband.php
 *
 *  A form like bugreport.php in which a user can write a message which is
 *  emailed to the band address.
 */

require_once($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/display.php');
// Include Pear library's Mail.php in order to send email
set_include_path(get_include_path().'/Sites/warriorsband.com/pear'.PATH_SEPARATOR);
require_once("Mail.php");

if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['message'])) {
  // Sanitize and validate input
  $name=htmlspecialchars(trim($_POST["name"]));
  $email=sanitize($_POST["email"]);
  $message=htmlspecialchars(trim($_POST["message"]));
  if (strlen($name) > 64) {
    error_and_exit("Name must be at most 64 characters.");
  }
  if (!valid_email($email)) {
    header("Location: $domain?page=jointheband&msg=bademail");
    exit();
  }
  if (empty($message)) {
  }
  if (strlen($message) > 10000) {
    error_and_exit("Message must be at most 10000 characters.");
  }

  // Set email headers
  $from = "$name <$email>";
  $to = "Warriors Band <$email_username>";
  $subject = jointheband_email_subject($name);
  $headers = array ('From' => $from, 
    'To' => $to,
    'Subject' => $subject);

  // Do email authentication
  $smtp = Mail::factory('smtp',
    array ('host' => $email_host,
    'port' => $email_port,
    'auth' => true,
    'username' => $email_username,
    'password' => $email_password));

  // Set the body of the email
  $body = jointheband_email_message($name, $email, $message);

  // Send the email
  $mail = $smtp->send($to, $headers, $body);

  // Check for any errors and return the result
  if (!PEAR::isError($mail)) {
    header("Location: $domain?page=jointheband&msg=jointhebandsuccess");
  } else {
    header("Location: $domain?page=jointheband&msg=jointhebandfail");
  }
  exit();
}
?>
<h3>Join the Band / Ask a Question</h3>
<div class="ctext8">
  <p>Interested in joining the band? Want to find out more about us? Leave your name, e-mail and 
  message here and we'll get back to you with whatever it is you'd like to know.</p>

  <p>You can also just show up at a practice (Thursdays at 5:30 PM in PAC 1001) and grab/bring an 
  instrument, no registration required!</p>
</div>
<br /><br />
<table>
  <form action="/jointheband.php" method="POST">
    <tr <?php echo row_color() ?> >
      <th>Name</th>
      <td><input type="text" name="name" maxlength="64" /></td>
    </tr>
    <tr <?php echo row_color() ?> >
      <th>E-mail</th>
      <td><input type="text" name="email" maxlength="255" /></td>
    </tr>
    <tr <?php echo row_color() ?> >
      <th>Message</th>
      <td><textarea name="message" rows="8" cols="80" maxlength="10000"></textarea></td>
    </tr>
    <tr><td class="center" colspan=2><input type="submit" value="Submit" /></td></tr>
  </form>
</table>
