<?php

namespace Drupal\bioprofile\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class BioprofileSettingForm
 * @package Drupal\bioprofile\Form
 */
class BioprofileSettingForm extends FormBase {
    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'bioprofile_setting_form';
    }
    
    /**
     * https://www.drupal.org/docs/8/api/form-api/introduction-to-form-api
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $config = \Drupal::config('bioprofile.settings');

        $form['list_limit'] = array(
            '#type' => 'textfield',
            '#title' => t('Items Per Page'),
            '#description' => t('The number of items to display per page.'),
            '#required' => TRUE,
            '#default_value' => !empty($config->get('list_limit')) ? $config->get('list_limit') : 25,
        );
        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => t('Save'),
        );

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        if (!intval($form_state->getValue('list_limit'))) {
            $form_state->setErrorByName('list_limit', $this->t('Items Per Page needs to be a number'));
        }
        parent::validateForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $f_value = $form_state->getValues();
        $items_page = $f_value['list_limit'];

        $config = \Drupal::service('config.factory')->getEditable('bioprofile.settings');
        $config->set('list_limit', $items_page);
        $config->save();

        drupal_set_message(t("Successfully update"));
        //$form_state->setRedirect('bioprofile.bio_table_display');
    }
}
