<?php
$pageTitle = "Messages";
require_once '../config/db.php';
if (!isAdmin()) { redirect('../login.php'); }

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cid = (int)($_POST['contact_id'] ?? 0);
    $act = $_POST['act'] ?? '';
    if ($act === 'mark_read') {
        $conn->query("UPDATE contacts SET is_read=1 WHERE id=$cid");
        $message = 'Message marked as read.';
    } elseif ($act === 'delete') {
        $conn->query("DELETE FROM contacts WHERE id=$cid");
        $message = 'Message deleted.';
    } elseif ($act === 'mark_all_read') {
        $conn->query("UPDATE contacts SET is_read=1");
        $message = 'All messages marked as read.';
    }
}

$filter = $_GET['filter'] ?? '';
$where  = $filter === 'unread' ? 'WHERE is_read=0' : '';
$contacts = $conn->query("SELECT * FROM contacts $where ORDER BY created_at DESC");

$extraCSS = '<link rel="stylesheet" href="../admin/css/contacts.css">';
$extraJS  = '<script src="../admin/js/contacts.js"></script>';
include 'includes/admin_header.php';
?>
<div class="admin-content">
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Contact Messages</h1>
            <p class="admin-page-subtitle">Customer inquiries and messages</p>
        </div>
        <form method="POST">
            <input type="hidden" name="act" value="mark_all_read"><input type="hidden" name="contact_id" value="0">
            <button type="submit" class="btn-vv btn-outline-primary-vv" style="font-size:13px;padding:10px 18px;"><i class="fas fa-check-double me-1"></i> Mark All Read</button>
        </form>
    </div>

    <?php if ($message): ?><div class="alert-vv alert-success mb-4"><i class="fas fa-check-circle"></i> <?= $message ?></div><?php endif; ?>

    <!-- Filter -->
    <div style="display:flex;gap:8px;margin-bottom:20px;">
        <a href="contacts.php" style="padding:8px 16px;border-radius:20px;font-size:12px;font-weight:600;text-decoration:none;background:<?= !$filter?'var(--primary)':'white' ?>;color:<?= !$filter?'white':'var(--text-muted)' ?>;border:1px solid <?= !$filter?'var(--primary)':'var(--border)' ?>;">All Messages</a>
        <a href="?filter=unread" style="padding:8px 16px;border-radius:20px;font-size:12px;font-weight:600;text-decoration:none;background:<?= $filter=='unread'?'var(--primary)':'white' ?>;color:<?= $filter=='unread'?'white':'var(--text-muted)' ?>;border:1px solid <?= $filter=='unread'?'var(--primary)':'var(--border)' ?>;">Unread</a>
    </div>

    <div class="admin-card">
        <div class="table-responsive">
            <table class="admin-table">
                <thead><tr><th>From</th><th>Subject</th><th>Message</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                <?php if ($contacts) while ($c = $contacts->fetch_assoc()): ?>
                <tr style="<?= !$c['is_read'] ? 'background:#fffbf0;' : '' ?>">
                    <td>
                        <?php if (!$c['is_read']): ?><div style="width:8px;height:8px;border-radius:50%;background:var(--primary);display:inline-block;margin-right:6px;vertical-align:middle;"></div><?php endif; ?>
                        <div style="font-weight:600;font-size:13px;"><?= htmlspecialchars($c['name']) ?></div>
                        <div style="font-size:11px;color:var(--text-muted);"><?= htmlspecialchars($c['email']) ?></div>
                        <?php if ($c['phone']): ?><div style="font-size:11px;color:var(--text-muted);"><?= htmlspecialchars($c['phone']) ?></div><?php endif; ?>
                    </td>
                    <td style="font-size:13px;font-weight:500;"><?= htmlspecialchars($c['subject'] ?: '(No subject)') ?></td>
                    <td style="max-width:300px;">
                        <div style="font-size:12px;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:280px;" title="<?= htmlspecialchars($c['message']) ?>">
                            <?= htmlspecialchars($c['message']) ?>
                        </div>
                        <a href="#" onclick="document.getElementById('msg-<?= $c['id'] ?>').classList.toggle('d-none');return false;" style="font-size:11px;color:var(--primary);">Show full</a>
                        <div id="msg-<?= $c['id'] ?>" class="d-none" style="font-size:12px;color:var(--dark);margin-top:8px;padding:10px;background:var(--light);border-radius:8px;white-space:pre-wrap;"><?= htmlspecialchars($c['message']) ?></div>
                    </td>
                    <td style="font-size:12px;color:var(--text-muted);white-space:nowrap;"><?= date('d M Y\nH:i', strtotime($c['created_at'])) ?></td>
                    <td><span class="status-badge" style="background:<?= $c['is_read']?'#27ae6020':'#f39c1220' ?>;color:<?= $c['is_read']?'#27ae60':'#f39c12' ?>;"><?= $c['is_read']?'Read':'Unread' ?></span></td>
                    <td>
                        <div style="display:flex;gap:8px;">
                            <?php if (!$c['is_read']): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="contact_id" value="<?= $c['id'] ?>"><input type="hidden" name="act" value="mark_read">
                                <button type="submit" style="background:none;border:none;padding:0;cursor:pointer;color:#27ae60;font-size:13px;" title="Mark Read"><i class="fas fa-check"></i></button>
                            </form>
                            <?php endif; ?>
                            <a href="mailto:<?= htmlspecialchars($c['email']) ?>?subject=Re: <?= urlencode($c['subject']) ?>" style="color:var(--primary);font-size:13px;" title="Reply"><i class="fas fa-reply"></i></a>
                            <form method="POST" style="display:inline;" onsubmit="return confirmDelete('Delete this message?')">
                                <input type="hidden" name="contact_id" value="<?= $c['id'] ?>"><input type="hidden" name="act" value="delete">
                                <button type="submit" style="background:none;border:none;padding:0;cursor:pointer;color:#e74c3c;font-size:13px;" title="Delete"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'includes/admin_footer.php'; ?>
