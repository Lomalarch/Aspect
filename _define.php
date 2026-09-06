<?php
/**
 * @file
 * @brief       The theme Aspect definition
 * @ingroup     aspect
 *
 * @defgroup    aspect Theme Aspect.
 *
 * A reader-focused theme built for bloggers, writers and journalists.
 *
 * @package     Dotclear
 * @subpackage  Themes
 * @copyright   Théodore and Noé
 * @copyright   GNU GPL v2
 */

$this->registerModule(
    'Aspect', // Name
    'A reader-focused theme built for bloggers, writers and journalists.', // Description
    'Théodore, Noé', // Author
    '3.1', // Version
    [
        'requires'    => [['core', '2.39']],
        'type'   => 'theme',
        'tplset' => 'mustek',
        'overload'  => true
    ]
);
