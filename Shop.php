<?php

namespace LionShop\LionCart;

use LionShop\LionCart\Models\ShopModel;

class Shop {
  public function create($shop) {
    $uuid = bin2hex(random_bytes(10));

    $exist = ShopModel::where('domain', $shop['domain'])->first();
    if ($exist) {
      throw new \Exception('Domain already exists');
    }

    $shopObj = new ShopModel();
    $shopObj->domain = $shop['domain'];
    $shopObj->uuid = substr($uuid, 0, 11);

    $shopObj->save();

    return $shopObj->toArray();
  }

  public function get($uuid) {
    $shop = ShopModel::where('uuid', $uuid)->first();
    if (!$shop) {
      throw new \Exception('Shop does not exist');
    }

    return $shop->toArray();
  }

  public function update($id, $shop) {
    if (isset($shop['uuid'])) {
      unset($shop['uuid']);
    }
    
    $shopObj = ShopModel::where('uuid', $id)->first();

    foreach($shop as $key => $value) {
      $shopObj->{$key} = $value;
    }

    $shopObj->save();

    return $shopObj->toArray();

  }  
}
