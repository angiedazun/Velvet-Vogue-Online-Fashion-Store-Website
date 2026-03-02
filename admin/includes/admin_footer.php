    <!-- End admin-main -->
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php if (isset($extraJS)) echo $extraJS; ?>
<script>
// Sidebar toggle for mobile
document.addEventListener('click', function(e) {
    const sidebar = document.getElementById('adminSidebar');
    const toggle = document.querySelector('.sidebar-toggle');
    if (sidebar && toggle && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
        sidebar.classList.remove('open');
    }
});

// Auto-dismiss alerts
document.querySelectorAll('.alert').forEach(el => {
    setTimeout(() => { el.style.opacity='0'; setTimeout(()=>el.remove(),300); }, 4000);
});

// Confirm delete helper
function confirmDelete(msg) {
    return confirm(msg || 'Are you sure you want to delete this?');
}

// Toast
function showAdminToast(msg, type='success') {
    const t = document.createElement('div');
    t.style.cssText = `position:fixed;top:80px;right:20px;z-index:9999;padding:12px 20px;border-radius:12px;font-size:13px;font-weight:500;color:white;background:${type==='success'?'#27ae60':type==='error'?'#e74c3c':'#3498db'};box-shadow:0 4px 20px rgba(0,0,0,0.2);transition:all 0.3s;max-width:300px;`;
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(()=>{ t.style.opacity='0'; setTimeout(()=>t.remove(),300); }, 3000);
}
</script>
</body>
</html>
