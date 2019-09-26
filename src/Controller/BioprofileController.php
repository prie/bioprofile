<?php

namespace Drupal\bioprofile\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\examples\Utility\DescriptionTemplateTrait;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Class BioprofileController.
 * @package Drupal\bioprofile\Controller
 */
class BioprofileController extends ControllerBase {

  use DescriptionTemplateTrait;

  protected function getModuleName() {
    return 'bioprofile';
  }

  /**
   * This content can be found in the twig template file: templates/description.html.twig.
   */
  public function basicInstructions() {
    return [
      $this->description(),
    ];
  }

  /**
   * Display
   * @return string
   * Return Hello string
   */
  public function bioprofileList() {
    // Create table header
    $header_table = array(
      array('data' => $this->t('ID'), 'field' => 'b.id'),
      array('data' => $this->t('Name'), 'field' => 'b.name', 'sort' => 'desc'),
      array('data' => $this->t('Mobile'), 'field' => 'b.mobile'),
      array('data' => $this->t('Email'), 'field' => 'b.email'),
      array('data' => $this->t('Age'), 'field' => 'b.age'),
      array('data' => $this->t('Gender'), 'field' => 'b.gender'),
      array('data' => $this->t('Website'), 'field' => 'b.website'),
      array('data' => $this->t('operations')),
    );

    // Get config limit
    $config = \Drupal::config('bioprofile.settings');
    $limit_page = $config->get('list_limit');

    // Select records from table
    $query = \Drupal::database()->select('bioprofile', 'b');
    $query->fields('b', ['id', 'name', 'mobile', 'email', 'age', 'gender', 'website']);
    $sort_table = $query->extend('Drupal\Core\Database\Query\TableSortExtender')->orderByHeader($header_table);
    $pager = $sort_table->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit($limit_page);
    $results = $pager->execute()->fetchAll();

    $rows = array();
    foreach ($results as $data) {
      //$edit = Url::fromUserInput('/bioprofile/form?num=' . $data->id, ['attributes' => ['class' => ['button']]]);
      //$delete = Url::fromUserInput('/bioprofile/form/delete/' . $data->id, ['attributes' => ['class' => ['button']]]);
      
      $web_url = Url::fromUri($data->website, ['attributes' => ['target' => ['_blank']]]);
      $web_link = Link::fromTextAndUrl($data->website, $web_url);

      $edit_link = Link::createFromRoute($this->t('Edit'), 'bioprofile.edit_form', ['cid' => $data->id], ['attributes' => ['class' => ['button']]]);
      $dele_link = Link::createFromRoute($this->t('Delete'), 'bioprofile.delete_form', ['cid' => $data->id], ['attributes' => ['class' => ['button']]]);
      $build_link_action = [
        'action_edit' => [
          '#type' => 'html_tag',
          '#value' => $edit_link->toString(),
          '#tag' => 'span',
          //'#attributes'=>['class'=>['button action-edit']],
        ],
        'action_delete' => [
          '#type' => 'html_tag',
          '#value' => $dele_link->toString(),
          '#tag' => 'span',
          //'#attributes'=>['class'=>['button action-edit']],
        ],
      ];
      
      // Print the data from table
      $rows[] = array(
        array('data' => $data->id),
        array('data' => $data->name),
        array('data' => $data->mobile),
        array('data' => $data->email),
        array('data' => $data->age),
        array('data' => $data->gender),
        array('data' => $web_link->toString()),
        // array('data' => \Drupal::l('Edit', $edit)),
        // array('data' => $edit_link),
        array('data' => \Drupal::service('renderer')->render($build_link_action)),
      );
    }
    
    // Display data in site
    //$add_data = Url::fromRoute('bioprofile.bio_form', [], ['attributes' => ['class' => ['button']]]);
    //$descript = Url::fromRoute('bioprofile.bio_display', [], ['attributes' => ['class' => ['button']]]);
    $profile_add = Link::createFromRoute($this->t('Add Profile'), 'bioprofile.bio_form', [], ['attributes' => ['class' => ['button']]]);
    $profile_descript = Link::createFromRoute($this->t('Profile Description'), 'bioprofile.bio_display', [], ['attributes' => ['class' => ['button']]]);
    
    $table_data['markup'] = [
      '#markup' => $profile_add->toString() . $profile_descript->toString(),
    ];
    $table_data['table'] = [
      '#type' => 'table',
      '#header' => $header_table,
      '#rows' => $rows,
      '#empty' => t('No profiles found.'),
    ];
    $table_data['pager'] = [
      '#type' => 'pager',
    ];

    return $table_data;
  }
}