<?php
/*

*/
class sfGuardGroupTable extends Doctrine_Table
{
    public function retrieveByName( $name )
    {
        return $this->createQuery()->where( 'sfGuardGroup.name = ?', $name )->execute()-getFirst();
    }
}
