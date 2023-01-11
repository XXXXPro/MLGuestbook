<?php

namespace MLFW\Models;

use Exception;
use MLFW\Models\Entity as ModelsEntity;

use function \MLFW\app, \MLFW\_dbg;

class Entity {
  const FIELDS = ['id','enabled','type','owner','title','url','descr','seo_title','seo_descr','seo_keywords','rating_pro','rating_contra']; // No text and extra fields, they should be added manually if needed!

  public $id=null;
  public $enabled=true;
  public $type;
  public $owner=null;
  public $title;
  public $url=null;  
  public $descr;
  public $text;
  public $seo_title;
  public $seo_descr;
  public $seo_keywords;
  public $rating_pro=0;
  public $rating_contra=0;
  public $extra=null;

  function __construct() {
    if (is_string($this->extra)) $this->extra = json_decode($this->extra,false);
  }

  static function getById(int $id) {
    $sql = 'SELECT e.* FROM entity e WHERE id=:id';
    $stmt=app()->db->prepare($sql);
    $stmt->execute(['id'=>$id]);
    $stmt->setFetchMode(\PDO::FETCH_CLASS, '\\MLFW\\Models\\Entity');
    $result = $stmt->fetch(\PDO::FETCH_CLASS);
    return $result;
  }

  static function getByUrl(string $url) {
    $sql = 'SELECT e.* FROM entity e WHERE url=:url';
    $stmt=app()->db->prepare($sql);
    $stmt->execute(['url'=>$url]);
    $stmt->setFetchMode(\PDO::FETCH_CLASS, '\\MLFW\\Models\\Entity');
    $result = $stmt->fetch(\PDO::FETCH_CLASS);
    return $result;
  }

  static function getClass(string $url) {
    $sql = 'SELECT et.classname FROM entity e, entity_type et WHERE url=:url AND e.type=et.id';
    $stmt=app()->db->prepare($sql);
    $stmt->execute(['url'=>$url]);
    $classname = $stmt->fetchColumn();
    return $classname;
  }

  static function getRelated($id, int $rel_type,bool $texts=false,bool $extra=false) {
    $id=self::extractId($id);
    $columns = ''.join(',',self::FIELDS).'';
    if ($texts) $columns.=',texts';
    if ($extra) $columns.=',extra';
    $sql = 'SELECT '.$columns.' FROM entity e, entity_relation er WHERE er.rel_from=:id AND e.id=er.rel_to AND rel_type=:type';
    $stmt=app()->db->prepare($sql);
    $stmt->execute(['id'=>$id,'type'=>$rel_type]);
    return $stmt->fetchAll(\PDO::FETCH_CLASS, '\\MLFW\\Models\\Entity');
  }

  static function getRelatedReverse(int $id, int $rel_type,bool $texts=false,bool $extra=false) {
    $id=self::extractId($id);
    $columns = ''.join(',',self::FIELDS).'';
    if ($texts) $columns.=',texts';
    if ($extra) $columns.=',extra';
    $sql = 'SELECT '.$columns.' FROM entity e, entity_relation er WHERE er.rel_to=:id AND e.id=er.rel_from AND rel_type=:type';
    $stmt=app()->db->prepare($sql);
    $stmt->execute(['id'=>$id,'type'=>$rel_type]);
    return $stmt->fetchAll(\PDO::FETCH_CLASS, '\\MLFW\\Models\\Entity');
  }

  static function extractId($obj) {
    if ($obj instanceof self) return $obj->id;
    if (is_numeric($obj)) return intval($obj);
    throw new \MLFW\ExceptionSecurity("Wrong object passed!");
  }

  function save($rel_from=[],$rel_to=[]) {
    $fields = self::FIELDS+['text','extra'];
    $data = [];
    foreach (self::FIELDS as $field) {
      if ($field==='extra') $data['extra']=json_encode($this->extra);
      else $data[$field]=$this->$field;
    }
    if ($this->id===null) {
      $sql = 'INSERT INTO entity ('.join(',',$field).') VALUES (:'.join(', :').')';
      $stmt = app()->db->prepare($sql);
      $stmt->execute($data);
      $this->id = app()->db->lastInsertId();
    }
    else {
      $pairs = join(',',array_map(function ($field) {
        return $field.='=:'.$field;
      },$fields));
      $sql = "UPDATE entity SET $pairs WHERE id=:id";
      $stmt = app()->db->prepare($sql);
      $stmt->execute($data);
    }
  }
}