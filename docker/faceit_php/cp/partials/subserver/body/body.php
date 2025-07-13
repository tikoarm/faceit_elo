<body>
    <div id="particles-js"></div>
    <?php include __DIR__ . '/../../navi.php'; ?>
    <h2>Subserver Viewer</h2>
    <form method="POST" action="" id="mainForm">
        <?php if ($authenticated): ?>
            <label for="subid">Subserver ID: </label>
            <select id="subid" name="subid">
                <?= $subserverOptions ?>
            </select>
        <?php endif; ?>

        <button type="submit" name="submit">Open</button>
    </form>


    <?php
    if ($authenticated) {
        if ($subid) 
        {
            $stmt = getDataBaseStmt();
            if ($stmt instanceof mysqli_stmt && $stmt->fetch())
            {
                require_once __DIR__ . '/health.php';
                require_once __DIR__ . '/database.php';
                require_once __DIR__ . '/tracked_users.php';
                require_once __DIR__ . '/logs.php';
            } 
            else 
            {
                echo "<p style='color:red;'><strong>❌ No subserver found with this ID.</strong></p>";
            }

            $stmt = getDataBaseStmt();
            if ($stmt instanceof mysqli_stmt) {
                $stmt->close();
                $stmt = null;
            }
            $mysqli->close();
        }
    }

    ?>
</div>
<script src="assets/javascript/particles.js"></script>
</body>