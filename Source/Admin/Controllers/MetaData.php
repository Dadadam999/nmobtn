<?php
/**
* @package nmobtn
* @author Bogdanov Andrey (swarzone2100@yandex.ru)
*/
namespace nmobtn\Admin\Controllers;
use nmobtn\DataBase;

class MetaData
{
    public function __construct()
    {
        add_action('admin_menu', function()
        {
            add_submenu_page(
                'settings_nmobtn',
                'Мета поля шаблонов',
                'Мета поля',
                'administrator',
                'settings_nmobtn_1',
                array($this, 'nmobtnSettingsUsermetaCallback')
            );
        });

        $this->postCallback();
    }

    public function nmobtnSettingsUsermetaCallback()
    {
        ob_start();
        include  WP_PLUGIN_DIR . '/nmobtn/Source/Admin/View/MetaData.php';
        echo ob_get_clean();
    }

    private function postCallback()
    {
        add_action('plugins_loaded', function()
        {
            if( !empty( $_POST['nmobtnUserMetaAddNonce'] ) )
            {
                if( !isset( $_POST['nmobtn-usermeta-select-add'] ) && !isset( $_POST['nmobtn-name-usermeta-add'] ) )
                {
                    echo 'Поля не выбраны!';
                    return '';
                }

                $meta_key_saved = json_decode( DataBase::$tables['settings']->Get('template_usermeta'), true );
                $meta_key_saved += [ $_POST['nmobtn-usermeta-select-add'] => $_POST['nmobtn-name-usermeta-add'] ];
                DataBase::$tables['settings']->Update( 'template_usermeta', json_encode( $meta_key_saved, JSON_UNESCAPED_UNICODE ) );
            }

            if( !empty( $_POST['nmobtnUserMetaRemoveNonce'] ) )
            {
                if( !isset( $_POST['nmobtn-usermeta-select-remove'] ) )
                {
                    echo 'Поля не выбраны!';
                    return '';
                }

                $meta_key_saved = json_decode( DataBase::$tables['settings']->Get('template_usermeta'), true );
                unset( $meta_key_saved[ $_POST['nmobtn-usermeta-select-remove'] ] );
                DataBase::$tables['settings']->Update( 'template_usermeta', json_encode( $meta_key_saved, JSON_UNESCAPED_UNICODE ) );
            }
        });
    }
}
