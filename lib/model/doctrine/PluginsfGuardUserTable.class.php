<?php
/*
 * Plugin class
 *
 */
class PluginsfGuardUserTable extends Doctrine_Table
{
	  # should this be a static function?
    public function retrieveByUsername( $username, $isActive = true )
    {
        return $this->createQuery()->where( 'sfGuardUser.username = ? AND sfGuardUser.is_active = ?', array( $username, $isActive ) )->execute()->getFirst();
    }

		static function retrieveByUsernameOrEmailAddress($usernameOrEmail)
		{
			$query = new Doctrine_Query();
			$query->from('sfGuardUser u')->where('u.username = ? OR u.email_address = ?', array($usernameOrEmail, $usernameOrEmail));

			return $query->limit(1)->execute()->getFirst();
		}
}
