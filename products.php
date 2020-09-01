<?php

/**
 * Plugin Name: Products
 * Description: A WordPress CPT for products.
 * Version: 1.1.0
 * Author: James Boynton
 */

namespace Xzito\Products;

$autoload_path = __DIR__ . '/vendor/autoload.php';

if (file_exists($autoload_path)) {
  require_once($autoload_path);
}

new Products();
