<?php use_helper('Validation') ?>

<div id="sf_guard_auth_form">
<?php echo form_tag('@sf_guard_signin') ?>

  <fieldset>

  <div class="form-row" id="sf_guard_auth_username">
    <?php echo form_error('username') ?>
    <label for="username">username:</label>
    <?php echo input_tag('username', $sf_data->get('sf_params')->get('username'), array('autocomplete' => 'off')) ?>
  </div>

  <div class="form-row" id="sf_guard_auth_password">
    <?php echo form_error('password') ?>
    <label for="password">password:</label>
    <?php echo input_password_tag('password') ?>
  </div>
  <div class="form-row" id="sf_guard_auth_remember">
	<label for="remember">Remember me?</label>
	<?php echo checkbox_tag('remember')?>
  </div>
  </fieldset>

  <?php echo submit_tag('sign in') ?>
  <?php echo link_to('Forgot your password?', '@sf_guard_password', array('id' => 'sf_guard_auth_forgot_password')) ?>

</form>
</div>
