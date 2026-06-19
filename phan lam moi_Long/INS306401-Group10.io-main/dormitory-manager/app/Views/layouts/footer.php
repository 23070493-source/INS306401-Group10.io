    </main>
</div>

<script>
    window.APP_BASE_URL = <?= json_encode(BASE_URL, JSON_UNESCAPED_SLASHES) ?>;
</script>
<?php $assetVersion = $assetVersion ?? '20260619-print-validation-1'; ?>
<script src="<?= BASE_URL ?>/assets/js/validation.js?v=<?= urlencode($assetVersion) ?>"></script>
<script src="<?= BASE_URL ?>/assets/js/app.js?v=<?= urlencode($assetVersion) ?>"></script>
</body>
</html>
