<?php
require_once __DIR__ . '/../../../config/main.php';

define('USERS_PER_PAGE', 15);
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * USERS_PER_PAGE;

$conn = getDatabaseConnection();
if ($conn) {
    $result = $conn->query("
        SELECT status, subserver_id, sub_start_day, sub_end_day, faceit_id, faceit_username, password 
        FROM users 
        ORDER BY (status = 1) DESC, id ASC 
        LIMIT " . USERS_PER_PAGE . " OFFSET $offset
    ");
} else {
    echo "Database connection failed.";
    exit;
}
?>

<h2 style="margin-bottom: 15px; font-size: 20px; color: #fff;">Active Users with Subscriptions</h2>
<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>FACEIT Username</th>
            <th>Subserver ID</th>
            <th>Status</th>
            <th>Sub Start</th>
            <th>Sub End</th>
            <th>Password</th>
            <th>Extend</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['faceit_username'] ?? '—') ?></td>
                <td style="<?= empty($row['subserver_id']) ? 'color: #b9f2ff; font-weight: bold;' : '' ?>"><?= empty($row['subserver_id']) ? 'No' : htmlspecialchars($row['subserver_id']) ?></td>
                <td style="color: <?= ($row['status'] ?? null) === '1' ? '#0f0' : '#b9f2ff'; ?>; font-weight: bold;">
                    <?= ($row['status'] ?? null) === '1' ? 'Active' : 'Inactive' ?>
                </td>
                <td><?= htmlspecialchars($row['sub_start_day'] ?? '—') ?></td>
                <td><?= htmlspecialchars($row['sub_end_day'] ?? '—') ?></td>
                <td style="align-items: center; gap: 6px;">
                    <span class="password-field" style="letter-spacing: 2px;">************</span>
                    <span class="real-password" style="display: none;"><?= htmlspecialchars($row['password'] ?? '—') ?></span>
                    <button class="toggle-password" style="background: none; border: none; color: #0ff; cursor: pointer;">👁️</button>
                </td>
                <td>
                    <form method="post" action="/cp/handlers/main/extend_subscription.php" style="display: flex; gap: 6px; align-items: center;">
                        <input type="hidden" name="faceit_id" value="<?= htmlspecialchars($row['faceit_id'] ?? '') ?>">
                        <select name="days" style="background: #111; color: #0ff; border: 1px solid #0ff; border-radius: 4px; padding: 2px 6px;">
                            <option value="1">1d</option>
                            <option value="7">7d</option>
                            <option value="14">14d</option>
                            <option value="21">21d</option>
                            <option value="31">31d</option>
                        </select>
                        <button type="submit" style="background: none; border: none; color: #0f0; cursor: pointer;">➕ Extend</button>
                    </form>
                </td>
                <td>
                    <div style="display: flex; flex-direction: column; gap: 6px;">
                        <form method="post" action="/cp/handlers/main/cancel_subscription.php">
                            <input type="hidden" name="faceit_id" value="<?= htmlspecialchars($row['faceit_id'] ?? '') ?>">
                            <button type="submit" style="background-color: #e74c3c; color: #fff; border: none; border-radius: 4px; padding: 6px 12px; cursor: pointer; font-weight: bold; transition: background-color 0.2s;">
                                Unsubscribe
                            </button>
                        </form>
                        <form method="post" action="/cp/handlers/main/delete_account.php">
                            <input type="hidden" name="faceit_id" value="<?= htmlspecialchars($row['faceit_id'] ?? '') ?>">
                            <button type="submit" style="background-color: #e74c3c; color: #fff; border: none; border-radius: 4px; padding: 6px 12px; cursor: pointer; font-weight: bold; transition: background-color 0.2s;">
                                Delete Account
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php
$countResult = $conn->query("SELECT COUNT(*) as total FROM users");
$totalUsers = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalUsers / USERS_PER_PAGE);
?>

<div style="margin-top: 10px;">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>" style="margin: 0 8px; padding: 4px 10px; text-decoration: none; color: #fff; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(4px); border-radius: 6px;<?= $i == $page ? ' font-weight: bold; border: 1px solid #0ff;' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>

<script>
document.querySelectorAll('.toggle-password').forEach(button => {
    button.addEventListener('click', () => {
        const row = button.closest('td');
        const passwordField = row.querySelector('.password-field');
        const realPassword = row.querySelector('.real-password');
        if (realPassword.style.display === 'none') {
            passwordField.style.display = 'none';
            realPassword.style.display = 'inline';
            button.textContent = '🙈';
        } else {
            passwordField.style.display = 'inline';
            realPassword.style.display = 'none';
            button.textContent = '👁️';
        }
    });
});
</script>