<?php
include "db.php";
 
$sql = "
SELECT p.*, b.booking_date, c.full_name
FROM payments p
JOIN bookings b ON p.booking_id = b.booking_id
JOIN clients c ON b.client_id = c.client_id
ORDER BY p.payment_id DESC
";
$result = mysqli_query($conn, $sql);
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Payments</title>
<link rel="stylesheet" href="color.css">
</head>
<body>
<?php include "nav.php"; ?>
 
<h2>Payments</h2>
 
<table border="1" cellpadding="8">
  <tr>
    <th>ID</th><th>Client</th><th>Booking ID</th><th>Amount</th><th>Method</th><th>Date</th>
  </tr>
  <?php while($p = mysqli_fetch_assoc($result)) { ?>
    <tr>
      <td><?php echo $p['payment_id']; ?></td>
      <td><?php echo $p['full_name']; ?></td>
      <td><?php echo $p['booking_id']; ?></td>
      <td>₱<?php echo number_format($p['amount_paid'],2); ?></td>
      <td><?php echo $p['method']; ?></td>
      <td><?php echo $p['payment_date']; ?></td>
    </tr>
  <?php } ?>
</table>
 
</body>
</html>