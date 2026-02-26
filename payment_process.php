<?php
include "db.php";
 
 
$booking_id = intval($_GET['booking_id']);  // Add this line

// Safe booking fetch
$stmt = $conn->prepare("SELECT * FROM bookings WHERE booking_id = ?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$booking) {
    die("Booking not found!");
}

// Safe total paid
$stmt = $conn->prepare("SELECT IFNULL(SUM(amount_paid),0) AS paid FROM payments WHERE booking_id = ?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$paidRow = $stmt->get_result()->fetch_assoc();
$total_paid = $paidRow['paid'];
$stmt->close();

$balance = $booking['total_cost'] - $total_paid;

$message = "";
 
 
if (isset($_POST['pay'])) {
  $amount = $_POST['amount_paid'];
  $method = $_POST['method'];
 
 
  if ($amount <= 0) {
    $message = "Invalid amount!";
  } else if ($amount > $balance) {
    $message = "Amount exceeds balance!";
  } else {
 
 
    // 1) Insert payment
    mysqli_query($conn, "INSERT INTO payments (booking_id, amount_paid, method)
      VALUES ($booking_id, $amount, '$method')");
 
 
    // 2) Recompute total paid (after insert)
    $paidRow2 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(amount_paid),0) AS paid FROM payments WHERE booking_id=$booking_id"));
    $total_paid2 = $paidRow2['paid'];
 
 
    // 3) Recompute new balance
    $new_balance = $booking['total_cost'] - $total_paid2;
 
 
    // 4) If fully paid, update booking status to PAID
    if ($new_balance <= 0.009) {
      mysqli_query($conn, "UPDATE bookings SET status='PAID' WHERE booking_id=$booking_id");
    }
 
 
    header("Location: bookings_list.php");
    exit;
  }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Process Payment</title>
<link rel="stylesheet" href="color.css">
</head>
<body>
<?php include "nav.php"; ?>
 
 
<h2>Process Payment (Booking #<?php echo $booking_id; ?>)</h2>
 
 
<p>Total Cost: ₱<?php echo number_format($booking['total_cost'],2); ?></p>
<p>Total Paid: ₱<?php echo number_format($total_paid,2); ?></p>
<p><b>Balance: ₱<?php echo number_format($balance,2); ?></b></p>
 
 
<p style="color:red;"><?php echo $message; ?></p>
 
 
<form method="post">
  <label>Amount Paid</label><br>
  <input type="number" name="amount_paid" step="0.01"><br><br>
 
 
  <label>Method</label><br>
  <select name="method">
    <option value="CASH">CASH</option>
    <option value="GCASH">GCASH</option>
    <option value="CARD">CARD</option>
  </select><br><br>
 
 
  <button type="submit" name="pay">Save Payment</button>
</form>
 
 
</body>
</html>