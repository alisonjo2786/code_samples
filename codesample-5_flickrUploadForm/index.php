<?php 

$eMore = '';
$getCleanInc = require_once('clean.php');
if (!$getCleanInc) {
	$eMore .= "Including clean.php was not successful.\n";
}

$e = "";
$eUrlStr = "";

if (is_uploaded_file($_FILES['image']['tmp_name'])) {
	$fileSizeKB = $_FILES["image"]["size"]/1024;
	$maxFileSizeKB = 2048;
	if ($fileSizeKB > $maxFileSizeKB) {
		$eUrlStr .= "&size=1";
		$e .= "Uploaded file exceeds 2MB maximum.<br />\n";
	}
}
if (strlen($eUrlStr) > 0) {
?>
<script type="text/javascript">
var url = "flickrUploadForm.html?error=true<?php echo $eUrlStr; ?>"; 
window.location = url;
</script>
<?php }

if ($e == "") {
	// read in file
	$file = fopen($_FILES['image']['tmp_name'], 'rb');
	$data = fread($file, filesize($_FILES['image']['tmp_name']));
	fclose($file);
	$data = chunk_split(base64_encode($data));
	// try to send email
	$email_suffix = "+public";
	$privacy_level = "public";
	$to = "example".$email_suffix."@photos.flickr.com";
	$title = strip_tags($_POST['title']);
	$subject = $title;
	$fname = strip_tags($_POST['fname']);
	$lname = strip_tags($_POST['lname']);
	$name = $fname." ".$lname;
	$email = $_POST['email'];
	$formname = $_POST['formname'];
	$association = fieldCleanup($_POST['association']);
	$city = fieldCleanup($_POST['city']);
	$state = $_POST['state'];
	$zip = fieldCleanup($_POST['zip']);
	$message .= "Submitted by: " . $name . ", from " . $city . ", " . $state . "\n";
	$message .= "Association: " . $association . "\n";
	$description = fieldCleanup($_POST['description']);
	$tags = $_POST['tags'];
	$subject .= " " . "tags: \"America Rally\" " . $state . " " . $tags;
	$photo_filename = $_FILES['image']['name'];
	$message .= $description . "\n";

	$headers = "From: comments@example.com\n";
	$semi_rand = md5(time());
	$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
	$headers .= "MIME-Version: 1.0\n" .
		"Content-Type: multipart/mixed;\n" .
		" boundary=\"{$mime_boundary}\"";
	$message = "This is a multi-part message in MIME format.\n\n" .
		"--{$mime_boundary}\n" .
		"Content-Type: text/plain; charset=\"iso-8859-1\"\n" .
		"Content-Transfer-Encoding: 7bit\n\n" .
		$message . "\n\n";
	$message .= "--{$mime_boundary}\n" .
		 "Content-Type: {$_FILES['image']['type']};\n" .
		 " name=\"{$_FILES['image']['name']}\"\n" .
		 "Content-Disposition: attachment;\n" .
		 " filename=\"{$_FILES['image']['name']}\"\n" .
		 "Content-Transfer-Encoding: base64\n\n" .
		 $data . "\n\n" .
		 "--{$mime_boundary}--\n"; 
	$sent = @mail($to, $subject, $message, $headers);
	// if it fails, update the error message $e
	if (!$sent) {
		$e .= "Your image could not be uploaded. Please try " .
			"again later.";
	} else {
		
		//send staff the notification email
		$notificationMsg = "Name of submitter: " . $name;
		$headers2 = "From: comments@example.com"."\n";
		$mailRecips = 'recipient1@example.com' . ', ' . 'recipient2@example.com';
		$sent2 = mail($mailRecips, 'Flickr upload: Rally', $notificationMsg, $headers2);
		if (!$sent2) {
			$eMore .= "Email notification was not successful.";
		}

		$getStoringInc = require_once('storing_data.php');
		if (!$getStoringInc) {
			$eMore .= "Including storing_data.php was not successful.\n";
		}
		$sql = '';
		if ($formname == 'rally') {
			$sql = "INSERT INTO rally (fname, lname, association, city, state, zip, email, photo_filename, photo_title) VALUES (
			'".mysqlEscape($fname)."',
			'".mysqlEscape($lname)."',
			'".mysqlEscape($association)."',
			'".mysqlEscape($city)."',
			'".mysqlEscape($state)."',
			'".mysqlEscape($zip)."',
			'".mysqlEscape($email)."',
			'".mysqlEscape($photo_filename)."',
			'".mysqlEscape($title)."'
			)";
		}
		$addRow = storeData($sql,$eMore);
		$eMore .= $addRow;
		if ($eMore != "") {
			$e .= "The photo was (probably) sent to Flickr but was not successfully added to the database.\n"."Here is some additional information on the subject:\n".$eMore;
		}
?>
		<script type="text/javascript">
		var url = "confirmationPage.html";
		window.location = url;
		</script>
<?php 
	}
}
?>

<!DOCTYPE html >
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<?php	if ($e == "") { ?>
<title>Thank You</title>
<?php } else { ?>
<title>Submission Error</title>
<?php } ?>

<style>
td {padding:1px;margin:0;}
#content_area {width:480px;}
</style>

</head>

<body>

<div id="content_area">

<?php
	if ($e == "") {
?>

<h2>Thank you for your submission (this is a temporary page)</h2>

<?php
	} else {
?>

<h2>Submission Error</h2>
<p><?php echo $e; ?></p><p style="font-weight:bold;">Please <a href="javascript:history.back();">go back and try again</a>.</p>

<?php	} ?>

</div><!--/#content_area-->

</body>
</html>
