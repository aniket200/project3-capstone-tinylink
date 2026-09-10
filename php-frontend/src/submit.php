<?php

$apiBase =
    getenv('API_BASE_URL')
    ?: 'http://java-backend:8080';

$url =
    $_POST['url'] ?? '';

if ($url === '') {

    http_response_code(400);

    die('Missing URL');
}

$payload =
    json_encode([
        'url' => $url
    ]);

$ctx =
    stream_context_create([
        'http' => [

            'method' => 'POST',

            'header' =>
                "Content-Type: application/json\r\n",

            'content' => $payload,

            'timeout' => 5,

            'ignore_errors' => true
        ]
    ]);

$response =
    @file_get_contents(
        $apiBase . '/api/shorten',
        false,
        $ctx
    );

if ($response === false) {

    http_response_code(502);

    die(
        'Could not reach shortener API'
    );
}

$data =
    json_decode(
        $response,
        true
    );

$code =
    $data['code'] ?? null;

?>

<!DOCTYPE html>

<html>

<head>
    <meta charset="UTF-8">
    <title>TinyLink Result</title>
</head>

<body>

<h1>Your short link</h1>

<?php if ($code): ?>

<p>
    Short code:
    <strong>
        <?= htmlspecialchars($code) ?>
    </strong>
</p>

<p>

<a href="redirect.php?c=<?= urlencode($code) ?>">

    Test short link

</a>

</p>

<?php else: ?>

<p>
    Something went wrong:
    <?= htmlspecialchars($response) ?>
</p>

<?php endif; ?>

<p>
    <a href="index.php">
        Shorten another
    </a>
</p>

</body>

</html>
