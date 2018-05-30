<?php

namespace LionShop\LionCart;

use LionShop\LionCart\Store\ProductStore;

/**
 *
 */

class Product {

  protected $shopId;
  
  public function __construct($shopId) {
    $this->shopId = $shopId;  
    $this->store = new ProductStore();
  }

  public function list($query=[]) {
    $query['shop_id'] = $this->shopId;

    return $this->store->get($query);
  }

  public function create($prod) {
    if (!$prod['name']) {
      throw new \Exception('Product must have a name');
    }
    $prod['shop_id'] = $this->shopId;
    if (!isset($prod['slug'])) {
      $prod['slug'] = str_replace(' ', '-', strtolower($prod['name']));
    }

    if (!isset($prod['status'])) {
      $prod['status'] = 'draft';
    }

    if ($this->store->slugExists($this->shopId, $prod['slug'])) {
      throw new \Exception('Duplicate Slug or Product Name');
    }
    return $this->store->insert($prod);
  }

  public function update($id, $prod) {
    $query = [ 'shop_id' => $this->shopId ];
    if (strlen($id) === 24 && strspn($id,'0123456789ABCDEFabcdef') === 24) {
      $query['id'] = $id;
    } else {
      $query['slug'] = $id;
    }

    return $this->store->update($query, $prod);
  }

  public function get($id) {
    $query = [ 'shop_id' => $this->shopId ];
    if (strlen($id) === 24 && strspn($id,'0123456789ABCDEFabcdef') === 24) {
      $query['id'] = $id;
    } else {
      $query['slug'] = $id;
    }

    return $this->store->get($query);
  }

  public function delete($id) {
    return $this->store->delete([ 'shop_id' => $this->shopId, 'id' => $id ]);
  }
}
