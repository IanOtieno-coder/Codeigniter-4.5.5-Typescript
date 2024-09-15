<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'My Website' ?></title>

    <!-- Global CSS -->
    <link rel="stylesheet" href="<?= env('app.baseURL') . 'assets/css/globals/globals.css' ?>">
    <link rel="stylesheet" href="<?= env('app.baseURL') . 'assets/css/globals/tailwind.min.css' ?>">

    <!-- Page-specific CSS if any -->
    <?php if (isset($css)): ?>
        <?php foreach ($css as $cssFile): ?>
            <link rel="stylesheet" href="<?= env('app.baseURL') . $cssFile ?>">
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- JQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

</head>
<body>

    <!-- Main content -->
    <?= $this->renderSection('content') ?>

    <!-- Global JS -->
    <script src="<?= env('app.baseURL') . 'assets/js/dist/app.bundle.js?v=' . time() ?>"></script>

    <!-- Page-specific JS if any -->
    <?php if (isset($js)): ?>
        <?php foreach ($js as $jsFile): ?>
            <script type="module" src="<?= env('app.baseURL') . $jsFile . '?v=' . time() ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
