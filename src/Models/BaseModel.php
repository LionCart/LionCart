<?php

namespace LionShop\LionCart\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class BootFactory {
  public static function bootCapsule() {
    static $capsule = null;
    if ($capsule === null) {
      $capsule = new Capsule();
      $capsule->addConnection([
        'host' => $_SERVER['MYSQL_HOST'],
        'port' => $_SERVER['MYSQL_PORT'],
        'database' => $_SERVER['MYSQL_DATABASE'],
        'username' => $_SERVER['MYSQL_USERNAME'],
        'password' => $_SERVER['MYSQL_PASSWORD'],
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => true,
        'engine' => null,
        'driver' => 'mysql'
      ]);
      $capsule->setAsGlobal();
      $capsule->bootEloquent();
    }

    return $capsule;
  }
}

class BaseModel extends Model {
  public function __construct() {
    BootFactory::bootCapsule();
    parent::__construct();
  }

  public function truncate() {
    Capsule::table($this->table)->delete();
  }
}
