<?php
/*
This is an automatically generated class. ANY CHANGES WILL BE LOST!
*/
class BasesfGuardUser extends sfPropelCompatRecord
{
  const DATABASE_NAME = 'users';

  
  public function setTableDefinition()
  {
    $this->setTableName('sf_guard_user');

    $this->hasColumn('username', 'string', 128, array (  'notnull' => true,));
    $this->hasColumn('algorithm', 'string', 128, array (  'default' => 'sha1',  'notnull' => true,));
    $this->hasColumn('salt', 'string', 128, array (  'notnull' => true,));
    $this->hasColumn('password', 'string', 128, array (  'notnull' => true,));
    $this->hasColumn('created_at', 'timestamp', null, array ());
    $this->hasColumn('last_login', 'timestamp', null, array ());
    $this->hasColumn('is_active', 'boolean', null, array (  'default' => true,  'notnull' => true,));
    $this->hasColumn('is_super_admin', 'boolean', null, array (  'default' => false,  'notnull' => true,));
  }
  

  
  public function setUp()
  {
    $this->hasOne('sfGuardRememberKey as remember_key', 'sfGuardRememberKey.user_id');
    $this->hasMany('sfGuardGroup as users_groups', 'sfGuardUserGroup.group_id');
    $this->hasMany('sfGuardPermission as users_permissions', 'sfGuardUserPermission.permission_id');
  }
  
}
