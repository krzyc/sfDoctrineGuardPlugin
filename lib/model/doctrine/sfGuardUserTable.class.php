<?php
/*

*/
class sfGuardUserTable extends Doctrine_Table
{
    public static function retrieveByUsername( $username, $isActive = true )
    {
        return $this->createQuery()->where( 'sfGuardUser.username = ? AND sfGuardUser.is_active = ?', array( $username, $isActive ) )->execute()->getFirst();
    }
}
