    </div> <!-- đóng container mở trong header_admin -->

    <footer class="bg-secondary text-light mt-auto border-top">
        <div class="container py-3">
            <div class="row align-items-center">

                <!-- TRÁI -->
                <div class="col-md-6">
                    <strong>NavShop Admin Panel</strong><br>
                    <small>Quản lý hệ thống bán màn hình</small>
                </div>

                <!-- PHẢI -->
                <div class="col-md-6 text-md-end">
                    <small>
                        Logged in as:
                        <strong><?= htmlspecialchars($_SESSION['user']['name'] ?? 'Admin') ?></strong>
                    </small><br>
                    <small>© <?= date('Y') ?> NavShop</small>
                </div>

            </div>
        </div>
    </footer>

</body>
</html>
