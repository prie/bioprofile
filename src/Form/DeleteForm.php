<?php

namespace Drupal\bioprofile\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Render\Element;

/**
 * Class DeleteForm
 * @package Drupal\bioprofile\Form
 */
class DeleteForm extends ConfirmFormBase {
    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'delete_form';
    }

    public function getQuestion() {
        $conn = Database::getConnection();
        $query = $conn->select('bioprofile', 'b')
            ->condition('b.id', $this->id)
            ->fields('b', array('name'));
        $name = $query->execute()->fetchField();
        
        return t('Do you want to delete %name?', array('%name' => $name));
    }

    public function getCancelUrl() {
        return new Url('bioprofile.bio_table_display');
    }

    public function getDescription() {
        return t('Only do this if you are sure!');
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmText() {
        return t('Delete it!');
    }

    /**
     * {@inheritdoc}
     */
    public function getCancelText() {
        return t('Cancel');
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state, $cid = NULL) {
        $this->id = $cid;
        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        parent::validateForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $query = \Drupal::database();
        $query->delete('bioprofile')
            ->condition('id', $this->id)
            ->execute();
        drupal_set_message(t('Successfully deleted'));
        $form_state->setRedirect('bioprofile.bio_table_display');
    }
}
