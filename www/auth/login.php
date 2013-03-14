<?php

/*
 *  login.php
 *
 *  Form which posts a login request to login-exec.php for validation.
 *  Accepts the following via GET:
 *
 *    redirect_url:  An optional string indicating the URL (relative to the
 *                   domain and starting with a /) to redirect to if login is
 *                   successful.
 */

if (isset($_GET['redirect_page'])) {
  $redirect_page = $_GET['redirect_page'];
}
?>
<h3>Login</h3>
<?php
if (!logged_in()) {
?>
<div class="center">
<?php
  if (isset($redirect_page)) {
?>
  You need to log in to access this page; please enter your e-mail address and password.
<?php
  }
  else {
?>
  Please enter your e-mail address and password.
<?php
  }
?>
  <br /><br />
</div>
<table>
  <form action="/auth/login-exec.php" method="POST">
<?php
  if (isset($redirect_page)) {
?>
    <input type="hidden" name="redirect_page" value="<?php echo $redirect_page ?>">
<?php
  }
?>
    <tr>
      <th class="side">E-mail:</th>
      <td><input type="text" name="email" tabindex="1"></td>
      <td rowspan="2"><input type="submit" value="Login" tabindex="3"></td>
    </tr>
    <tr>
      <th class="side">Password:</th>
      <td><input type="password" name="password" tabindex="2"></td>
    </tr>
  </form>
</table>
<?php
}
else {
?>
<div class="center">
  You are already logged in.
</div>
<?php
}
?>
