<?php 
session_start();
if (isset($_SESSION['message'])) { 
?>
    <p style="color: green; text-align: center; margin-top: 10px;">
        <?= htmlspecialchars($_SESSION['message']); ?>
    </p>
<?php 
unset($_SESSION['message']); // Clear message after displaying
} 
?>
