<body>
    <div id="particles-js"></div>
    <?php include __DIR__ . '/../../navi.php'; ?>
    <h2>Main Server Settings</h2>

    <?php
    if ($authenticated) {
        echo "authed";

        echo "
            <div style='flex: 0 0 auto;'>
                <button id=\"findUserBtn\" type=\"button\" style=\"padding: 6px 14px; width: 130px;\">Find User</button>
            </div>";
    }

    ?>
</div>
<script src="assets/javascript/particles.js"></script>
</body>