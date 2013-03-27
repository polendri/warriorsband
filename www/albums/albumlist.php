<?php

/*
 *  albums/albumlist.php
 *
 *  A page which lists all photo albums in a grid of "Polaroid-style" previews.
 */

require_once($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/display.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/database.php');

$no_albums = FALSE;

// Get all photo album information
$result = $mysqli->query(
  "SELECT `album_id`,`title`,`description` " .
  "FROM `photo_albums` " .
  "ORDER BY `date_uploaded`");
handle_sql_error($mysqli);
if ($result->num_rows == 0) {
  $no_albums = TRUE;
}
?>

<h1>Photo Albums</h1>
<br />
<?php
if(auth_view_photos()) {
?>
<br />
<?php
  if ($no_albums == TRUE) {
?>
<div class="center">
  There are currently no photo albums.
</div>
<?php
  } else {
?>
<ul>
<?php
    // For each photo album
    while ($album_row = $result->fetch_assoc()) {
      // Build absolute/web links to the preview image
      $path_suffix = $album_row['album_id'] . "/thumbs/0000.jpg";
      $image_preview_path = $photo_album_rel_path . "/" . $path_suffix;
      list($width, $height, $image_type) = getimagesize($photo_album_abs_path . "/" . $path_suffix);
      $album_link = $domain . '?page=album&amp;album_id=' . $album_row['album_id'];
      // Display an entry for this album
?>
  <li class="imageborderwithcaption" style="width:<?php echo $width + 2 ?>px">
    <a href="<?php echo $album_link ?>">
      <img src="<?php echo $image_preview_path ?>"/>
    </a>
    <span style="font-size: 120%; font-style:normal">
      <a href="<?php echo $album_link ?>">
        <?php echo $album_row['title'] ?>
      </a>
    </span>
    <br />
      <?php echo $album_row['description'] ?>
<?php
      // Additionally display a delete button if the user is allowed to use it
      if (auth_delete_photos()) { ?>
    <br />
    <form action="/albums/deletealbum-exec.php" method="POST">
      <input type="hidden" name="album_id" value="<?php echo $album_row['album_id'] ?>" />
<?php
        if ((isset($_GET['msg'])) && ($_GET['msg'] == "confirmdelete")) {
?>
      <input type="hidden" name="confirm" value="true" />
<?php
        }
?>
      <input style="width:100px" type="submit" value="Delete" />
    </form>
<?php
      }
?>
  </li>
<?php
    }
    $result->free();
?>
</ul>
<?php
  }
} else {
?>
<div class="center">
  You are not authorized to view photo albums.
</div>
<?php
}
?>
