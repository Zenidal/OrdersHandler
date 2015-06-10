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
   'columnName' => 'company_id',
   'fieldName' => 'companyId',
   'type' => 'integer',
  ));
$metadata->mapField(array(
   'columnName' => 'user_id',
   'fieldName' => 'userId',
   'type' => 'integer',
  ));
$metadata->mapField(array(
   'columnName' => 'user_role',
   'fieldName' => 'userRole',
   'type' => 'string',
   'length' => '30',
  ));
$metadata->mapField(array(
   'columnName' => 'description',
   'fieldName' => 'description',
   'type' => 'text',
  ));
$metadata->mapField(array(
   'columnName' => 'address',
   'fieldName' => 'address',
   'type' => 'text',
  ));
$metadata->mapField(array(
   'columnName' => 'status',
   'fieldName' => 'status',
   'type' => 'string',
   'length' => '30',
  ));
$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_AUTO);