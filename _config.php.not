<?php
if (!defined('DC_RC_PATH')) {
    return;
}

l10n::set(__DIR__ . '/locales/' . dcCore::app()->lang . '/public');

class adminConfigAspect
{
    /**
     * Defines the sections in which the theme settings will be sorted.
     *
     * The sections and sub-sections are placed in an array following this pattern:
     * $page_sections['section_id'] = [
     *     'name'         => 'The name of this section',
     *     'sub_sections' => [
     *         'sub_section_1_id' => 'The name of this subsection',
     *         'sub_section_2_id' => …
     *     ]
     * ];
     *
     * @return array Sections and sub-sections.
     */
    public static function page_sections()
    {
        $page_sections['header'] = [
            'name'         => __('Header Settings'),
            'sub_sections' => [
                'logo' => __('Logo'),
            ]
        ];

        $page_sections['content'] = [
            'name'         => __('Content Settings'),
            'sub_sections' => [
                'post' => __('Posts'),
            ]
        ];

        $page_sections['footer'] = [
            'name'         => __('Footer Settings'),
            'sub_sections' => [
                'no-title' => '',
            ]
        ];

        return $page_sections;
    }

    /**
     * Defines all customization settings of the theme.
     *
     * $default_settings['setting_id'] = [
     *     'title'       => (string) The title of the setting,
     *     'description' => (string) The description of the setting,
     *     'type'        => (string) The type of the form input (checkbox, string, select, select_int),
     *     'choices'     => [
     *         __('The name of the option') => 'the-id-of-the-option', // Choices are only used with "select" and "select_int" types.
     *     ],
     *     'default'     => (string) The default value of the setting,
     *     'section'     => (array) ['section', 'sub_section'] The section where to put the setting
     * ];
     *
     * @return array The settings.
     */
    public static function default_settings()
    {
        // Global settings.
        $default_settings['header_logo_url'] = [
            'title'       => __('Logo URL'),
            'description' => __(''),
            'type'        => 'text',
            'default'     => '',
            'placeholder' => 'https://…',
            'section'     => ['header', 'logo']
        ];

        $default_settings['header_logo_url_2x'] = [
            'title'       => __('Dual pixel density logo URL'),
            'description' => __('To ensure proper display on dual pixel density displays (Retina), please provide an image that is twice the size of the standard image.'),
            'type'        => 'text',
            'default'     => '',
            'placeholder' => 'https://…',
            'section'     => ['header', 'logo']
        ];

        $default_settings['content_style'] = [
            'title'       => __('Style of post content'),
            'description' => '',
            'type'        => 'select',
            'choices'     => [
                __('Standard (default)') => 'standard',
                __('Roman')              => 'roman'
            ],
            'default'     => 'standard',
            'section'     => ['content', 'post']
        ];

        $default_settings['content_title_style'] = [
            'title'       => __('Appearance of titles'),
            'description' => '',
            'type'        => 'select',
            'choices'     => [
                __('Standard')             => 'standard',
                __('Small caps (default)') => 'small-caps'
            ],
            'default'     => 'small-caps',
            'section'     => ['content', 'post']
        ];

        $default_settings['footer_credits'] = [
            'title'       => __('Display a mention to Dotclear and the theme'),
            'description' => __('Allows you to advertise Dotclear and this theme.'),
            'type'        => 'checkbox',
            'default'     => 1,
            'section'     => ['footer', 'no-title']
        ];

        $default_settings['styles'] = [
            'title' => __('Theme styles to add in the blog head'),
        ];

        return $default_settings;
    }

    /**
     * Retrieves all theme settings stored in the database.
     *
     * @return array The id of the saved parameters associated with their values.
     */
    public static function saved_settings()
    {
        $saved_settings   = [];
        $default_settings = self::default_settings();

        foreach ($default_settings as $setting_id => $setting_data) {
            if (dcCore::app()->blog->settings->aspect->$setting_id !== null) {
                if (isset($setting_data['type']) && $setting_data['type'] === 'checkbox') {
                    $saved_settings[$setting_id] = (boolean) dcCore::app()->blog->settings->aspect->$setting_id;
                } elseif (isset($setting_data['type']) && $setting_data['type'] === 'select_int') {
                    $saved_settings[$setting_id] = (integer) dcCore::app()->blog->settings->aspect->$setting_id;
                } else {
                    $saved_settings[$setting_id] = dcCore::app()->blog->settings->aspect->$setting_id;
                }
            }
        }

        return $saved_settings;
    }

    /**
     * Converts a style array into a minified style string.
     *
     * @param array $rules An array of styles.
     *
     * @return string $css The minified styles.
     */
    public static function styles_array_to_string($rules)
    {
        $css = '';

        foreach ($rules as $key => $value) {
            if (is_array($value) && !empty($value)) {
                $selector   = $key;
                $properties = $value;

                $css .= str_replace(', ', ',', $selector) . '{';

                if (is_array($properties) && !empty($properties)) {
                    foreach ($properties as $property => $rule) {
                        if ($rule !== '') {
                            $css .= $property . ':' . str_replace(', ', ',', $rule) . ';';
                        }
                    }
                }

                $css .= '}';
            }
        }

        return $css;
    }

    /**
     * Displays each parameter according to its type.
     *
     * @param strong $setting_id The id of the setting to display.
     *
     * @return void The parameter.
     */
    public static function setting_rendering($setting_id = '')
    {
        $default_settings = self::default_settings();
        $saved_settings   = self::saved_settings();

        if ($setting_id !== '' && array_key_exists($setting_id, $default_settings)) {
            echo '<p id=', $setting_id, '-input>';

            // Displays the default value of the parameter if it is not defined.
            if (isset($saved_settings[$setting_id])) {
                $setting_value = $saved_settings[$setting_id];
            } else {
                $setting_value = isset($default_settings[$setting_id]['default']) ? $default_settings[$setting_id]['default'] : '';
            }

            switch ($default_settings[$setting_id]['type']) {
                case 'checkbox' :
                    echo form::checkbox(
                         $setting_id,
                         true,
                         $setting_value
                    ),
                    '<label class=classic for=', $setting_id, '>',
                    $default_settings[$setting_id]['title'],
                    '</label>';

                    break;

                case 'select' :
                case 'select_int' :
                    echo '<label for=', $setting_id, '>',
                    $default_settings[$setting_id]['title'],
                    '</label>',
                    form::combo(
                        $setting_id,
                        $default_settings[$setting_id]['choices'],
                        strval($setting_value)
                    );

                    break;

                case 'textarea' :
                    $placeholder = isset($default_settings[$setting_id]['placeholder']) ? 'placeholder="' . $default_settings[$setting_id]['placeholder'] . '"' : '';

                    echo '<label for=', $setting_id, '>',
                    $default_settings[$setting_id]['title'],
                    '</label>',
                    form::textArea(
                        $setting_id,
                        60,
                        3,
                        $setting_value,
                        '',
                        '',
                        false,
                        $placeholder
                    );

                    break;

                default :
                    $placeholder = isset($default_settings[$setting_id]['placeholder']) ? 'placeholder="' . $default_settings[$setting_id]['placeholder'] . '"' : '';

                    echo '<label for=', $setting_id, '>',
                    $default_settings[$setting_id]['title'],
                    '</label>',
                    form::field(
                        $setting_id,
                        30,
                        255,
                        $setting_value,
                        '',
                        '',
                        false,
                        $placeholder
                    );

                    break;
            }

            echo '</p>';

            // Displays the description of the parameter as a note.
            if ($default_settings[$setting_id]['type'] === 'checkbox' || (isset($default_settings[$setting_id]['description']) && $default_settings[$setting_id]['description'] !== '')) {
                echo '<p class=form-note id=', $setting_id, '-description>', $default_settings[$setting_id]['description'];

                // If the parameter is a checkbox, displays its default value as a note.
                if ($default_settings[$setting_id]['type'] === 'checkbox') {
                    if ($default_settings[$setting_id]['default'] === 1) {
                        echo ' ', __('Default: checked.');
                    } else {
                        echo ' ', __('Default: unchecked.');
                    }
                }

                echo '</p>';
            }
        }
    }

    /**
     * Saves the settings to the database.
     *
     * If the parameter value is equal to the default value, the parameter is removed from the database.
     *
     * @return void
     */
    public static function save_settings()
    {
        if (!empty($_POST)) {
            $default_settings = self::default_settings();
            $saved_settings   = self::saved_settings();

            try {
                dcCore::app()->blog->settings->addNamespace('aspect');

                if (isset($_POST['save'])) {
                    foreach ($default_settings as $setting_id => $setting_value) {
                        if ($setting_id !== 'styles') {
                            if (isset($_POST[$setting_id])) {
                                $drop          = false;
                                $setting_value = '';
                                $setting_type  = isset($default_settings[$setting_id]['type']) ? $default_settings[$setting_id]['type'] : 'string';
                                $setting_title = isset($default_settings[$setting_id]['title']) ? $default_settings[$setting_id]['title'] : '';

                                // If the parameter has a new value that is different from the default (and is not an unchecked checkbox).
                                if ($_POST[$setting_id] != $default_settings[$setting_id]['default']) {
                                    if ($setting_type === 'select') {
                                        // Checks if the input value is proposed by the setting.
                                        if (in_array($_POST[$setting_id], $default_settings[$setting_id]['choices'])) {
                                            $setting_value = $_POST[$setting_id];
                                        } else {
                                            $drop = true;
                                        }

                                        $setting_type = 'string';
                                    } elseif ($setting_type === 'select_int') {
                                        // Checks if the input value is proposed by the setting.
                                        if (in_array((int) $_POST[$setting_id], $default_settings[$setting_id]['choices'], true)) {
                                            $setting_value = (int) $_POST[$setting_id];
                                        } else {
                                            $drop = true;
                                        }

                                        $setting_type = 'integer';
                                    } elseif ($setting_type === 'checkbox') {
                                        if ($_POST[$setting_id] === '1' && $default_settings[$setting_id]['default'] !== '1') {
                                            $setting_value = true;
                                            $setting_type  = 'boolean';
                                        }
                                    } else {
                                        $setting_value = html::escapeHTML($_POST[$setting_id]);
                                    }

                                // If the value is equal to the default value, removes the parameter.
                                } elseif ($_POST[$setting_id] == $default_settings[$setting_id]['default']) {
                                    $drop = true;
                                }

                                if ($drop === false) {
                                    dcCore::app()->blog->settings->aspect->put(
                                        $setting_id,
                                        $setting_value,
                                        $setting_type,
                                        html::clean($setting_title),
                                        true
                                    );
                                } else {
                                    dcCore::app()->blog->settings->aspect->drop($setting_id);
                                }

                            // For unchecked checkboxes (= no POST request), does a specific action.
                            } elseif (!isset($_POST[$setting_id]) && $default_settings[$setting_id]['type'] === 'checkbox') {
                                $setting_title = isset($default_settings[$setting_id]['title']) ? $default_settings[$setting_id]['title'] : '';

                                if ($default_settings[$setting_id]['default'] !== 0) {
                                    dcCore::app()->blog->settings->aspect->put(
                                        $setting_id,
                                        false,
                                        'boolean',
                                        html::clean($setting_title),
                                        true
                                    );
                                } else {
                                    dcCore::app()->blog->settings->aspect->drop($setting_id);
                                }
                            } else {
                                dcCore::app()->blog->settings->aspect->drop($setting_id);
                            }
                        }
                    }

                    dcPage::addSuccessNotice(__('The theme configuration has been updated.'));
                } if (isset($_POST['reset'])) {
                    foreach ($default_settings as $setting_id => $setting_value) {
                        dcCore::app()->blog->settings->aspect->drop($setting_id);
                    }

                    dcPage::addSuccessNotice(__('The theme configuration has been reset.'));
                }

                // Puts styles in the database.
                adminConfigAspect::add_theme_styles();

                // Refreshes the blog.
                dcCore::app()->blog->triggerBlog();

                // Resets template cache.
                dcCore::app()->emptyTemplatesCache();

                /**
                 * Redirects to refresh form values.
                 *
                 * With the parameters ['module' => 'aspect', 'conf' => '1'],
                 * the & is interpreted as &amp; causing a wrong redirect.
                 */
                http::redirect(dcCore::app()->adminurl->get('admin.blog.theme', ['module' => 'aspect']) . '&conf=1');
            } catch (Exception $e) {
                dcCore::app()->error->add($e->getMessage());
            }
        }
    }

    /**
     * Adds custom styles to the theme to apply the settings.
     *
     * @return void
     */
    public static function add_theme_styles()
    {
        if (isset($_POST['save'])) {
            $css = '';

            $css_main_array      = [];
            $css_media_999_array = [];
            $css_media_600_array = [];

            $default_settings = self::default_settings();

            if (isset($_POST['header_logo_url']) && $_POST['header_logo_url'] !== '') {
                $css_main_array['#site-logo']['margin-bottom'] = '1em';
                $css_main_array['#site-logo']['max-width']     = '120px';

                $css_main_array['#site-logo a']['display'] = 'inline-block';
                $css_main_array['#site-logo a']['height']  = 'auto';

                $css_main_array['#site-logo img']['border-radius'] = '2px';

                $css_media_999_array['#site-logo']['max-width']    = '100px';
                $css_media_999_array['#site-logo']['margin-left']  = 'auto';
                $css_media_999_array['#site-logo']['margin-right'] = 'auto';

                $css_media_600_array['#site-logo']['max-width'] = '80px';
            }

            if (isset($_POST['content_style']) && $_POST['content_style'] === 'roman') {
                $css_main_array['.post-content > p']['margin']      = '0';
                $css_main_array['.post-content > p']['text-indent'] = '1.5em';

                $css_main_array['.post-content p iframe']['margin-left'] = '-1.5em';

                $css_main_array['.comment-content p']['margin'] = '0';
            } else {
                $css_main_array['.post-content > p']['margin'] = '1em 0';
            }

            if (isset($_POST['content_title_style']) && $_POST['content_title_style'] === 'small-caps') {
                $css_main_array['#content-info h2, #site-title, .post-title, dt']['font-variant'] = 'small-caps';
            }

            $css .= !empty($css_main_array) ? self::styles_array_to_string($css_main_array) : '';
            $css .= !empty($css_media_999_array) ? '@media only screen and (max-width:999px){' . self::styles_array_to_string($css_media_999_array) . '}' : '';
            $css .= !empty($css_media_600_array) ? '@media only screen and (max-width:600px){' . self::styles_array_to_string($css_media_600_array) . '}' : '';

            if (!empty($css)) {
                dcCore::app()->blog->settings->aspect->put(
                    'styles',
                    str_replace('&gt;', '>', htmlspecialchars($css, ENT_NOQUOTES)),
                    'string',
                    $default_settings['styles']['title'],
                    true
                );
            } else {
                dcCore::app()->blog->settings->aspect->drop('styles');
            }
        } else {
            dcCore::app()->blog->settings->aspect->drop('styles');
        }
    }

    /**
     * Displays the theme configuration page.
     *
     * @return void
     */
    public static function page_rendering()
    {
        /**
         * Creates a table that contains all the parameters and their titles according to the following pattern:
         *
         * $sections_with_settings_id = [
         *     'section_1_id' => [
         *         'sub_section_1_id' => ['setting_1_id', 'option_2_id'],
         *         'sub_section_2_id' => …
         *     ]
         * ];
         */
        $sections_with_settings_id = [];

        $sections = self::page_sections();
        $settings = self::default_settings();

        // Puts titles in the setting array.
        foreach($sections as $section_id => $section_data) {
            $sections_with_settings_id[$section_id] = [];
        }

        // Puts all parameters in their section.
        foreach($settings as $setting_id => $setting_data) {
            if ($setting_id !== 'styles') {
                // If a sub-section is set.
                if (isset($setting_data['section'][1])) {
                    $sections_with_settings_id[$setting_data['section'][0]][$setting_data['section'][1]][] = $setting_id;
                } else {
                    $sections_with_settings_id[$setting_data['section'][0]][] = $setting_id;
                }
            }
        }

        // Removes the titles if they are not associated with any parameter.
        $sections_with_settings_id = array_filter($sections_with_settings_id);
        ?>

        <form action="<?php echo dcCore::app()->adminurl->get('admin.blog.theme', ['module' => 'aspect', 'conf' => '1']); ?>" enctype=multipart/form-data id=theme_config method=post>
            <?php
            // Displays the title of each section and places the corresponding parameters under each one.
            foreach ($sections_with_settings_id as $section_id => $section_data) {
                echo '<h3 id=section-', $section_id, '>', $sections[$section_id]['name'], '</h3>',
                '<div class=fieldset>';

                foreach ($section_data as $sub_section_id => $setting_id) {
                    // Displays the name of the sub-section unless its ID is "no-title".
                    if ($sub_section_id !== 'no-title') {
                        echo '<h4 id=section-', $section_id, '-', $sub_section_id, '>', $sections[$section_id]['sub_sections'][$sub_section_id], '</h4>';
                    }

                    // Displays the parameter.
                    foreach ($setting_id as $setting_id_value) {
                        self::setting_rendering($setting_id_value);
                    }
                }

                echo '</div>';
            }
            ?>

            <p>
                <?php echo dcCore::app()->formNonce(); ?>

                <input name=save type=submit value="<?php echo __('Save'); ?>">

                <input class=delete name=reset value="<?php echo __('Reset all settings'); ?>" type=submit>
            </p>
        </form>

        <?php
    }
}

adminConfigAspect::save_settings();
adminConfigAspect::page_rendering();
