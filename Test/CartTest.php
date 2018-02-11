<?php
namespace LionCart\Test;

require('./vendor/autoload.php');

use PHPUnit\Framework\TestCase;
use \LionCart\Cart;

class CartTest extends TestCase {

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
    $item = [
      'id' => '001-100',
      'description' => 'Shopable item',
      'qty' => 1,
      'price' => 100.00
    ];

    $cart->addToCart($item);
    $items = $cart->getItems();
    $itemCount = count($items);
    $this->assertEquals($itemCount, 1, 'should add items to cart');
    $cartItem = array_shift($items);
    $this->assertEquals($cartItem['id'], '001-100', 'Should include the added item');
  }

  public function testAddToCartExisting() {
    $cart = Cart::initialize();
    $item = [
      'id' => '845',
      'description' => 'Shopable Item',
      'qty' => 1,
      'price' => 45.00
    ];
    $cart->addToCart($item);

    $this->assertEquals(count($cart->getItems()), 1, 'Adds first item to cart');

    $item['price'] = 49.37;
    $cart->addToCart($item);
    $items = $cart->getItems();
    $this->assertEquals(count($items), 1, 'Updates qty instead of direct add');

    $cartItem = array_shift($items);
    $this->assertEquals($cartItem['price'], 49.37, 'Uses the updated price');
  }

  public function testGetItems() {
    $cart = Cart::initialize();
    $items = $cart->getItems();
    $this->assertEquals($items, [], 'Should get items and should be empty');
    
    $item = [
      'id' => '278',
      'description' => 'Something to buy',
      'qty' => 3,
      'price' => 12.75
    ];

    $cart->addToCart($item);
    $this->assertEquals(count($cart->getItems()), 1, 'Adds and item to the empty cart');
    $this->assertEquals($cart->getItems(), [ '278' =>  [
      'id' => '278',
      'description' => 'Something to buy',
      'qty' => 3,
      'price' => 12.75
    ] ], 'Adds the correct item to the cart items array');
  }

  public function testGetTotal() {
    $cart = Cart::initialize();
    $item = [
      'id' => '143',
      'description' => 'Shopable Item',
      'qty' => 1,
      'price' => 45.00
    ];
    $cart->addToCart($item);
    $items = $cart->getItems();
    $this->assertEquals(count($items), 1);

    $item2 = [
      'id' => '787',
      'description' => 'Shopable Item 2',
      'qty' => 2,
      'price' => 25.00
    ];

    $cart->addToCart($item2);

    $items = $cart->getItems();
    $this->assertEquals(count($items), 2);

    $total = $cart->getTotal();
    $this->assertEquals((45.00 + ( 2 * 25.00)), $total);
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
    $item = [
      'id' => '392349',
      'description' =>  'A New shirt',
      'qty' => 1,
      'price' => 85.00
    ];
    $cart->addToCart($item);

    $newCart = new Cart($cartId);
    $items = $newCart->getItems();
    $this->assertEquals($items, [ '392349' => $item ]);

    $newItem = [
      'id' => '234890',
      'description' => 'New Pants',
      'qty' => 3,
      'price' => 250.00
    ];

    $newCart->addToCart($newItem);

    $finalCart = new Cart($cartId);
    $this->assertEquals($finalCart->getItems(), [ '392349' => $item, '234890' => $newItem ]);
    $this->assertEquals($finalCart->getTotal(), (( 3 * 250.00 ) + 85.00 ));
  }
}
