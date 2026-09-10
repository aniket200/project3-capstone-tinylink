<?php

$logDir =
    getenv('LOG_DIR')
    ?: '/var/log/tinylink';

$logFile =
    $logDir . '/access.log';

$lines = [];

if (file_exists($logFile)) {

    $lines =
        array_filter(
            explode(
                PHP_EOL,
                file_get_contents($logFile)
            )
        );

    $lines =
        array_reverse($lines);
}

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>TinyLink Logs</title>

</head>

<body>

<h1>Shared Access Log</h1>

<p>
Reading:
<code>
<?= htmlspecialchars($logFile) ?>
</code>
</p>

<?php if (empty($lines)): ?>

<p>
No entries yet.
</p>

<?php else: ?>

<?php foreach ($lines as $line): ?>

<div>
<?= htmlspecialchars($line) ?>
</div>

<?php endforeach; ?>

<?php endif; ?>

<p>
<a href="index.php">
Back
</a>
</p>

</body>

</html>
