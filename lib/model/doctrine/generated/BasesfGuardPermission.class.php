<?php
/*
This is an automatically generated class. ANY CHANGES WILL BE LOST!
*/
class BasesfGuardPermission extends sfPropelCompatRecord
{
  const DATABASE_NAME = 'users';

  
  public function setTableDefinition()
  {
    $this->setTableName('sf_guard_permission');

    $this->hasColumn('name', 'string', 255, array (  'notnull' => true,));
    $this->hasColumn('description', 'string', 4000, array ());
  }
  

  
  public function setUp()
  {
    $this->hasMany('sfGuardGroup as groups', 'sfGuardGroupPermission.group_id');
    $this->hasMany('sfGuardUser as users', 'sfGuardUserPermission.user_id');
  }
  
}
