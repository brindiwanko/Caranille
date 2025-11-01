<?php require_once(__DIR__ . '/templates/header.php'); ?>

<h1>News</h1>

<?php if (!empty($news)): ?>
    <?php foreach ($news as $newsItem): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($newsItem['newsTitle']); ?></h5>
                <h6 class="card-subtitle mb-2 text-muted">
                    Posted on <?php echo (new DateTime($newsItem['newsDate']))->format('d-m-Y'); ?> by <?php echo htmlspecialchars($newsItem['newsAccountPseudo']); ?>
                </h6>
                <p class="card-text"><?php echo nl2br(htmlspecialchars($newsItem['newsMessage'])); ?></p>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No news available.</p>
<?php endif; ?>

<?php require_once(__DIR__ . '/templates/footer.php'); ?>
