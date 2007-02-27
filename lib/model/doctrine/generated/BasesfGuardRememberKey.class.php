<?php
/*
This is an automatically generated class. ANY CHANGES WILL BE LOST!
*/
class BasesfGuardRememberKey extends sfPropelCompatRecord
{
  const DATABASE_NAME = 'users';

  
  public function setTableDefinition()
  {
    $this->setTableName('sf_guard_remember_key');

    $this->hasColumn('user_id', 'integer', 11, array (  'primary' => true,));
    $this->hasColumn('remember_key', 'string', 32, array ());
    $this->hasColumn('ip_address', 'string', 15, array (  'primary' => true,));
    $this->hasColumn('created_at', 'timestamp', null, array ());
  }
  

  
  public function setUp()
  {
    $this->ownsOne('sfGuardUser as user', 'sfGuardRememberKey.user_id', 'id');
  }
  
}
