<?php
namespace LionShop\LionCart\Store;

use LionShop\LionCart\Store\Base;

class ProductStore extends Base {
  public function __construct() {
    parent::__construct();
    $this->collection = $this->database->products;
  }

  public function slugExists($shopId, $slug) {
    return $this->collection->count([ 'shop_id' => $shopId, 'slug' => $slug ]) !== 0;
  }
}
