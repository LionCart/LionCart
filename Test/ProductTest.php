<?php
namespace LionShop\LionCart\Test;

require('./vendor/autoload.php');

use PHPUnit\Framework\TestCase;
use LionShop\LionCart\Product;

class ProductTest extends TestCase {

  protected $shopId = 909339;

  public function setUp() {
    $product = new Product($this->shopId);
    $product->store->truncate();
  }

  public function testCreate() {
    $product = new Product($this->shopId);

    $_prod = $product->create([
      'name' => 'Something Great Charles',
      'price' => 99.00
    ]);

    $this->assertEquals($_prod['slug'], 'something-great-charles', 'Adds a slug field');
  }


  /**
   * @expectedException Exception
   */
  public function testDuplicateSlug() {
    $product = new Product($this->shopId);

    $product->create([
      'name' => 'Product One',
      'price' => 49.99
    ]);

    $err = false;

    $p = $product->create([
      'name' => 'Product One Again',
      'slug' => 'product-one',
      'price' => 29.99
    ]);
  }

  public function testGetBySlug() {
    $product = new Product($this->shopId);

    $result = $product->create([
      'name' => 'Product One',
      'price' => 49.99
    ]);
    
    $this->assertEquals($result['slug'], 'product-one');
    
    $p = $product->get('product-one');

    $this->assertEquals($p['status'], 'draft');
    $this->assertEquals($p['id'], $result['id'], 'Found the correct item');
  }
  
  public function testGetById() {
    $product = new Product($this->shopId);

    $result = $product->create([
      'name' => 'Product One',
      'price' => 49.99
    ]);
    
    $this->assertEquals($result['slug'], 'product-one');
    
    $p = $product->get($result['id']);

    $this->assertEquals($p['status'], 'draft');
    $this->assertEquals($p['id'], $result['id'], 'Found the correct item');
  }

  public function testList() {
    $product = new Product($this->shopId);
    
    $product->create([
      'name' => 'Product One',
      'price' => 99.99
    ]);

    $product->create([
      'name' => 'Product Two',
      'price' => 29.99
    ]);

    $result = $product->list();

    $this->assertEquals(count($result), 2);
  }

  public function testUpdateProduct() {
    $product = new Product($this->shopId);

    $result = $product->create([
      'name' => 'Product One',
      'price' => 49.99
    ]);
    
    $this->assertEquals($result['slug'], 'product-one');

    $p = $product->update($result['id'], [ 'name' => 'A Grand Product!' ]);
    $this->assertEquals($p['name'], 'A Grand Product!', 'Updates are correct');
  }

  /**
   * @expectedException Exception
   */
  public function testDeleteProduct() {
    $product = new Product($this->shopId);

    $result = $product->create([
      'name' => 'Product One',
      'price' => 49.99
    ]);
    
    $this->assertEquals($result['slug'], 'product-one');

    $r = $product->delete($result['id']);
    $this->assertEquals($r, true, 'Delete returns true');
  
    $p = $product->get($result['id']);
  }

  /**
   * @expectedException Exception
   */
  public function testShopScope() {
    $product = new Product($this->shopId);

    $result = $product->create([
      'name' => 'Product One',
      'price' => 49.99
    ]);
    
    $this->assertEquals($result['slug'], 'product-one');

    $product2 = new Product(823489234);

    $product2->get($result['slug']);
  }
}
