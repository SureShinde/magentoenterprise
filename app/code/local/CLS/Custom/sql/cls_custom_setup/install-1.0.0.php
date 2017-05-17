<?php
/**
 * install-1.0.0.php
 *
 * @category    CLS
 * @package     Custom
 * @author      Chris Huffman <chris.huffman@classyllama.com>
 * @copyright   Copyright (c) 2015 Classy Llama Studios, LLC
 */

$installer = $this;
$installer->startSetup();

$this->addAttribute('catalog_product', 'blog_tags', array(
    'group' => 'General',
    'type' => 'text',
    'note' => 'Comma separated values of blog tags to use.',
    'label' => 'Blog Tags',
    'input' => 'text',
    'used_in_product_listing' => 1,
    'required' => false,
    'default' => '',
));

$installer->endSetup();