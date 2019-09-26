<?php

namespace Drupal\bioprofile\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a BioprofileBlock block
 * @block(
 *  id = "bioprofile_block",
 *  admin_label = @Translation("Bioprofile block"),
 * )
 */
class BioprofileBlock extends BlockBase {
    /**
     * {@inheritdoc}
     */
    public function build() {
        $form = \Drupal::formBuilder()->getForm('Drupal\bioprofile\Form\BioprofileForm');
    
        return $form;
    }
}
