<?php
/*

*/
class sfGuardUser extends BasesfGuardUser
{
    protected
        $allPermissions  = null;

    public function __toString()
    {
        return $this->get('username');
    }

    public function filterSetPassword( $password )
    {
        # FIXME: why is this necessary?
        if ( !$password )
        {
            return $this->get('password');
        }

        $salt = md5( rand( 100000, 999999 ) . $this->get('username') );
        $this->set('salt', $salt );
        $algorithm = sfConfig::get( 'app_sf_guard_plugin_algorithm_callable', 'sha1' );
        $algorithmAsStr = is_array( $algorithm ) ? $algorithm[ 0 ].'::' . $algorithm[ 1 ] : $algorithm;
        if ( !is_callable( $algorithm ) )
        {
            throw new sfException( sprintf( 'The algorithm callable "%s" is not callable.', $algorithmAsStr ) );
        }
        $this->set('algorithm', $algorithmAsStr );

        return call_user_func_array( $algorithm, array( $salt . $password ) );
    }

    public function checkPassword( $password )
    {
        if ( $callable = sfConfig::get( 'app_sf_guard_plugin_check_password_callable' ) )
        {
            return call_user_func_array( $callable, array( $this->get('username'), $password ) );
        }
        else
        {
            $algorithm = $this->get('algorithm');
            if ( false !== $pos = strpos( $algorithm, '::' ) )
            {
                $algorithm = array( substr( $algorithm, 0, $pos ), substr( $algorithm, $pos + 2 ) );
            }
            if ( !is_callable( $algorithm ) )
            {
                throw new sfException( sprintf( 'The algorithm callable "%s" is not callable.', $algorithm ) );
            }

            return $this->get('password') == call_user_func_array( $algorithm, array( $this->get('salt') . $password ) );
        }
    }

    public function addGroupByName( $name )
    {
        $group = sfDoctrine::getTable('sfGuardGroup')->retrieveByName( $name );
        if ( !$group )
        {
            throw new Exception( sprintf( 'The group "%s" does not exist.', $name ) );
        }
        
        $this->get('groups')->add($group);
    }

    public function addPermissionByName( $name )
    {
        $permission = sfDoctrine::getTable('sfGuardGroup')->retrieveByName( $name );
        if ( !$permission->exists() )
        {
            throw new Exception( sprintf( 'The permission "%s" does not exist.', $name ) );
        }

        $this->get('permissions')->add($permission);
    }

    public function hasGroup( $name )
    {
        $group = sfDoctrine::queryFrom('sfGuardGroup')->where('sfGuardGroup.name = ? AND sfGuardGroup.users.id = ?', array($name, $this->get('id')))->execute()->getFirst();
        return $group->exists();
    }

    public function getGroupNames()
    {
        # FIXME: won't work since collections are not arrays?
        return array_keys( $this->get('groups') );
    }

    public function hasPermission( $name )
    {
        $permission = sfDoctrine::queryFrom('sfGuardPermission')->where('sfGuardPermission.name = ? AND sfGuardPermission.users.id = ?', array($name, $this->get('id')))->execute()->getFirst();
        return $permission->exists();
    }


    // merge of permission in a group + permissions
    public function getAllPermissions()
    {
        if ( !$this->allPermissions )
        {
            $this->allPermissions = array();

            foreach ( $this->get('groups') as $group )
            {
                foreach ( $group->get('permissions') as $permission )
                {
                    $this->allPermissions[ $permission->getName() ] = $permission;
                }
                
            }

            # FIXME: check that array_merge works with collections...
            $this->allPermissions = array_merge_recursive( $this->allPermissions, $this->get('permissions') );
        }

        return $this->allPermissions;
    }

    public function getPermissionNames()
    {
        $q = new Doctrine_Query();
        $names = $q->select('p.name')->from('sfGuardPermission p')->where('p.users.id = ?', $this->get('id'))->execute();
        return $names;
    }

    public function getAllPermissionNames()
    {
        # FIXME: won't work
        return array_keys( $this->getAllPermissions() );
    }

    public function reloadGroupsAndPermissions()
    {
        $this->allPermissions = null;
    }

    public function set($name, $value, $load = true)
    {
      // do nothing if trying to set the phony password_bis field
      if ($name == 'password_bis')
        return;

      parent::set($name, $value, $load);
    }
}
