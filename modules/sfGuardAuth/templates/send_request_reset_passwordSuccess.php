Hello <?php echo $sfGuardUser->getUsername(); ?>,<br/><br/>

<?php echo link_to('Click here to have your password reset and mailed to you', url_for('sfGuardAuth/reset_password?key='.$sfGuardUser->getPassword().'&id='.$sfGuardUser->getId(), true)); ?>
