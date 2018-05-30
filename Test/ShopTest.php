<?php

namespace LionShop\LionCart\Test;

require('./vendor/autoload.php');

use PHPUnit\Framework\TestCase;
use LionShop\LionCart\Shop;
use LionShop\LionCart\Models\ShopModel;

class ShopTest extends TestCase {

  public function setUp() {
    $shp = new ShopModel();
    $shp->truncate();
  }

  public function testCreate() {
    $shop = new Shop();

    $_shop = $shop->create([
      'domain' => 'www.exampleshop.com'
    ]);

    $this->assertNotEmpty($_shop['uuid'], 'Adds a uuid to the shop');
    $this->assertEquals($_shop['domain'], 'www.exampleshop.com', 'Returns the correct data');
  }

 /**
   * @expectedException Exception
   */
  public function testDomainValidation() {
    $shop = new Shop();

    $_shop = $shop->create([
      'domain' => 'www.exampleshop.com'
    ]);

    $this->assertNotEmpty($_shop['uuid'], 'Adds a uuid to the shop');
    $this->assertEquals($_shop['domain'], 'www.exampleshop.com', 'Returns the correct data');

    $_shop = $shop->create([
      'domain' => 'www.exampleshop.com'
    ]);
  }

  public function testGetStore() {
    $shop = new Shop();

    $_shop = $shop->create([
      'domain' => 'example.com'
    ]);
    
    $this->assertNotEmpty($_shop['uuid'], 'Adds a uuid to the shop');
    $this->assertEquals($_shop['domain'], 'example.com', 'Returns the correct data');
    
    $_shop_two = $shop->get($_shop['uuid']);

    $this->assertEquals($_shop_two['domain'], 'example.com');
  }
  
  public function testUpdate() {
    $shop = new Shop();

    $_shop = $shop->create([
      'domain' => 'example.com'
    ]);
    
    $this->assertNotEmpty($_shop['uuid'], 'Adds a uuid to the shop');
    $this->assertEquals($_shop['domain'], 'example.com', 'Returns the correct data');
    
    $_shop_two = $shop->update($_shop['uuid'], [
      'domain' => 'another.com'
    ]);

    $this->assertEquals($_shop_two['domain'], 'another.com');
  }
}
