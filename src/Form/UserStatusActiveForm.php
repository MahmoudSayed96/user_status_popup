<?php

namespace Drupal\user_status_popup\Form;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
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
    protected int $userId;

    /**
     * User entity object.
     *
     * @var \Drupal\user\Entity\User
     */
    protected User $user;

    /**
     * The Messenger service.
     *
     * @var \Drupal\Core\Messenger\MessengerInterface
     */
    protected $messenger;

    /**
     * Constructor.
     *
     * @param \Drupal\Core\Messenger\MessengerInterface $messenger
     *   The messenger service.
     */
    public function __construct(MessengerInterface $messenger)
    {
        $this->messenger = $messenger;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('messenger')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state, $user = null): array
    {
        $this->userId = $user->id();
        $this->user = $user;
        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Drupal\Core\Entity\EntityStorageException
     */
    public function submitForm(array &$form, FormStateInterface $form_state): void
    {
        $user = User::load($this->userId);

        // Active target user.
        $user->activate();
        $user->save();

        // Redirect user after submission.
        $form_state->setRedirectUrl(Url::fromUri(\Drupal::request()->headers->get('referer')));

        // Set confirmation alert.
        $this->messenger->addMessage($this->t('The user has been activated successfully.'));
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
    public function getCancelUrl(): Url
    {
        return Url::fromUri(\Drupal::request()->headers->get('referer'));
    }

    /**
     * {@inheritdoc}
     */
    public function getQuestion(): FormattableMarkup|TranslatableMarkup
    {
        $user = User::load($this->userId);
        // Get user Full name.
        $full_name = $user->getDisplayName();
        // User English name.
        $warning_html = $this->t('Are you sure to active the account %title?', ['%title' => $full_name]);
        return new FormattableMarkup($warning_html, []);
    }

}
