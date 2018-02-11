<?php
namespace LionCart\Store;

class CartStore {
  
  protected $client;
  
  public function __construct() {
    if (!isset($_SERVER['MONGO_URL'])) {
      throw new \Exception('MONGO_URL Environmental variable must be set');
    }

    $this->collection = (new \MongoDB\Client($_SERVER['MONGO_URL']))->cartStore->carts;
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
