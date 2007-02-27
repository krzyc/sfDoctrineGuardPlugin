<?php
/*
This is an automatically generated class. ANY CHANGES WILL BE LOST!
*/
class BasesfGuardUserPermission extends sfPropelCompatRecord
{
  const DATABASE_NAME = 'users';

  
  public function setTableDefinition()
  {
    $this->setTableName('sf_guard_user_permission');

    $this->hasColumn('user_id', 'integer', 11, array (  'notnull' => true,));
    $this->hasColumn('permission_id', 'integer', 11, array (  'notnull' => true,));
  }
  

  
  public function setUp()
  {
    $this->ownsMany('sfGuardUser as sfGuardUser', 'sfGuardUserPermission.user_id', 'id');
    $this->ownsMany('sfGuardPermission as sfGuardPermission', 'sfGuardUserPermission.permission_id', 'id');
  }
  
}
