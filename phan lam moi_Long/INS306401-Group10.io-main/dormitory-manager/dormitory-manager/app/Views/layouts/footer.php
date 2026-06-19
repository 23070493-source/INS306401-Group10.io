    </main>
</div>

<script>
    window.APP_BASE_URL = <?= json_encode(BASE_URL, JSON_UNESCAPED_SLASHES) ?>;
</script>
<?php $assetVersion = $assetVersion ?? '20260619-unified-dash-1'; ?>
<script src="<?= BASE_URL ?>/assets/js/app.js?v=<?= urlencode($assetVersion) ?>"></script>
</body>
</html>
