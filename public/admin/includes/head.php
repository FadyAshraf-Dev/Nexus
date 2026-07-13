<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="csrf-token" content="<?= Html::escape(CSRF::token()); ?>">
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title><?= $pageTitle ?? 'Nexus'; ?></title>
    <link href="<?= Asset::admin('css/styles.css') ?>" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />

    <link rel="icon" type="image/x-icon" href="<?= Asset::admin('assets/img/favicon.png') ?>" />
    <script data-search-pseudo-elements defer
        src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.js"
        crossorigin="anonymous"></script>
    <script src="
https://cdn.jsdelivr.net/npm/sortablejs@1.15.7/Sortable.min.js
"></script>
</head>