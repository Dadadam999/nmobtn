<?php
/**
 * @package nmobtn
 * @author Bogdanov Andrey (swarzone2100@yandex.ru)
 */
namespace nmobtn;
use nmobtn\DataBase;
use nmobtn\Admin\SettingsPage;
use WP_REST_Request;

class Main
{
    private $database;

    public function __construct()
    {
        new SettingsPage;
        $this->database = new DataBase;
        $this->trackVisit();
        $this->apiInit();
        $this->scriptAdd();
        $this->buttonShortcode();
        $this->chatShortcode();
        $this->onlineShortcode();
        $this->memberShortCode();
        $this->memberMessageShortcode();
        $this->subsbyEventCheckShortCode();
    }

    private function getAudience( $event_id )
    {
      return $this->database->wpdb->get_results(
          "SELECT `subscribers`
           FROM `" . $this->database->wpdb->prefix . "subsbu_audience`
           WHERE `post_id` = " . $event_id,
           ARRAY_A
      )[0]['subscribers'];
    }


    private function subsbyEventCheckShortCode()
    {
        add_shortcode( 'nmo-subsbu-event-check', function( $atts )
        {
            $atts = shortcode_atts([
                'url' => '',
                'event_id' => ''
            ], $atts);

            if( $atts[ 'url' ] === '' || $atts[ 'event_id' ] === '' )
                return 'Данные не заполненые шорткода nmo-subsbu-event-check не заполнены!';

            $html = "<script>document.location.href = '{$atts[ 'url' ]}'</script>";
            $audience = $this->getAudience( $atts[ 'event_id' ] );

             if( in_array( get_current_user_id(), explode(';', $audience) ) && is_user_logged_in() )
                 return '';

            wp_redirect($atts[ 'url' ]);
            exit;
            return;
        });
    }

    private function memberShortCode()
    {
        add_shortcode( 'unmember', function( $content )
        {
            if ( !is_user_logged_in() && !is_feed() )
              return do_shortcode($content);

            return '';
        });
    }

    private function memberMessageShortcode()
    {
        add_shortcode( 'member-message', function( $content )
        {
            if( is_user_logged_in() && !is_null( $content ) && !is_feed() )
                return do_shortcode($content);

            return do_shortcode('[us_message color="red" icon="fas|user-secret"]</p><p>Для просмотра <b><u><span style="cursor: pointer;" class="lrm-login"><a href="/login-page/">авторизуйтесь</a></span></u> или <u><span style="cursor: pointer;" class="lrm-register"><a href="/registration/">зарегистрируйтесь</a></span></u></b> на&nbsp;сайте!</p>[/us_message]');
        });
    }

    private function trackVisit()
    {
        add_action('template_redirect', function()
        {
            $user_id = get_current_user_id();
            $response_code = http_response_code();

            if ( !empty($user_id) && (int)$response_code < 400 )
            {
                $url = ( empty($_SERVER['HTTPS']) ? 'http' : 'https' ) . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                $this->database->tables['visits']->Add( $user_id, $url );
            }
        });
    }

    private function scriptAdd()
    {
        wp_enqueue_style( 'shortcodes', plugins_url('nmobtn/assets/css/shortcodes.css') );

        add_action('wp_enqueue_scripts', function()
        {
            wp_enqueue_script(
                'nmobtn-client',
                plugins_url('nmobtn/assets/js/nmobtn-client.js'),
                [],
                '0.1.5'
            );
        });
    }

    private function chatShortcode()
    {
        add_shortcode('nmo-chat', function($atts, $content)
        {
            $atts = shortcode_atts([
                'event_id' => '',
            ], $atts);

            $user_id = get_current_user_id();
            $user_name =  get_user_meta( $user_id, 'lastname', true ) . ' ' .get_user_meta( $user_id, 'firstname', true );

            if(  $user_name === ' ' )
                $user_name = wp_get_current_user()->display_name;

            $event_id = $atts['event_id'];
            $html_md5 = md5('nmobtn-chat-' . $event_id . '-' . $user_id);
            $html_send = 'onclick="nmobtnClient.chatsend(\'' . $event_id . '\', \'' . $user_id . '\', \'' . $user_name . '\', \'' . $html_md5 . '\')"';
            $messages = $this->database->tables['chat']->GetToEvent($event_id);

            $html = '<script>
                        setInterval( () => {
                          nmobtnClient.chatcheck(\'' . $event_id . '\', \'' . $user_id . '\');
                        }, 60 * 1000);
                      </script>';

            $html .='<div class="container-chat">';
            $html .='<ul id="chat-messages" class="chat-messages">';

            if( empty( $messages) )
                $html .='<li id="chat-empty-text">Сообщения пока отсутсвуют!</li>';
            else
                foreach ($messages as $message)
                    $html .='<li id="' . $message['id'] . '" class="message"  write-date="'. $message['write_date'] . '"><span id="name' . $message['id'] . '" class="name-message '. ($user_name === $message['user_name'] ? 'name-user' : '') . '" onClick="nmobtnClient.answering(\'name' .  $message['id'] . '\')">' . $message['user_name'] . '</span><br><span class="message-text">' . $message['message'] . '</span></li>';

            $html .='</ul>';

            $html .='<div id="username-chat" class="username-chat">'. $user_name .'</div>';
            $html .='<div class="msg-box">';
            $html .='<input type="text" id="area-msg-chat" class="area-msg" placeholder="Напишите сообщение и нажмите Отправить" />';
            $html .='<button id="btn-msg-chat" class="btn-msg" ' . $html_send . ' >Отправить</button>';
            $html .='</div>';

            $html .='</div>';

            return $html;
        });
    }

    private function onlineShortcode()
    {
        add_shortcode('nmo-online', function($atts, $content)
        {
            $atts = shortcode_atts([
                'event_id' => '',
            ], $atts);

            $user_info_per = get_userdata( get_current_user_id() );

            if ($user_info_per->user_level >= 7)
            {
              $online = $this->database->tables['eventUsers']->GetCount( $atts['event_id'] );
              return '<div id="online-event" class="online-event">Всего сейчас онлайн: '. $online .'</div>';
            }

            return '';
        });
    }

    private function buttonShortcode()
    {
        add_shortcode('nmo-button', function($atts, $content)
        {
            $atts = shortcode_atts([
                'event_id' => '',
                'event_name' => '',
                'css-class' => '',
                'css-style' => ''
            ], $atts);

            $post = $this->database->tables['events']->Get($atts['event_id']);

            $user_id = get_current_user_id();

            if(empty($post))
                $this->database->tables['events']->Add($atts['event_id'], $atts['event_name']);

            if (empty($user_id))
                return;

            $post_id = $post['event_id'];
            $this->database->tables['eventUsers']->Delete($user_id); //?
            $this->database->tables['eventUsers']->Add($post_id, $user_id); //?
            $html_id = htmlspecialchars('nmobtn-presence-button');
            $html_style = htmlspecialchars($atts['css-style']);
            $html_class = htmlspecialchars($atts['css-class']);
            $html_md5 = md5('nmobtn-button-' . $post_id . '-' . $user_id);

            $html = '';
            $html .= '<script>
                        setInterval( () => {
                          nmobtnClient.checknmo(\'' . $post_id . '\', \'' . $user_id . '\');
                        },  60 * 1000);
                      </script>';

            $style_btn = 'style="padding: 5px; margin: 5px; background: #ff2b2b; color: white;"';

            $html .='
            <div id="tytoknoall" class="tytoknoall"></div>
            <div id="blockcentr" class="blockcentr" style="display: none">
            <label id="closeokno" class="closeokno" onclick="nmobtnClient.close();" style="cursor:pointer;float:right;color:#188bd2;">Закрыть</label>
            <span>Подтвердите свое присутствие!</span>
            <button '. $style_btn .' type="button" id="' . $html_id . '" class="nmo-accept ' . $html_class . '" style="' . $html_style . '" onclick="nmobtnClient.click(\'' . $html_id . '\', \'' . $post_id . '\', \'' . $user_id . '\', \'' . $html_md5 . '\');">
            <span id="' . $html_id . '-content-0">Подтвердите присутствие</span>
            <span id="' .  $html_id . '-content-1" style="display: none;">Подтверждено</span>
            </button>
            </div>';

            $user_info_per = get_userdata( get_current_user_id() );
            if ($user_info_per->user_level >= 7) {
              $html .= '
                <button type="button" id="send-nmo" class="send-nmo ' . $html_class . '" onclick="nmobtnClient.sendnmo(\'' . $post_id . '\');">
                <span id="send-nmo-span">Разослать уведомления НМО</span>
                </button>
              ';
            }
            return $html;
        });
    }

    private function apiInit()
    {
        add_action('rest_api_init', function()
        {
            register_rest_route(
                'nmobtn/v1',
                '/sendnmo',
                [
                    'methods' => 'POST',
                    'callback' => function(WP_REST_Request $request)
                    {
                        $post_id = (int)$request->get_param('nmobtn-button-event');

                        if (empty($post_id))
                            return [
                                'code' => -99,
                                'message' => 'Too few arguments for this argument.'
                            ];

                        $users = $this->database->tables['eventUsers']->Get($post_id);

                        foreach ($users as $user)
                           $this->database->tables['previewCheck']->Add($post_id, $user['user_id']);

                        return [
                            'code' => 0,
                            'message' => 'Success.'
                        ];
                    }
                ]
            );

            register_rest_route(
                'nmobtn/v1',
                '/checknmo',
                [
                    'methods' => 'POST',
                    'callback' => function(WP_REST_Request $request)
                    {
                        $post_id = (int)$request->get_param('nmobtn-button-event');
                        $user_id = (int)$request->get_param('nmobtn-button-user');

                        if (empty($post_id) || empty($user_id))
                            return [
                                'code' => -99,
                                'message' => 'Too few arguments for this argument.'
                            ];

                        $status_click = $this->database->tables['previewCheck']->Get($post_id, $user_id);
                        $current_track = $this->database->tables['track']->Get( $post_id, $user_id );

                        if( empty( $current_track ) )
                             $this->database->tables['track']->Add( $post_id, $user_id );
                        else
                             $this->database->tables['track']->Update( $post_id, $user_id );

                        return [
                            'code' => 0,
                            'message' => 'Success.',
                            'status' => $status_click[0]['click']
                        ];

                    }
                ]
            );

            register_rest_route(
                'nmobtn/v1',
                '/click',
                [
                    'methods' => 'POST',
                    'callback' => function(WP_REST_Request $request)
                    {
                        $post_id = (int)$request->get_param('nmobtn-button-event');
                        $user_id = (int)$request->get_param('nmobtn-button-user');

                        if (empty($post_id) || empty($user_id))
                            return [
                                'code' => -99,
                                'message' => 'Too few arguments for this argument.'
                            ];

                        $this->database->tables['previewCheck']->Delete($post_id, $user_id);
                        $this->database->tables['presence']->Add($post_id, $user_id);

                        return [
                            'code' => 0,
                            'message' => 'Success.'
                        ];

                    },
                    'permission_callback' => function(WP_REST_Request $request) {
                        return $request->get_param('nmobtn-button-key') === md5('nmobtn-button-' . $request->get_param('nmobtn-button-event') . '-' . $request->get_param('nmobtn-button-user'));
                    }
                ]
            );

            register_rest_route(
                'nmobtn/v1',
                '/chatcheck',
                [
                    'methods' => 'POST',
                    'callback' => function(WP_REST_Request $request)
                    {
                        $event_id = (int)$request->get_param('nmobtn-chat-event');
                        $user_id = (int)$request->get_param('nmobtn-chat-user');
                        $last_date = $request->get_param('nmobtn-chat-last');

                        if ( empty($event_id) || empty($user_id) || empty($last_date) )
                            return [
                                'code' => -99,
                                'message' => 'Too few arguments for this argument.',
                                'messages' => []
                            ];

                        $messages = $this->database->tables['chat']->GetLastWithoutUser($event_id, $user_id, $last_date);

                        return [
                            'code' => 0,
                            'message' => 'Success.',
                            'messages' => $messages
                        ];
                    }
                ]
            );

            register_rest_route(
                'nmobtn/v1',
                '/chatsend',
                [
                    'methods' => 'POST',
                    'callback' => function(WP_REST_Request $request)
                    {
                        $event_id = (int)$request->get_param('nmobtn-chat-event');
                        $user_id = (int)$request->get_param('nmobtn-chat-user');
                        $user_name = $request->get_param('nmobtn-chat-name');
                        $message = $request->get_param('nmobtn-chat-message');

                        if ( empty($event_id) || empty($user_id) || empty($message) )
                            return [
                                'code' => -99,
                                'message' => 'Too few arguments for this argument.'
                            ];

                        $this->database->tables['chat']->Add($event_id, $user_id, $user_name, $message);
                        $last_messge = $this->database->tables['chat']->GetToUserLast($event_id, $user_id);

                        return [
                            'code' => 0,
                            'message' => 'Success.',
                            'id' => $last_messge['id'],
                            'date' => $last_messge['write_date'],
                        ];
                    },
                    'permission_callback' => function(WP_REST_Request $request) {
                        return $request->get_param('nmobtn-chat-key') === md5('nmobtn-chat-' . $request->get_param('nmobtn-chat-event') . '-' . $request->get_param('nmobtn-chat-user'));
                    }
                ]
            );
        });
    }
}
