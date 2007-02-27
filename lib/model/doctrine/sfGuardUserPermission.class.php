<?php
/*

*/
class sfGuardUserPermission extends BasesfGuardUserPermission
{
    public function save( Doctrine_Connection $con = null )
    {
        parent::save( $con );

        $this->getsfGuardUser()->reloadGroupsAndPermissions();
    }
}
