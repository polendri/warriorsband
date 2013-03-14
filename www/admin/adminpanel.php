<?php

/*
 *  adminpanel.php
 *  
 *  A table of buttons and instructions for performing various administrator
 *  actions.
 *
 *  It should be obvious, but MAKE SURE ONLY ADMINISTRATORS HAVE ACCESS TO
 *  THIS PAGE!
 */

$redirect_page = "adminpanel";
require($_SERVER['DOCUMENT_ROOT'].'/auth/auth.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');

//Ensure that the user has admin exec level or above
if (!auth_access_admin_panel()) {
  print_and_exit("You do not have permission to access the admin panel.");
}
?>

<table>
  <tr><td>
    <b>Change welcome page photo</b><br/>
    <form action="/admin/adminpanel-exec.php" method="POST" enctype="multipart/form-data">
      <input type="file" name="photo" />
      <input style="width:150px" type="submit" value="Change photo" /></td>
    </form>
  </td></tr>
  <tr><td>
    <b>Delete past events</b><br/>
    <form action="/admin/adminpanel-exec.php" method="POST" enctype="multipart/form-data">
      <input type="file" name="photo" />
      <input style="width:150px" type="submit" value="Change photo" /></td>
    </form>
  </td></tr>
</table>
