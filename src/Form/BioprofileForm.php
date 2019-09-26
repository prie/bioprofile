<?php

namespace Drupal\bioprofile\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class BioprofileForm
 * @package Drupal\bioprofile\Form
 */
class BioprofileForm extends FormBase {
    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'bioprofile_form';
    }
    
    /**
     * https://www.drupal.org/docs/8/api/form-api/introduction-to-form-api
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $is_edit = FALSE;

        $route_match = \Drupal::service('current_route_match');
        $cid = $route_match->getParameter('cid');

        // If edit form
        $record = array();
        if ($cid) {
            $is_edit = TRUE;

            $form['bio_id'] = array(
                '#type' => 'hidden',
                //'#title' => t('Name'),
                '#required' => TRUE,
                '#default_value' => $cid,
            );

            $conn = Database::getConnection();
            $query = $conn->select('bioprofile', 'b')
                ->condition('b.id', $cid)
                ->fields('b');
            $record = $query->execute()->fetchAssoc();
        }
        
        $form['bio_name'] = array(
            '#type' => 'textfield',
            '#title' => t('Name'),
            '#required' => TRUE,
            '#default_value' => isset($record['name']) ? $record['name'] : '',
        );
        $form['bio_mobile'] = array(
            '#type' => 'textfield',
            '#title' => t('Mobile'),
            '#default_value' => isset($record['mobile']) ? $record['mobile'] : '',
        );
        $form['bio_email'] = array(
            '#type' => 'email',
            '#title' => t('Email'),
            '#required' => TRUE,
            '#default_value' => isset($record['email']) ? $record['email'] : '',
        );
        $form['bio_age'] = array(
            '#type' => 'textfield',
            '#title' => t('Age'),
            '#required' => TRUE,
            '#default_value' => isset($record['age']) ? $record['age'] : '',
        );
        $form['bio_gender'] = array(
            '#type' => 'select',
            '#title' => t('Gender'),
            '#options' => array(
                'male' => t('Male'),
                'female' => t('Female'),
            ),
            '#default_value' => isset($record['gender']) ? $record['gender'] : 'male',
        );
        $form['bio_website'] = array(
            '#type' => 'textfield',
            '#title' => t('Website'),
            '#default_value' => isset($record['website']) ? $record['website'] : '',
        );
        $submit_value = $is_edit ? t('Update') : t('Save');
        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => $submit_value,
        );

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        $name = $form_state->getValue('bio_name');
        /*
        if (preg_match('/[^A-Za-z]/', $name)) {
            $form_state->setErrorByName('bio_name', $this->t('Your name must in character without space'));
        }
        */
        if (strlen($form_state->getValue('bio_mobile')) < 10) {
            $form_state->setErrorByName('bio_mobile', $this->t('Your mobile number must in 10 digits or more'));
        }
        if (!intval($form_state->getValue('bio_age'))) {
            $form_state->setErrorByName('bio_age', $this->t('Age needs to be a number'));
        }
        parent::validateForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $f_value = $form_state->getValues();
        $id = isset($f_value['bio_id']) ? $f_value['bio_id'] : '';
        $name = $f_value['bio_name'];
        $mobile = $f_value['bio_mobile'];
        $email = $f_value['bio_email'];
        $age = $f_value['bio_age'];
        $gender = $f_value['bio_gender'];
        $website = $f_value['bio_website'];

        $field = array(
            'name' => $name,
            'mobile' => $mobile,
            'email' => $email,
            'age' => $age,
            'gender' => $gender,
            'website' => $website,
        );

        if (!empty($id)) {  
            $query = \Drupal::database();
            $query->update('bioprofile')
                ->fields($field)
                ->condition('id', $id)
                ->execute();
            drupal_set_message(t("Successfully update"));
            $form_state->setRedirect('bioprofile.bio_table_display');
        } else {
            $query = \Drupal::database();
            $query->insert('bioprofile')
                ->fields($field)
                ->execute();
            drupal_set_message(t("Successfully saved"));
            //$form_state->setRedirect('bioprofile.bio_table_display');
            $response = new RedirectResponse("/bioprofile/list");
            $response->send();
        }
    }
}
