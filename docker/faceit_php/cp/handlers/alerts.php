<?php
if (isset($_SESSION['reloaded']) && $_SESSION['reloaded'] == 1) {
    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: 'Users successfully reloaded.'
        });
    </script>";
    if($_SESSION['reloaded'] == 1) $_SESSION['reloaded']++;
    else unset($_SESSION['reloaded']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unlink_faceit_id'])) {
    require_once dirname(__DIR__, 1) . '/config/database.php';
    $unlink_id = $_POST['unlink_faceit_id'];
    unlinkFaceitUser($unlink_id);
    $_SESSION['just_unlinked'] = true;
    header("Location: ?subid={$subid}");
    exit;
}

if (isset($_SESSION['just_unlinked'])) {
    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'User unlinked successfully',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000
        });
    </script>";
    unset($_SESSION['just_unlinked']);
}
?>