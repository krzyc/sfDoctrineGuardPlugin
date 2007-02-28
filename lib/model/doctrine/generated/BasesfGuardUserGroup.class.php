<?php
/*
This is an automatically generated class. ANY CHANGES WILL BE LOST!
*/
class BasesfGuardUserGroup extends sfDoctrineRecord
{
  const DATABASE_NAME = 'users';

  
  public function setTableDefinition()
  {
    $this->setTableName('sf_guard_user_group');

    $this->hasColumn('group_id', 'integer', 11, array (  'notnull' => true,));
    $this->hasColumn('user_id', 'integer', 11, array (  'notnull' => true,));
  }
  

  
  public function setUp()
  {
    $this->ownsMany('sfGuardGroup as sfGuardGroup', 'sfGuardUserGroup.group_id', 'id');
    $this->ownsMany('sfGuardUser as sfGuardUser', 'sfGuardUserGroup.user_id', 'id');
  }
  
}
