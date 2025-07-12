

<nav style="margin-bottom: 10px;">
    <button onclick="window.location.href='index.php'">Main Panel</button>
    <button onclick="window.location.href='subserver.php'">Subserver Panel</button>

    <?php if ($authenticated): ?>
    <a href="?logout=1"><button type="button">Logout</button></a>
    <?php endif; ?>
</nav>