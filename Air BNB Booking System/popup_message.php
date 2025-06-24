<?php
//FOR POPUP MESSAGE
$msg = isset($_GET['msg']) ? $_GET['msg'] : 'No message.';
$type = isset($_GET['type']) ? $_GET['type'] : 'info';
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
$delay = 4000;
$color = '#1976d2';
if ($type === 'error') $color = '#d32f2f';
if ($type === 'success') $color = '#388e3c';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notification</title>
  <link rel="stylesheet" href="CSS/popup_message.css" />
  <style>
    :root {
      --popup-color: <?php echo $color; ?>;
    }
  </style>
  <script>
  //FOR POPUP MESSAGE SCRIPTS
    setTimeout(function() {
      window.location.href = <?php echo json_encode($redirect); ?>;
    }, <?php echo $delay; ?>);
  </script>
</head>
<body>
  <div class="popup-message">
    <div class="icon">
      <?php if ($type === 'success'): ?>
        &#10003;
      <?php elseif ($type === 'error'): ?>
        &#9888;
      <?php else: ?>
        &#8505;
      <?php endif; ?>
    </div>
    <div class="msg"><?php echo htmlspecialchars($msg); ?></div>
    <button class="redirect-btn" onclick="window.location.href='<?php echo htmlspecialchars($redirect); ?>'">OK</button>
    <div style="margin-top:10px; color:#888; font-size:0.95em;">You will be redirected shortly...</div>
  </div>
</body>
</html>
