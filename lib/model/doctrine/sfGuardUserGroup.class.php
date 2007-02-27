<?php
/*

*/
class sfGuardUserGroup extends BasesfGuardUserGroup
{
    public function save( Doctrine_Connection $con = null )
    {
        parent::save( $con );

        $this->getsfGuardUser()->reloadGroupsAndPermissions();
    }
}
