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
            showConfirmButton: false
        });
    </script>";
    unset($_SESSION['just_unlinked']);
}

if(isset($_SESSION['extended_successfully']))
{
    echo "<script>
    Swal.fire({
        icon: 'success',
        title: '<span style=\"color:#fff; font-family:monospace\">🎉 Subscription Extended</span>',
        html: '<div style=\"font-size:14px; color:#cfc; font-family:monospace\">Subscription for user <strong style=\"color:#8cf\">" . htmlspecialchars($_SESSION['extended_user']) . "</strong> has been extended by <strong style=\"color:#ff8\">" . htmlspecialchars($_SESSION['extended_days']) . "</strong> days.<br><br>New end date: <strong style=\"color:#0ff\">" . htmlspecialchars($_SESSION['extended_enddate']) . "</strong>.</div>',
        background: '#222',
        color: '#fff',
        confirmButtonColor: '#4CAF50',
        confirmButtonText: 'OK'
    });
</script>";

    unset($_SESSION['extended_successfully']);
    unset($_SESSION['extended_user']);
    unset($_SESSION['extended_days']);
    unset($_SESSION['extended_enddate']);
}

if(isset($_SESSION['unsubscribed_successfully']))
{
    echo "<script>
    Swal.fire({
        icon: 'success',
        title: '<span style=\"color:#fff; font-family:monospace\">🙅‍♂️ Subscription Cancelled</span>',
        html: '<div style=\"font-size:14px; color:#cfc; font-family:monospace\">You have successfully cancelled the subscription for user <strong style=\"color:#8cf\">" . htmlspecialchars($_SESSION['unsubscribed_user']) . "</strong>.</div>',
        background: '#222',
        color: '#fff',
        confirmButtonColor: '#4CAF50',
        confirmButtonText: 'OK'
    });
</script>";

    unset($_SESSION['unsubscribed_successfully']);
    unset($_SESSION['unsubscribed_user']);
}
?>