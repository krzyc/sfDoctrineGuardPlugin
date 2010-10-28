<?php

/**
 * Base actions for the sfGuardForgotPasswordPlugin sfGuardForgotPassword module.
 *
 * @package     sfGuardForgotPasswordPlugin
 * @subpackage  sfGuardForgotPassword
 * @author      Your name here
 * @version     SVN: $Id: BaseActions.class.php 12534 2008-11-01 13:38:27Z Kris.Wallsmith $
 */
abstract class BasesfGuardForgotPasswordActions extends sfActions
{
  public function preExecute()
  {
    if ($this->getUser()->isAuthenticated())
    {
      $this->redirect('@homepage');
    }
  }

  public function executeIndex($request)
  {
    $this->form = new sfGuardRequestForgotPasswordForm();

    if ($request->isMethod('post'))
    {
      $i18n = $this->getContext()->getI18N();

      $this->form->bind($request->getParameter($this->form->getName()));
      if ($this->form->isValid())
      {
        $this->user = Doctrine_Core::getTable('sfGuardUser')
          ->retrieveByUsernameOrEmailAddress($this->form->getValue('email_address'));
        $this->_deleteOldUserForgotPasswordRecords();

        $forgotPassword = new sfGuardForgotPassword();
        $forgotPassword->user_id = $this->user->id;
        $forgotPassword->unique_key = md5(rand() + time());
        $forgotPassword->expires_at = new Doctrine_Expression('NOW()');
        $forgotPassword->save();

        $message = $this->getMailer()->compose(
          sfConfig::get('app_sf_guard_plugin_default_from_email', 'from@noreply.com'),
          $this->user->email_address,
          $i18n->__('Forgot Password Request for %name%', array('%name%' => $this->user->username), 'sf_guard'),
          $this->getPartial('sfGuardForgotPassword/send_request', array('user' => $this->user, 'forgot_password' => $forgotPassword))
        )->setContentType('text/html');
        $this->getMailer()->send($message);

        $this->getUser()->setFlash('notice', $i18n->__('Check your e-mail! You should receive something shortly!'));

        $this->redirect(sfConfig::get('app_sf_guard_plugin_password_request_url', '@sf_guard_signin'));
      } else {
        $this->getUser()->setFlash('error', $i18n->__('Invalid e-mail address!'));
      }
    }
  }

  public function executeChange($request)
  {
    $this->forgotPassword = $this->getRoute()->getObject();
    $this->user = $this->forgotPassword->User;
    $this->form = new sfGuardChangeUserPasswordForm($this->user);

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));
      if ($this->form->isValid())
      {
        $this->form->save();

        $this->_deleteOldUserForgotPasswordRecords();

        $i18n = $this->getContext()->getI18N();

        $message = $this->getMailer()->compose(
          sfConfig::get('app_sf_guard_plugin_default_from_email', 'from@noreply.com'),
          $this->user->email_address,
          $i18n->__('New Password for %name%', array('%name%' => $this->user->username) , 'sf_guard'),
          $this->getPartial('sfGuardForgotPassword/new_password', array('user' => $this->user, 'password' => $request['sf_guard_user']['password']))
        )->setContentType('text/html');
        $this->getMailer()->send($message);

        $this->getUser()->setFlash('notice', $i18n->__('Password updated successfully!'));

        $this->redirect('@sf_guard_signin');
      }
    }
  }

  private function _deleteOldUserForgotPasswordRecords()
  {
    Doctrine_Core::getTable('sfGuardForgotPassword')
      ->createQuery('p')
      ->delete()
      ->where('p.user_id = ?', $this->user->id)
      ->execute();
  }
}
