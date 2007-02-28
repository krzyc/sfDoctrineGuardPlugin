<?php
/*
This is an automatically generated class. ANY CHANGES WILL BE LOST!
*/
class BasesfGuardGroupPermission extends sfDoctrineRecord
{
  const DATABASE_NAME = 'users';

  
  public function setTableDefinition()
  {
    $this->setTableName('sf_guard_group_permission');

    $this->hasColumn('group_id', 'integer', 11, array (  'notnull' => true,));
    $this->hasColumn('permission_id', 'integer', 11, array (  'notnull' => true,));
  }
  

  
  public function setUp()
  {
    $this->ownsMany('sfGuardGroup as sfGuardGroup', 'sfGuardGroupPermission.group_id', 'id');
    $this->ownsMany('sfGuardPermission as sfGuardPermission', 'sfGuardGroupPermission.permission_id', 'id');
  }
  
}
