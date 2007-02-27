<?php
/*
This is an automatically generated class. ANY CHANGES WILL BE LOST!
*/
class BasesfGuardGroup extends sfPropelCompatRecord
{
  const DATABASE_NAME = 'users';

  
  public function setTableDefinition()
  {
    $this->setTableName('sf_guard_group');

    $this->hasColumn('name', 'string', 255, array (  'notnull' => true,));
    $this->hasColumn('description', 'string', 4000, array ());
  }
  

  
  public function setUp()
  {
    $this->hasMany('sfGuardPermission as groups_permissions', 'sfGuardGroupPermission.permission_id');
    $this->hasMany('sfGuardUser as users', 'sfGuardUserGroup.user_id');
  }
  
}
