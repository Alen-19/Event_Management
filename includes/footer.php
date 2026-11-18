<?php
/**
 * Shared Public Footer Component
 * Event Management System
 */
$basePath = $basePath ?? '';
?>
    </main>
    <footer class="site-footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> LevelX Event Management. Built with PHP, MySQL, HTML, CSS & JavaScript.</p>
        </div>
    </footer>
    <script src="<?= $basePath ?>assets/js/main.js"></script>
    <?php if (isset($extraJs)): ?>
        <script src="<?= $basePath . $extraJs ?>"></script>
    <?php endif; ?>
</body>
</html>
