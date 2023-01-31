<?php
/**
 * @file
 * Contains active user implementation.
 */
namespace Drupal\user_status_popup\Form;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\user\Entity\User;

/**
 * Defines an Active user form.
 */
class UserStatusActiveForm extends ConfirmFormBase
{
    /**
     * ID of the item to active.
     *
     * @var int
     */
    protected int $user_id;

    /**
     * User entity object.
     *
     * @var \Drupal\user\Entity\User
     */
    protected $user;

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state, $user = NULL)
    {
        $this->user_id = $user->id();
        $this->user = $user;
        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $user = User::load($this->user_id);

        // Active target user.
        $user->activate();
        $user->save();

        // Redirect user after submission.
        $form_state->setRedirectUrl(new Url('entity.user.edit_form', ['user' => $this->user_id]));

        // Set confirmation alert.
        \Drupal::messenger()->addMessage($this->t('The user has been activated successfully.'));
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId() : string
    {
        return "change_user_status_active_form";
    }

    /**
     * {@inheritdoc}
     */
    public function getCancelUrl()
    {
        return new Url('entity.user.edit_form', ['user' => $this->user_id]);
    }

    /**
     * {@inheritdoc}
     */
    public function getQuestion()
    {
        $user = User::load($this->user_id);
        // Get user Full name.
        $full_name = $user->getDisplayName();

        $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
        $warning_html = '';
        if ($language == 'en') {
          // User English name.
          $warning_html = $this->t('Are you sure to active the account %title?', ['%title' => $full_name]);
        }

        return new FormattableMarkup($warning_html, []);
    }

}
