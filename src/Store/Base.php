<?php
namespace LionShop\LionCart\Store;

use MongoDB\BSON\ObjectID;
use MongoDB\Operation\FindOneAndUpdate;

class Base {
  protected $client;
  protected $database;

  public function __construct() {
    if (!isset($_SERVER['MONGO_URL']) || !isset($_SERVER['MONGO_DB'])) {
      throw new \Exception('MONGO_URL and MONGO_DB Environmental variable must be set');
    }

    $this->client = (new \MongoDB\Client($_SERVER['MONGO_URL']));
    $this->database = $this->client->selectDatabase($_SERVER['MONGO_DB']);
  }

  public function insert($doc) {
    $doc['createdOn'] = new \DateTime();
    $result = $this->collection->insertOne($doc);
    $doc['id'] = (string) $result->getInsertedId();

    return $doc;
  }

  public function update($query, $upds) {
    if (isset($query['id'])) {
      $query['_id'] = new ObjectID($query['id']);
      unset($query['id']);
    }

    $upds['updatedOn'] = new \DateTime();

    $result = $this->collection->findOneAndUpdate($query, [ '$set' => $upds ], [ 'returnDocument' => FindOneAndUpdate::RETURN_DOCUMENT_AFTER]);
    $result['id'] = (string) $result['_id'];
    unset($result['_id']);

    return $result;
  }

  public function get($query) {
    if (isset($query['id'])) {
      $query['_id'] = new ObjectID($query['id']);
      unset($query['id']);
    }

    $resCursor = $this->collection->find($query);
    $results = $resCursor->toArray(); 
    if (count($results) === 0) {
      throw new \Exception('Object not found');
    }

    foreach($results as $obj) {
      $obj['id'] = (string) $obj['_id'];
      unset($obj['_id']);
    }

    if (count($results) === 1) {
      $results = $results[0];
    }

    return $results;
  }
  
  public function delete($q) {
    $q['_id'] = new ObjectID($q['id']);
    unset($q['id']);

    $result = $this->collection->deleteOne($q);
    return $result->getDeletedCount() === 1; 
  }

  public function truncate() {
    $result = $this->collection->deleteMany([]);
  }
}
