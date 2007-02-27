<?php
/*

*/
class sfGuardPermission extends BasesfGuardPermission
{
    public function __toString()
    {
        return $this->get( 'name' );
    }
}
