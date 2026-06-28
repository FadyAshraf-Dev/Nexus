<?php

declare(strict_types=1);

require_once dirname(__DIR__, 5) . '/bootstrap/bootstrap.php';

CSRF::verify();

$_POST['vendor_id'] = Gatekeeper::id();

$validator = ProductValidator::validate($_POST);

if ($validator->fails()) {

    Errors::set($validator->errors());
    Old::set($_POST);
    Response::redirectAdmin('add_product.php');
}

$pdo = Database::connection();

$productService = new ProductService($pdo);

$productService->createProduct(
    $validator->validated(),
    $_FILES['images']
);

Response::redirectAdmin('index.php');