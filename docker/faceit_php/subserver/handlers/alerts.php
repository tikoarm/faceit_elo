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
?>