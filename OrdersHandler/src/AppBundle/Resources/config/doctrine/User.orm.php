<?php

use Doctrine\ORM\Mapping\ClassMetadataInfo;

$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_DEFERRED_IMPLICIT);
$metadata->mapField(array(
   'fieldName' => 'id',
   'type' => 'integer',
   'id' => true,
   'columnName' => 'id',
  ));
$metadata->mapField(array(
   'columnName' => 'username',
   'fieldName' => 'username',
   'type' => 'string',
   'length' => '50',
  ));
$metadata->mapField(array(
   'columnName' => 'role_id',
   'fieldName' => 'roleId',
   'type' => 'integer',
  ));
$metadata->mapField(array(
   'columnName' => 'name',
   'fieldName' => 'name',
   'type' => 'string',
   'length' => '50',
  ));
$metadata->mapField(array(
   'columnName' => 'surname',
   'fieldName' => 'surname',
   'type' => 'string',
   'length' => '50',
  ));
$metadata->mapField(array(
   'columnName' => 'e-mail',
   'fieldName' => 'e-mail',
   'type' => 'string',
   'length' => '50',
  ));
$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_AUTO);