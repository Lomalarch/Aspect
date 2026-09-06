<?php
/**
 * @package     Dotclear
 * @subpackage  Theme
 *
 * @author      Théodore, Noé
 * @copyright   Théodore
 * @copyright   AGPL-3.0
 */
declare(strict_types=1);

namespace Dotclear\Theme\aspect;

use Dotclear\App;
use Dotclear\Helper\Html\Form\Checkbox;
use Dotclear\Helper\Html\Form\Fieldset;
use Dotclear\Helper\Html\Form\Input;
use Dotclear\Helper\Html\Form\Label;
use Dotclear\Helper\Html\Form\Legend;
use Dotclear\Helper\Html\Form\Para;
use Dotclear\Helper\Html\Form\Select;
use Dotclear\Helper\Html\Form\Set;
use Dotclear\Helper\Html\Form\Text;
use Dotclear\Helper\Process\TraitProcess;
use Exception;

/**
 * @brief   The module configuration process.
 * @ingroup aspect
 */
class Config
{
    use TraitProcess;

    /**
     * Initializes processes
     *
     * @return boolean
     */
    public static function init(): bool
    {
        // limit to backend permissions
        if (!self::status(My::checkContext(My::CONFIG))) {
            return false;
        }

        // load locales
        My::l10n('public');

        // Load contextual help
        // App::themes()->loadModuleL10Nresources(My::id(), App::lang()->getLang());

        $aspect_user = App::backend()->aspect_user;

        // Stored config values
        App::backend()->standalone_config = (bool) App::themes()->moduleInfo(App::blog()->settings()->system->theme, 'standalone_config');
        $aspect_user['header_logo_url'] = App::blog()->settings()->themes->get(App::blog()->settings()->system->theme . '_header_logo_url');
        $aspect_user['header_logo_url_2x'] = App::blog()->settings()->themes->get(App::blog()->settings()->system->theme . '_header_logo_url_2x');
        $aspect_user['content_style'] = App::blog()->settings()->themes->get(App::blog()->settings()->system->theme . '_content_style');
        $aspect_user['content_title_style'] = App::blog()->settings()->themes->get(App::blog()->settings()->system->theme . '_content_title_style');
        $aspect_user['footer_credits'] = App::blog()->settings()->themes->get(App::blog()->settings()->system->theme . '_footer_credits');
        // App::backend()->styles = App::blog()->settings()->themes->get(App::blog()->settings()->system->theme . '_styles');

         App::backend()->aspect_user = $aspect_user;

        return true;
    }

    /**
     * Process submitted data
     *
     * @return boolean
     */
    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        if (!empty($_POST)) {
            try
            {
                # HTML
                $aspect_user = App::backend()->aspect_user;

                $aspect_user['header_logo_url'] = $_POST['header_logo_url'] ?? '';
                $aspect_user['header_logo_url_2x']  = $_POST['header_logo_url_2x'];
                $aspect_user['content_style'] = $_POST['content_style'];
                $aspect_user['content_title_style'] = $_POST['content_title_style'];
                $aspect_user['footer_credits'] = $_POST['footer_credits'] ?? 0;
                // $aspect_user['styles'] = $_POST['styles'];

                $css = '';

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
                    App::blog()->settings()->themes->put(App::blog()->settings()->system->theme . '_styles', str_replace('&gt;', '>', htmlspecialchars($css, ENT_NOQUOTES)));
                    // App::blog()->settings->aspect->put(
                    //     'styles',
                    //     str_replace('&gt;', '>', htmlspecialchars($css, ENT_NOQUOTES)),
                    //     'string',
                    //     $default_settings['styles']['title'],
                    //     true
                    // );
                } else {
                    App::blog()->settings()->themes->drop(App::blog()->settings()->system->theme . '_styles');
                }

                App::backend()->aspect_user = $aspect_user;

                App::blog()->settings()->addWorkspace('themes');
                App::blog()->settings()->themes->put(App::blog()->settings()->system->theme . '_header_logo_url', App::backend()->aspect_user['header_logo_url']);
                App::blog()->settings()->themes->put(App::blog()->settings()->system->theme . '_header_logo_url_2x', App::backend()->aspect_user['header_logo_url_2x']);
                App::blog()->settings()->themes->put(App::blog()->settings()->system->theme . '_content_style', App::backend()->aspect_user['content_style']);
                App::blog()->settings()->themes->put(App::blog()->settings()->system->theme . '_content_title_style', App::backend()->aspect_user['content_title_style']);
                App::blog()->settings()->themes->put(App::blog()->settings()->system->theme . '_footer_credits', App::backend()->aspect_user['footer_credits']);
                // App::blog()->settings()->themes->put(App::blog()->settings()->system->theme . '_styles', App::backend()->aspect_user['styles']);

                // Blog refresh
                App::blog()->triggerBlog();

                // Template cache reset
                App::cache()->emptyTemplatesCache();

                App::backend()->notices()->message(__('Theme configuration upgraded.'), true, true);
            } catch (Exception $e) {
               App::error()->add($e->getMessage());
            }
        }

        return true;
    }

    /**
     * Displays config page for theme
     *
     * @return void
     */
    public static function render(): void
    {
        if (!self::status()) {
            return;
        }

        $footer_credits = App::backend()->aspect_user['footer_credits'];

        // Page content
        echo (new Set())
            ->items([
                (new Fieldset())
                    ->legend((new Legend(__('Header Settings'))))
                    ->items([
                        (new Para('header_logo_url_input'))
                            ->items([
                                (new Input('header_logo_url'))
                                    ->label((new Label(__('Logo URL'), Label::OL_TF)))
                                    ->placeholder('https://…')
                                    ->value(App::backend()->aspect_user['header_logo_url'])
                                    ->size(30)
                                    ->maxlength(255)
                            ]),
                        (new Para('header_logo_url_input'))
                            ->items([
                                (new Input('header_logo_url_2x'))
                                    ->label((new Label(__('Dual pixel density logo URL'), Label::OL_TF)))
                                    ->placeholder('https://…')
                                    ->value(App::backend()->aspect_user['header_logo_url_2x'])
                                    ->size(30)
                                    ->maxlength(255)
                           ]),
                        (new Text('p', __('To ensure proper display on dual pixel density displays (Retina), please provide an image that is twice the size of the standard image.')))
                            ->class('form-note')
                    ]),
                (new Fieldset())
                    ->legend((new Legend(__('Content Settings'))))
                    ->items([
                        (new Para('content_style-select'))
                            ->items([
                                (new Select('content_style'))
                                    ->label((new Label(__('Style of post content'), Label::OL_TF)))
                                    ->items([
                                        __('Standard (default)') => 'standard',
                                        __('Roman')              => 'roman'
                                    ])
                                    ->default(!empty(App::backend()->aspect_user['content_style']) ? App::backend()->aspect_user['content_style'] : 'standard'),
                            ]),
                        (new Para('content_title_style-select'))
                            ->items([
                                (new Select('content_title_style'))
                                    ->label((new Label(__('Appearance of titles'), Label::OL_TF)))
                                    ->items([
                                        __('Standard')             => 'standard',
                                        __('Small caps (default)') => 'small-caps'
                                    ])
                                    ->default(!empty(App::backend()->aspect_user['content_title_style']) ? App::backend()->aspect_user['content_title_style'] : 'small-caps')
                            ])
                    ]),
                (new Fieldset())
                    ->legend((new Legend(__('Footer Settings'))))
                    ->items([
                        (new Para('footer_credits-input'))
                            ->items([
                                (new Checkbox('footer_credits', App::backend()->aspect_user['footer_credits'] != 0))
                            ->label((new Label(__('Display a mention to Dotclear and the theme'), Label::IL_FT)))
                            ]),
                        (new Text('p', __('Allows you to advertise Dotclear and this theme.')))
                            ->class('form-note')
                            ->id('footer_credits-description')
                    ])
            ])
            ->render();
    }

    /**
     * Converts a style array into a minified style string.
     *
     * @param array $rules An array of styles.
     *
     * @return string $css The minified styles.
     */
    private static function styles_array_to_string($rules)
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
}
