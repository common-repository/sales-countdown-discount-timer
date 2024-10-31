<?php
namespace MetSalesCountdown\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * Description: This class handle various utility functions.
 *
 * @package    MetSalesCountdown\Helper
 * @subpackage Utility Class
 * @since      1.0.0
 */
class Utils {

    public static function get_kses_array()
    {
        return array(
            'html'                          => array(),
            'head'                          => array(),
            'body'                          => array(),
            'hr'                            => array(),
            'address'                       => array(),
            'a'                             => array(
                'class'  => array(),
                'href'   => array(),
                'rel'    => array(),
                'title'  => array(),
                'target' => array(),
                'style'  => array(),
                'id'     => array(),
            ),
            'abbr'                          => array(
                'title' => array(),
                'style'  => array(),
            ),
            'b'                             => array(
                'class' => array(),
                'style'  => array(),
            ),
            'blockquote'                    => array(
                'cite' => array(),
                'style'  => array(),
            ),
            'cite'                          => array(
                'title' => array(),
                'style'  => array(),
            ),
            'code'                          => array(
                'style'  => array(),
            ),
            'pre'                           => array(
                'style'  => array(),
            ),
            'del'                           => array(
                'datetime' => array(),
                'title'    => array(),
                'style'  => array(),
            ),
            'dd'                            => array(
                'style'  => array(),
            ),
            'div'                           => array(
                'class' => array(),
                'title' => array(),
                'style' => array(),
                'id' => array(),
                'data-total-stock' => array(),
                'data-available-stock' => array(),
                'data-metsales-schedules' => array(),
            ),
            'dl'                            => array(
                'style' => array(),
            ),
            'dt'                            => array(
                'style' => array(),
            ),
            'em'                            => array(
                'style' => array(),
            ),
            'strong'                        => array(
                'style' => array(),
            ),
            'h1'                            => array(
                'class' => array(),
                'style' => array(),
            ),
            'h2'                            => array(
                'class' => array(),
                'style' => array(),
                'id' => array(),
            ),
            'h3'                            => array(
                'class' => array(),
                'style' => array(),
            ),
            'h4'                            => array(
                'class' => array(),
                'style' => array(),
            ),
            'h5'                            => array(
                'class' => array(),
                'style' => array(),
            ),
            'h6'                            => array(
                'class' => array(),
                'style' => array(),
            ),
            'i'                             => array(
                'class' => array(),
                'style' => array(),
            ),
            'img'                           => array(
                'alt'        => array(),
                'class'        => array(),
                'height'    => array(),
                'src'        => array(),
                'width'        => array(),
                'style'        => array(),
                'title'        => array(),
                'srcset'    => array(),
                'loading'    => array(),
                'sizes'        => array(),
                'style' => array(),
            ),
            'figure'                        => array(
                'class'        => array(),
                'style' => array(),
            ),
            'li'                            => array(
                'class' => array(),
                'style' => array(),
                'id' => array(),
                'title' => array(),
                'data-select2-id' => array(),
            ),
            'ol'                            => array(
                'class' => array(),
                'style' => array(),
            ),
            'p'                             => array(
                'class' => array(),
                'style' => array(),
            ),
            'q'                             => array(
                'cite'  => array(),
                'title' => array(),
                'style' => array(),
            ),
            'span'                          => array(
                'class' => array(),
                'id' => array(),
                'title' => array(),
                'style' => array(),
                'aria-haspopup' => array(),
                'role' => array(),
                'aria-expanded' => array(),
                'tabindex' => array(),
                'aria-disabled' => array(),
                'dir' => array(),
                'data-select2-id' => array(),
                'aria-hidden' => array(),
            ),
            'iframe'                        => array(
                'width'       => array(),
                'height'      => array(),
                'scrolling'   => array(),
                'frameborder' => array(),
                'allow'       => array(),
                'src'         => array(),
                'style' => array(),
            ),
            'strike'                        => array(),
            'br'                            => array(),
            'table'                         => array(),
            'thead'                         => array(),
            'tbody'                         => array(
                'width'       => array(),
                'height'      => array(),
                'scrolling'   => array(),
                'frameborder' => array(),
                'allow'       => array(),
                'src'         => array(),
                'style' => array()
            ),
            'tfoot'                         => array(),
            'tr'                            => array(
                'width'       => array(),
                'height'      => array(),
                'scrolling'   => array(),
                'frameborder' => array(),
                'allow'       => array(),
                'src'         => array(),
                'style' => array()
            ),
            'th'                            => array(),
            'td'                            => array(),
            'colgroup'                      => array(),
            'col'                           => array(),
            'strong'                        => array(),
            'data-wow-duration'             => array(),
            'data-wow-delay'                => array(),
            'data-wallpaper-options'        => array(),
            'data-stellar-background-ratio' => array(),
            'ul'                            => array(
                'class' => array(),
                'id'    => array(),
                'style' => array(),
            ),
            'svg'                           => array(
                'class'           => true,
                'aria-hidden'     => true,
                'aria-labelledby' => true,
                'role'            => true,
                'xmlns'           => true,
                'width'           => true,
                'height'          => true,
                'viewbox'         => true, // <= Must be lower case!
                'preserveaspectratio' => true,
            ),
            'g'                             => array('fill' => true),
            'title'                         => array('title' => true),
            'path'                          => array(
                'd'    => true,
                'fill' => true,
            ),
            'progress'                      => array(
                'value' => true,
                'max'   => true,
            ),
            'input'                            => array(
                'class'        => array(),
                'type'        => array(),
                'value'        => array(),
                'name'        => array(),
                'id'        => array(),
                'min'        => array(),
                'max'        => array(),
                'step'        => array(),
                'placeholder' => array(),
                'style' => array(),
                'required' => array(),
                'readonly' => array(),
                'disabled' => array(),
                'checked' => array(),
                'multiple' => array(),
                'data-default-color' => array(),
                'pattern' => array(),
                'maxlength' => array(),
			),
            'fieldset' => array(
                'class' => array(),
            ),
            'label' => array(
                'for' => array(),
            ),
            'select' => array(
                'class' => array(),
                'name' => array(),
                'id' => array(),
                'style' => array(),
                'required' => array(),
                'multiple' => array(),
                'tabindex' => array(),
                'data-select2-id' => array(),
                'aria-hidden' => array(),
            ),
            'option' => array(
                'value' => array(),
                'selected' => array(),
                'disabled' => array(),
                'data-select2-id' => array(),
            ),
            'textarea' => array(
                'class' => array(),
                'name' => array(),
                'id' => array(),
                'style' => array(),
                'required' => array(),
                'readonly' => array(),
                'disabled' => array(),
                'cols' => array(),
                'rows' => array(),
                'placeholder' => array(),
                'maxlength' => array(),
                'tabindex' => array(),
                'type' => array(),
                'autocorrect' => array(),
                'autocapitalize' => array(),
                'spellcheck' => array(),
                'role' => array(),
                'aria-autocomplete' => array(),
                'autocomplete' => array(),
                'aria-label' => array(),
                'aria-describedby' => array(),
            ),
			'button' => [
				'class' => array(),
                'type' => array(),
				'title' => array(),
				'data-share-url' => array(),
				'data-message' => array(),
                'tabindex' => array(),
                'aria-label' => array(),
                'aria-describedby' => array(),
			]
        );
    }

    public static function kses($raw)
    {

        $allowed_tags = self::get_kses_array();

        if (function_exists('wp_kses')) { // WP is here
            return wp_kses($raw, $allowed_tags);
        } else {
            return $raw;
        }
    }
}