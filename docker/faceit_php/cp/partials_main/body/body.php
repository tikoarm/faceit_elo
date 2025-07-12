<body>
    <h2>Main Server Settings</h2>
    <?php if ($authenticated): ?>
        <a href="?logout=1"><button type="button">Logout</button></a>
    <?php endif; ?>


    <?php
    if ($authenticated) {
        echo "authed";
    }

    ?>
</div>
</body>