<?php

if (isset($_SESSION['notification'])) {
    $success_notifications = [
        "Welcome back!",
        "New product created successfully.",
        "Product removed successfully"
    ];
    if (in_array($_SESSION['notification'],$success_notifications)) {
?>
        <script>
            if (window.Notyf) {
                (window.notyfInstance = window.notyfInstance || new Notyf({ duration: 3000, position: { x: 'right', y: 'top' } }));
                notyfInstance.success("<?= $_SESSION['notification'] ?>");
            }
        </script>
    <?php
    } else {
    ?>
        <script>
            if (window.Notyf) {
                (window.notyfInstance = window.notyfInstance || new Notyf({ duration: 3000, position: { x: 'right', y: 'top' } }));
                notyfInstance.error("⚠️<?= $_SESSION['notification'] ?>");
            }
        </script>
<?php
    }

    unset($_SESSION['notification']);
}
?>