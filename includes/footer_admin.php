</div><!-- /container -->
</div><!-- /main-content -->

<footer class="footer-admin">
    <div class="container">
        <div class="footer-admin-inner">
            <span><strong>NavShop Admin Panel</strong> &mdash; Quản lý hệ thống bán màn hình</span>
            <span>
                Logged in as: <strong><?= htmlspecialchars($_SESSION['user']['name'] ?? 'Admin') ?></strong>
                &nbsp;&middot;&nbsp; &copy; <?= date('Y') ?> NavShop
            </span>
        </div>
    </div>
</footer>

</body>
</html>
