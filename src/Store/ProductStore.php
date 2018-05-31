<?php
namespace LionShop\LionCart\Store;

use LionShop\LionCart\Store\Base;

class ProductStore extends Base {
  public function __construct() {
    parent::__construct();
    $this->collection = $this->database->products;
  }

  public function slugExists($slug) {
    return $this->collection->count([ 'slug' => $slug ]) !== 0;
  }
}
