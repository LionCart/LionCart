<?php
namespace LionShop\LionCart\Store;

use LionShop\LionCart\Store\Base;

class CartStore extends Base {
  
  public function __construct() {
    parent::__construct();
    $this->collection = $this->database->carts;
  }

  public function loadItems($id) {
    $cart = $this->collection->findOne(['cart_id' => $id ], [ 'typeMap' => ['root' => 'array', 'document' => 'array'] ]);
    if (!$cart) {
      return [];
    }
    return (isset($cart['items'])) ? $cart['items'] : [];
  }

  public function loadMeta($id) {
    $cart = $this->collection->findOne(['cart_id' => $id ], [ 'typeMap' => ['root' => 'array', 'document' => 'array'] ]);
    if (!$cart) {
      return [];
    }
    return (isset($cart['meta'])) ? $cart['meta'] : [];
  }

  public function saveItems($id, $items, $total=null) {
    $update = [
      '$set' => ['items' => $items ]
    ];

    if($total) {
      $update['$set']['total'] = $total;
    }

    $result = $this->collection->updateOne(
      ['cart_id' => $id ],
      $update,
      ['upsert' => true ]
    );
  }

  public function saveMeta($id, $items) {
    $update = [
      '$set' => [
        'meta' => $items
      ]
    ];

    $result = $this->collection->updateOne(
      [ 'cart_id' => $id ],
      $update,
      [ 'upsert' => true ]
    );
  }
}
