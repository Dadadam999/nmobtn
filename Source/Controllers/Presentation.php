<?php

/**
 * @package nmobtn
 * @author Bogdanov Andrey (swarzone2100@yandex.ru)
 */

namespace nmobtn\Controllers;

use nmobtn\DataBase;

class Presentation
{
    public function __construct()
    {
        $this->shortcodeManange();
        $this->postCallback();
        $this->buttonOnEventShortcode();
        $this->shortcodeShowName();
    }

    private function shortcodeManange()
    {
        add_shortcode('nmo-manange-presentation', function ($atts, $content) {
            $atts = shortcode_atts([
                'event_id' => '',
            ], $atts);

            $user_info_per = get_userdata(get_current_user_id());

            if ($user_info_per->user_level >= 7) {
                $event_id = $atts['event_id'];
                ob_start();
                include  WP_PLUGIN_DIR . '/nmobtn/Source/Templates/EditPresentation.php';
                echo ob_get_clean();
            }

            return '';
        });
    }

    private function shortcodeShowName()
    {
        add_shortcode('nmo-show-presentation', function ($atts, $content) {
            $atts = shortcode_atts([
                'event_id' => '',
            ], $atts);

            $presentation = DataBase::$tables['presentations']->GetNow($atts['event_id'], date("Y-m-d H:i:s"));

            if (empty($presentation))
                return '';

            return '<div style="margin-top:10px;margin-bottom:10px;">
                    <h5>Симпозиум:</h5>
                    <div>' . $presentation['name'] . '</div>
                    <h5 class="bold">Модератор:</h5>
                    <div>' . $presentation['lector'] . '</div>
                    </div>';
        });
    }

    private function buttonOnEventShortcode()
    {
        add_shortcode('nmo-button-on-event', function ($atts, $content) {
            $atts = shortcode_atts([
                'event_id' => '',
                'css-class' => '',
                'css-class-title' => '',
                'css-class-name' => '',
                'css-class-lector' => '',
                'url' => '',
                'title' => '',
                'show_lector' => '',
            ], $atts);

            if (empty($atts['event_id'])) {
                return '<p class="error">Не указан ID мероприятия!</p>';
            }

            if (empty($atts['url'])) {
                return '<p class="error">Не указан URL мероприятия!</p>';
            }

            $html_class = htmlspecialchars($atts['css-class']);
            $html_class_title = htmlspecialchars($atts['css-class-title']);
            $html_class_name = htmlspecialchars($atts['css-class-name']);
            $html_class_lector = htmlspecialchars($atts['css-class-lector']);
            date_default_timezone_set('Europe/Moscow');
            $presentation = DataBase::$tables['presentations']->GetNow($atts['event_id'], date("Y-m-d H:i:s"));
            $event = DataBase::$tables['events']->Get($atts['event_id']);

            if (empty($atts['title'])) {
                $atts['title'] = $event['name'];
            }

            $html = '<a class="' . $html_class . '" href="' . $atts['url'] . '">';
            $html .= '<div class="' . $html_class_title . '">' . $atts['title'] . '</div>';

            if (!empty($presentation)) {
                $html .= '<div class="' . $html_class_name . '">' . $presentation['name'] . '</div>';
            }

            if ($atts['show_lector'] == 'true') {
                $html .= '<div class="' . $html_class_lector . '">' . $presentation['lector'] . '</div>';
            }

            $html .= '</a>';

            return $html;
        });
    }

    private function postCallback()
    {
        add_action('plugins_loaded', function () {
            if (!empty($_POST['nmobtnEditPresentations-wpnp'])) {
                $model = DataBase::$tables['presentations']->Get($_POST['nmobtn-name']);
                $id = $model['id'];
                $number = $model['number'];
                $name = $model['name'];
                $lector = $model['lector'];
                $event_id = $model['event_id'];
                $date_start = date("Y-m-d H:i:s", strtotime("{$_POST['nmobtn-date_start']} {$_POST['nmobtn-start-time-hours']}:{$_POST['nmobtn-start-time-minutes']}"));
                $date_end = date("Y-m-d H:i:s", strtotime("{$_POST['nmobtn-date_end']} {$_POST['nmobtn-end-time-hours']}:{$_POST['nmobtn-end-time-minutes']}"));
                DataBase::$tables['presentations']->Update($id, $number, $name, $lector, $event_id, $date_start, $date_end);
            }
        });
    }
}
