<?php

namespace LionShop\LionCart;
use Ramsey\Uuid\Uuid;
use LionShop\LionCart\Store\CartStore;

use LionShop\LionCart\Product;

/**
 *
 */
class Cart {
  protected $id;
  protected $items;
  protected $meta;
  protected $total = 0;

  public $store;
  protected $product;

  public function __construct($id) {
    $this->id = $id;
    $this->store = new CartStore();
    $this->product = new Product();
    $this->_loadItems();
    $this->_loadMeta();
    $this->_calculateTotal();
  }

  public static function initialize() {
    $id = Uuid::uuid4()->toString();
    $self = new self($id);
    return $self;
  }

  public function getId() {
    return $this->id;
  }

  protected function _loadItems() {
    $this->items = $this->store->loadItems($this->id); 
  }

  protected function _loadMeta() {
    $this->meta = $this->store->loadMeta($this->id);
  }
  
  protected function _calculateTotal() {
    $total = 0;
    if ($this->items) {
      foreach($this->items as $id => $item) {
        $total += ( $item['qty'] * $item['price'] );
      }
    }

    $this->total = $total;
  }

  public function addToCart($item) {
    $id = $item['id'];

    // Get the product for this item
    $product = $this->product->get($item['id']);
    if (!$product) {
      throw new \Exception('Invalid product id');
    }

    $item['price'] = $product['price'];
    $item['description'] = $product['name'];

    if (isset($this->items[$id])) {
      $item['qty'] = $item['qty'] + $this->items[$id]['qty'];
    }

    $this->items[$id] = $item;
    $this->_calculateTotal();
    $this->store->saveItems($this->id, $this->items, $this->total);
  }

  public function addMetaItem($key, $value) {
    $this->meta[$key] = $value;
    $this->store->saveMeta($this->id, $this->meta);
  }

  public function addMetaItems($items) {
    $this->store->addMeta($this->id, $items);
  }

  public function getItems() {
    return $this->items;
  }

  public function getTotal() {
    return $this->total;    
  }

  public function getMeta() {
    return $this->meta;
  }

  public function ping()
  {
    return 'pong';
  }
}
