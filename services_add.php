<?php
include "db.php";
 
$message = "";
 
if (isset($_POST['save'])) {
  $service_name = $_POST['service_name'];
  $description = $_POST['description'];
  $hourly_rate = $_POST['hourly_rate'];
  $is_active = $_POST['is_active'];
 
  // simple validation
  if ($service_name == "" || $hourly_rate == "") {
    $message = "Service name and hourly rate are required!";
  } else if (!is_numeric($hourly_rate) || $hourly_rate <= 0) {
    $message = "Hourly rate must be a number greater than 0.";
  } else {
    $service_name = mysqli_real_escape_string($conn, $_POST['service_name']);
$description  = mysqli_real_escape_string($conn, $_POST['description']);
$hourly_rate  = (float)$hourly_rate; // already validated
$is_active    = (int)$is_active;     // already validated

$sql = "INSERT INTO services (service_name, description, hourly_rate, is_active)
        VALUES ('$service_name', '$description', $hourly_rate, $is_active)";

if (!mysqli_query($conn, $sql)) {
    $message = "Database error: " . mysqli_error($conn);
} else {
    header("Location: services_list.php");
    exit;
}

 
    header("Location: services_list.php");
    exit;
  }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Add Service</title>
<link rel="stylesheet" href="color.css">
</head>
<body>
<?php include "nav.php"; ?>
 
<h2>Add Service</h2>
<p style="color:red;"><?php echo $message; ?></p>
 
<form method="post">
  <label>Service Name*</label><br>
  <input type="text" name="service_name"><br><br>
 
  <label>Description</label><br>
  <textarea name="description" rows="4" cols="40"></textarea><br><br>
 
  <label>Hourly Rate (₱)*</label><br>
  <input type="text" name="hourly_rate"><br><br>
 
  <label>Active?</label><br>
  <select name="is_active">
    <option value="1">Yes</option>
    <option value="0">No</option>
  </select><br><br>
 
  <button type="submit" name="save">Save Service</button>
</form>
 
</body>
</html>