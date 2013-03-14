<?php

/*
 *  albums/album.php
 *  
 *  Displays the photos in a photo album. If no photo ID is specified, this
 *  displays previews of all the photos in a grid; otherwise it displays an
 *  individual image full-size with links to previous/next images.
 */

require_once($_SERVER['DOCUMENT_ROOT'].'/auth/auth-functions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/database.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');

// Ensure that the user is allowed to view photos
if (!auth_view_photos()) {
  print_and_exit("You are not authorized to view photos.");
}

// Ensure that the album ID has been specified, get its value, and validate it
if (!isset($_GET['album_id'])) {
  error_and_exit("No album ID specified");
}
$album_id = sanitize($_GET['album_id']);
if (!is_dir($photo_album_abs_path . "/" . $album_id)) {
  error_and_exit("No photo album with that ID exists.");
}


// If a photo ID has been specified, display that image
if (isset($_GET['photo_id'])) {
  // Get the ID value and validate it
  $photo_id = sanitize($_GET['photo_id']);
  if ($photo_id < 0) {
    error_and_exit("Invalid photo ID.");
  }

  // Build the full path to the image
  $photo_path_suffix = "/$album_id/images/" . str_pad($photo_id, 4, "0", STR_PAD_LEFT) . ".jpg";
  $photo_linkpath = $photo_album_rel_path . $photo_path_suffix;
  $photo_filepath = $photo_album_abs_path . $photo_path_suffix;

  // Build links to previous/next images
  if ($photo_id > 0) {
    $prev_id = str_pad(intval($photo_id) - 1, 4, "0", STR_PAD_LEFT);
    $prev_path = "$domain?page=album&album_id=$album_id&photo_id=$prev_id";
  }
  $next_id = str_pad(intval($photo_id) + 1, 4, "0", STR_PAD_LEFT);
  if (is_file($photo_album_abs_path . "/" . $album_id . "/images/" . $next_id . ".jpg")) {
    $next_path = "$domain?page=album&album_id=$album_id&photo_id=$next_id";
  }

  // Get image dimensions for centering and putting a border around the image
  list($image_width, $image_height) = getimagesize( $photo_filepath );
?>

<table style="width:100%">
  <tr>
    <td style="width:20%;text-align:center">
<?php if (isset($prev_path)) { ?>
      <a href="<?php echo $prev_path?>">Prev</a>
<?php } ?>
    </td>
    <td style="width:60%;text-align:center">
      <a href="<?php echo "$domain?page=album&amp;album_id=$album_id"?>">Back to album</a>
    </td>
    <td style="width:20%;text-align:center">
<?php if (isset($next_path)) { ?>
      <a href="<?php echo $next_path?>">Next</a>
<?php } ?>
    </td>
  </tr>
</table>
<br/><br/>
<div class="imageborder">
  <a href="<?php echo $next_path?>">
    <img id="photo" src="<?php echo $photo_linkpath?>" width="<?php echo $image_width?>" height="<?php echo $image_height?>"/>
  </a>
</div>
<br/><br/>
<table style="width:100%">
  <tr>
    <td style="width:20%;text-align:center">
<?php if (isset($prev_path)) { ?>
      <a href="<?php echo $prev_path?>">Prev</a>
<?php } ?>
    </td>
    <td style="width:60%;text-align:center">
      <a href="<?php echo "$domain?page=album&amp;album_id=$album_id"?>">Back to album</a>
    </td>
    <td style="width:20%;text-align:center">
<?php if (isset($next_path)) { ?>
      <a href="<?php echo $next_path?>">Next</a>
<?php } ?>
    </td>
  </tr>
</table>

<?php
}
// If no photo ID was specified, display thumbnails of the whole album
else {
?>
  <div class="center">
    <a href="<?php echo "$domain?page=albumlist"?>">Back to album list</a>
  </div>
  <br/>
<?php
  $dir = opendir($photo_album_abs_path . "/" . $album_id . "/thumbs");

  // Add thumbnails for each image in the album
  echo "<ul>\n";
  while (($file = readdir($dir)) !== false) {
    if($file === "." || $file === "..") continue;
    $photo_path_suffix = "/" . $album_id . "/thumbs/" . $file;
    list($width, $height, $image_type) = getimagesize($photo_album_abs_path . $photo_path_suffix);
    $photo_linkpath = $photo_album_rel_path . $photo_path_suffix;
    echo "<li class=\"photoalbum\" style=\"width:" . $width . "px\">\n";
    echo "<a href=\"$domain?page=album&amp;album_id=$album_id&amp;photo_id=" . substr($file, 0, 4) . "\">";
    echo "<img src=\"" . $photo_linkpath . "\"/>\n";
    echo "</a></li>\n";
  }
  echo "</ul>";
  closedir($dir);
}
?>
