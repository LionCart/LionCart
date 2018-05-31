<?php
namespace LionShop\LionCart\Test;

require('./vendor/autoload.php');

use PHPUnit\Framework\TestCase;
use LionShop\LionCart\Cart;
use LionShop\LionCart\Product;

class CartTest extends TestCase {
  
  public function setUp() {
    $cart = Cart::initialize();
    $cart->store->truncate();

    $this->products = [];
    $prod = new Product();
    $prod->store->truncate();

    $result = $prod->create([
      'name' => 'White T-Shirt',
      'price' => 36.00
    ]);

    $this->products[] = $result;

    $result2 = $prod->create([
      'name' => 'Black T-Shirt',
      'price' => 32.00
    ]);

    $this->products[] = $result2;
  }

  public function testConstruct() {
    $cart = Cart::initialize();
    $id = $cart->getId();
    $this->assertRegExp('/[a-f0-9]{8}-?[a-f0-9]{4}-?4[a-f0-9]{3}-?[89ab][a-f0-9]{3}-?[a-f0-9]{12}/i', $id);
  }

  public function testPing() {
    $cart = Cart::initialize();
    $val = $cart->ping();
    $this->assertEquals($val, "pong", 'value should equal pong');
  }
  
  public function testAddToCart() {
    $cart = Cart::initialize();
    $prodId = $this->products[0]['id'];
    $item = [
      'id' => $prodId,
      'qty' => 1
    ];

    $cart->addToCart($item);
    $items = $cart->getItems();
    $itemCount = count($items);
    $this->assertEquals($itemCount, 1, 'should add items to cart');
    $cartItem = array_shift($items);
    $this->assertEquals($cartItem['id'], $prodId, 'Should include the added item');
  }

  public function testAddToCartExisting() {
    $cart = Cart::initialize();
    $prodId = $this->products[1]['id'];
    $item = [
      'id' => $prodId,
      'qty' => 1
    ];
    $cart->addToCart($item);

    $this->assertEquals(count($cart->getItems()), 1, 'Adds first item to cart');

    $cart->addToCart($item);
    $items = $cart->getItems();
    $this->assertEquals(count($items), 1, 'Updates qty instead of direct add');
  }

  public function testGetItems() {
    $prodId = $this->products[0]['id'];
    
    $cart = Cart::initialize();
    $items = $cart->getItems();
    $this->assertEquals($items, [], 'Should get items and should be empty');
    
    $item = [
      'id' => $prodId,
      'qty' => 3
    ];

    $cart->addToCart($item);
    $this->assertEquals(count($cart->getItems()), 1, 'Adds and item to the empty cart');
    $this->assertEquals($cart->getItems(), [ $prodId  =>  [
      'id' => $prodId,
      'description' => 'White T-Shirt',
      'qty' => 3,
      'price' => 36.00
    ] ], 'Adds the correct item to the cart items array');
  }

  public function testGetTotal() {
    $cart = Cart::initialize();
    $prodIdOne = $this->products[0]['id'];
    $prodIdTwo = $this->products[1]['id'];
    $item = [
      'id' => $prodIdOne,
      'qty' => 1
    ];
    $cart->addToCart($item);
    $items = $cart->getItems();
    $this->assertEquals(count($items), 1);

    $item2 = [
      'id' => $prodIdTwo,
      'qty' => 2
    ];

    $cart->addToCart($item2);

    $items = $cart->getItems();
    $this->assertEquals(count($items), 2);

    $total = $cart->getTotal();
    $this->assertEquals((36.00 + ( 2 * 32.00 )), $total);
  }

  public function testAddMeta() {
    $cart = Cart::initialize();
    $cart->addMetaItem('email', 'someone@gmail.com');
    $meta = $cart->getMeta();
    $this->assertEquals(count($meta), 1);

    $cart->addMetaItem('email', 'another@gmail.com');
    $meta = $cart->getMeta();
    $this->assertEquals(count($meta), 1); // Still one, updated meta item

    $cart->addMetaItem('lastAddDate', 'In the Past');

    $meta = $cart->getMeta();
    $this->assertEquals(count($meta), 2); // Now it adds another one.
    $this->assertEquals(array_keys($meta), ['email', 'lastAddDate']);
  }

  public function testExistingCart() {
    $cart = Cart::initialize();
    $cartId = $cart->getId();
    $prodId = $this->products[1]['id'];
    $prodIdTwo = $this->products[0]['id'];

    $item = [
      'id' => $prodId,
      'qty' => 1
    ];
    $cart->addToCart($item);

    $newCart = new Cart($cartId);
    $items = $newCart->getItems();
    
    $cartItem = $item;
    $cartItem['description'] = $this->products[1]['name'];
    $cartItem['price'] = $this->products[1]['price'];

    $this->assertEquals($items, [ $prodId => $cartItem ]);

    $newItem = [
      'id' => $prodIdTwo,
      'qty' => 3
    ];

    $newCart->addToCart($newItem);

    $finalCart = new Cart($cartId);

    $cartItemTwo = $newItem;
    $cartItemTwo['description'] = $this->products[0]['name'];
    $cartItemTwo['price'] = $this->products[0]['price'];


    $this->assertEquals($finalCart->getItems(), [ $prodId => $cartItem, $prodIdTwo => $cartItemTwo ]);
    $this->assertEquals($finalCart->getTotal(), (( 3 * 36.00 ) + 32.00 ));
  }
}
