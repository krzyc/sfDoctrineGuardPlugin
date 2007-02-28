<?php
/*

*/
class sfGuardUser extends BasesfGuardUser
{
    const DEFAULT_PROFILE_CLASS      = 'sfGuardUserProfile';
    const DEFAULT_PROFILE_FIELD_NAME = 'user_id';

    protected
        $profile          = null,
        $userGroups       = null,
        $userPermissions  = null,
        $groupPermissions = null;

    public function __toString()
    {
        return $this->getUsername();
    }

    public function filterSetPassword( $password )
    {
        if ( !$password )
        {
            return $this->getPassword();
        }

        $salt = md5( rand( 100000, 999999 ) . $this->getUsername() );
        $this->setSalt( $salt );
        $algorithm = sfConfig::get( 'app_sf_guard_plugin_algorithm_callable', 'sha1' );
        $algorithmAsStr = is_array( $algorithm ) ? $algorithm[ 0 ].'::' . $algorithm[ 1 ] : $algorithm;
        if ( !is_callable( $algorithm ) )
        {
            throw new sfException( sprintf( 'The algorithm callable "%s" is not callable.', $algorithmAsStr ) );
        }
        $this->setAlgorithm( $algorithmAsStr );

        return call_user_func_array( $algorithm, array( $salt . $password ) );
    }

    public function setPasswordBis( $password )
    {
        return null;
    }

    public function checkPassword( $password )
    {
        if ( $callable = sfConfig::get( 'app_sf_guard_plugin_check_password_callable' ) )
        {
            return call_user_func_array( $callable, array( $this->getUsername(), $password ) );
        }
        else
        {
            $algorithm = $this->getAlgorithm();
            if ( false !== $pos = strpos( $algorithm, '::' ) )
            {
                $algorithm = array( substr( $algorithm, 0, $pos ), substr( $algorithm, $pos + 2 ) );
            }
            if ( !is_callable( $algorithm ) )
            {
                throw new sfException( sprintf( 'The algorithm callable "%s" is not callable.', $algorithm ) );
            }

            return $this->getPassword() == call_user_func_array( $algorithm, array( $this->getSalt() . $password ) );
        }
    }

    public function getProfile()
    {
        if ( null === $this->profile )
        {
            $profileClass = sfConfig::get( 'app_sf_guard_plugin_profile_class', self::DEFAULT_PROFILE_CLASS );

            if ( !class_exists( $profileClass ) )
            {
                throw new sfException( sprintf( 'The user profile class "%s" does not exist.', $profileClass ) );
            }

            $fieldName = sfConfig::get( 'app_sf_guard_plugin_profile_field_name', self::DEFAULT_PROFILE_FIELD_NAME );

            $foreignKeyColumnExists = sfDoctrine::getTable( $profileClass )->hasColumn( $fieldName );

            if ( !$foreignKeyColumnExists )
            {
                throw new sfException( sprintf( 'The user profile class "%s" does not contain a "%s" column.', $profileClass, $fieldName ) );
            }

            $this->profile = sfDoctrine::queryFrom( $profileClass )->where( "{$profileClass}.{$fieldName} = ?", $this->getId() )->execute()->getFirst();

            if ( !$this->profile )
            {
                $this->profile = new $profileClass();
                $method = 'set' . sfInflector::camelize( $fieldName );
                $this->profile->{$method}( $this->getId() );
            }
        }

        return $this->profile;
    }

    public function addGroupByName( $name )
    {
        $group = sfDoctrine::getTable('sfGuardGroup')->retrieveByName( $name );
        if ( !$group )
        {
            throw new Exception( sprintf( 'The group "%s" does not exist.', $name ) );
        }

        $ug = new sfGuardUserGroup();
        $ug->setUserId( $this->getId() );
        $ug->setGroupId( $group->getId() );

        $ug->save();
    }

    public function addPermissionByName( $name )
    {
        $permission = sfDoctrine::getTable('sfGuardGroup')->retrieveByName( $name );
        if ( !$permission )
        {
            throw new Exception( sprintf( 'The permission "%s" does not exist.', $name ) );
        }

        $up = new sfGuardUserPermission();
        $up->setUserId( $this->getId() );
        $up->setPermissionId( $permission->getId() );

        $up->save();
    }

    public function hasGroup( $name )
    {
        if ( !$this->userGroups )
        {
            $this->getGroups();
        }

        return isset( $this->userGroups[ $name ] );
    }

    public function getGroups()
    {
        if ( !$this->userGroups )
        {
            $this->userGroups = array();

            foreach ( $this->getUsersGroups() as $group )
            {
                if ( $group->exists() )
                {
                    $this->userGroups[ $group->getName() ] = $group;
                }
            }
        }

        return $this->userGroups;
    }

    public function getGroupNames()
    {
        return array_keys( $this->getGroups() );
    }

    public function hasPermission( $name )
    {
        if ( !$this->userPermissions )
        {
            $this->getPermissions();
        }

        return isset( $this->userPermissions[ $name ] );
    }

    public function getPermissions()
    {
        if ( !$this->userPermissions )
        {
            $this->userPermissions = array();

            foreach ( $this->getUsersPermissions() as $permission )
            {
                if ( $permission->exists() )
                {
                    $this->userPermissions[ $permission->getName() ] = $permission;
                }
            }
        }

        return $this->userPermissions;
    }

    public function getPermissionNames()
    {
        return array_keys( $this->getPermissions() );
    }

    // merge of permission in a group + permissions
    public function getAllPermissions()
    {
        if ( !$this->groupPermissions )
        {
            $this->groupPermissions = array();

            foreach ( $this->getUsersGroups() as $group )
            {
                if ( $group->exists() )
                {
                    foreach ( $group->getGroupsPermissions() as $permission )
                    {
                        if ( $permission->exists() )
                        {
                            $this->groupPermissions[ $permission->getName() ] = $permission;
                        }
                    }
                }
            }

            $this->groupPermissions = array_merge_recursive( $this->groupPermissions, $this->getPermissions() );
        }

        return $this->groupPermissions;
    }

    public function getAllPermissionNames()
    {
        return array_keys( $this->getAllPermissions() );
    }

    public function reloadGroupsAndPermissions()
    {
        $this->userGroups       = null;
        $this->userPermissions  = null;
        $this->groupPermissions = null;
    }

    public function delete( Doctrine_Connection $con = null )
    {
        // We check for profileClass and fieldName existence here because it is ok if they don't
        // exist, but if they don't exist and getProfile() is called, an Exception is thrown.

        $profileClass = sfConfig::get( 'app_sf_guard_plugin_profile_class', self::DEFAULT_PROFILE_CLASS );
        $fieldName    = sfConfig::get( 'app_sf_guard_plugin_profile_field_name', self::DEFAULT_PROFILE_FIELD_NAME );

        // First, check to see if class exists
        if ( class_exists( $profileClass ) )
        {
            // Then see if foreign key column exists.
            // Foreign key column existence will fail with Doctrine throwing an exception
            // if the profileClass doesn't actually exist.
            $foreignKeyColumnExists = sfDoctrine::getTable( $profileClass )->hasColumn( $fieldName );

            if ( $foreignKeyColumnExists && $profile = $this->getProfile() )
            {
                $profile->delete();
            }
        }

        return parent::delete();
    }

    public function set( $name, $value, $load = true )
    {
        $method = 'set' . sfInflector::camelize( $name );

        if ( !$this->getTable()->hasColumn( $name ) &&
             method_exists( $this, $method ) )
        {
            return $this->{$method}( $value );
        }

        return parent::set( $name, $value, $load );
    }

    public function get( $name, $invoke = true )
    {
        $method = 'set' . sfInflector::camelize( $name );

        if ( !$this->getTable()->hasColumn( $name ) &&
             method_exists( $this, $method ) )
        {
            return $this->{$method}();
        }

        return parent::get( $name, $invoke );
    }
}
