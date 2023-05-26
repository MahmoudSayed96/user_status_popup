<?php

namespace Drupal\user_status_popup\Plugin\views\field;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\user\Entity\User;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Provides User Status field handler.
 *
 * @ViewsField("user_status_action_views_field")
 *
 * @DCG
 * The plugin needs to be assigned to a specific table column through
 * hook_views_data() or hook_views_data_alter().
 * For non-existent columns (i.e. computed fields) you need to override
 * self::query() method.
 */
class UserStatusActionViewsField extends FieldPluginBase
{

    /**
     * {@inheritdoc}
     */
    public function usesGroupBy(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function query()
    {
        // Do nothing -- to override the parent query.
    }

    /**
     * {@inheritdoc}
     */
    protected function defineOptions(): array
    {
        return parent::defineOptions();
    }

    /**
     * {@inheritdoc}
     */
    public function buildOptionsForm(&$form, FormStateInterface $form_state): void
    {
        parent::buildOptionsForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function render(ResultRow $values): MarkupInterface|array|string
    {
        // Render links.
        $uid = $values->uid;
        $user = User::load($uid);
        $status = $user->get('status')->getValue()[0]['value'];

        // Hide or show the action buttons based on the user status.
        return [
          '#theme' => 'user_status_action',
          '#status' => $status,
          '#approve_url' => Url::fromRoute('nwc_user_status.user_active', ['user' => $uid]),
          '#reject_url' => Url::fromRoute('nwc_user_status.user_block', ['user' => $uid]),
        ];
    }

}
