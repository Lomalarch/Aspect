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

use ArrayObject;
use Dotclear\App;
use Dotclear\Helper\Process\TraitProcess;
use Dotclear\Helper\Html\Html;

/**
 * @brief   The module frontend process.
 * @ingroup ductile
 */
class Frontend
{
    use TraitProcess;

    /**
     * Init the process.
     * 
     * @return bool
     */
    public static function init(): bool
    {
        // load locales
        My::l10n('public');

        return self::status(My::checkContext(My::FRONTEND));
    }

    /**
     * Processes
     * 
     * @return bool
     */
    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        # Behaviors
        App::behavior()->addBehaviors([
            'publicHeadContent'  => self::publicHeadContent(...),
            'publicFooterContent' => self::publicFooterContent(...)
        ]);

        # Templates
        App::frontend()->template()->addBlock('EntryIfContentIsCut', self::EntryIfContentIsCut(...));
        App::frontend()->template()->addValue('aspectLogo', self::aspectLogo(...));
        App::frontend()->template()->addValue('AspectSVGIcon', self::AspectSVGIcon(...));

        return true;
    }

    /**
     * Public head content behavior callback
     * 
     * @return void
     */
    public static function publicHeadContent()
    {
        $styles = App::blog()->settings()->themes->get(App::blog()->settings()->system->theme . '_styles');
        if (!empty(App::blog()->settings()->themes->get(App::blog()->settings()->system->theme . '_styles'))) {
            echo '<style>', App::blog()->settings()->themes->get(App::blog()->settings()->system->theme . '_styles'), '</style>';
        } else {
            echo '<style>#content-info h2,#site-title,.post-title,dt{font-variant:small-caps;}.post-content > p{margin:1em 0;}</style>';
        }
    }

    /**
     * Public footer content behavior callback
     *
     * @return void
     */
    public static function publicFooterContent()
    {
        $toto = App::blog()->settings()->themes->get(App::blog()->settings()->system->theme . '_footer_credits');
        if (App::blog()->settings()->themes->get(App::blog()->settings()->system->theme . '_footer_credits') != 0) {
            echo '<div class="footer-div" id="copyright"><em>', App::blog()->name, '</em> ', __('is powered by <a href="https://dotclear.org/" target="_blank">Dotclear</a> and <a href="https://dotclear.org/theme/list" target="_blank">Aspect</a>'), ' Et coucou !!!!</div>';
        }
    }

    /**
     * Tpl:aspectLogo template value
     * Displays a logo in the header.
     * 
     * @return string
     */
    public static function aspectLogo(): string
    {
        $theme = App::blog()->settings()->system->theme;
        $url = App::blog()->settings()->get('themes')->get($theme . '_header_logo_url');
        if (App::blog()->settings()->get('themes')->get($theme . '_header_logo_url')) {
            $src_image = Html::escapeURL(App::blog()->settings()->get('themes')->get($theme . '_header_logo_url'));
            $srcset    = '';

            $url_public_relative = App::blog()->settings->system->public_url;
            $public_path         = App::blog()->public_path;

            $image_size_attr = '';

            if ($src_image !== '') {
                $image_url_relative = substr($src_image, strpos($src_image, $url_public_relative));

                $image_path = $public_path . str_replace($url_public_relative . '/', '/', $image_url_relative);

                if (App::blog()->settings->aspect->header_logo_url_2x && App::blog()->settings->aspect->header_logo_url_2x !== $src_image) {
                    $src_image_2x = Html::escapeURL(App::blog()->settings->aspect->header_logo_url_2x);
                } else {
                    $src_image_2x = '';
                }

                if ($src_image_2x !== '') {
                    $srcset = ' srcset="' . $src_image . ' 1x, ' . $src_image_2x . ' 2x"';
                }

                if (getimagesize($image_path)) {
                    $image_width  = (int) getimagesize($image_path)[0];
                    $image_height = (int) getimagesize($image_path)[1];
                } else {
                    $image_width  = 0;
                    $image_height = 0;
                }

                if ($image_width > 0 && $image_height > 0) {
                    if ($image_width <= 120) {
                        $image_size_attr .= ' width="' . $image_width . '" height="' . $image_height . '"';
                    } else {
                        $image_size_attr .= ' width="120" height="' . intval($image_height * (120 / $image_width)) . '"';

                        if ($src_image_2x !== '') {
                            $image_size_attr .= ' sizes="100vw"';
                        }
                    }
                }
            }

            return '<div id=site-logo><a class="site-logo-link" href="' . Html::escapeURL(App::blog()->url) . '"><img alt="' . __('logo-img-alt') . '" class=site-logo itemprop="logo" src=' . $src_image . $srcset . $image_size_attr . '></a></div>';
        }
        return '';
    }

    /**
     * Tpl:EntryIfContentIsCut template block
     * from ductile theme
     *
     * @param      ArrayObject<string, mixed>   $attr   The attribute
     * @param      string                       $content  The content
     *
     * @return     string       rendered element
     */
    public static function EntryIfContentIsCut(ArrayObject $attr, string $content): string
    {
        if (empty($attr['cut_string']) || !empty($attr['full'])) {
            return '';
        }

        $urls = '0';
        if (!empty($attr['absolute_urls'])) {
            $urls = '1';
        }

        $short              = App::frontend()->template()->getFilters($attr);
        $cut                = $attr['cut_string'];
        $attr['cut_string'] = 0;
        $full               = App::frontend()->template()->getFilters($attr);
        $attr['cut_string'] = $cut;

        return '<?php if (strlen(' . sprintf($full, 'App::frontend()->context()->posts->getContent(' . $urls . ')') . ') > ' .
        'strlen(' . sprintf($short, 'App::frontend()->context()->posts->getContent(' . $urls . ')') . ')) : ?>' .
            $content .
            '<?php endif; ?>';
    }

    /**
     * Tpl:AspectSVGIcon template value
     * SVGicon for RSS
     *
     * @param ArrayObject $attr
     * @return string
     */
    public static function AspectSVGIcon(ArrayObject $attr): string
    {
        $path = '';

        if ( isset( $attr['path'] ) && !empty( $attr['path'] ) ) {
            $path = str_replace( '\'', '"', $attr['path'] );
        }

        $class = 'feather';

        if ( isset( $attr['class'] ) && !empty( $attr['class'] ) ) {
            $class = $attr['class'];
        }

        $viewbox = '0 0 24 24';

        if ( isset( $attr['viewbox'] ) && !empty( $attr['viewbox'] ) ) {
            $viewbox = $attr['viewbox'];
        }

        return '<svg class="' . $class . '" xmlns="http://www.w3.org/2000/svg" viewBox="' . $viewbox . '">' . $path . '</svg>';
    }
}
