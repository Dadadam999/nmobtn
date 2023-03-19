<?php
/**
* @package nmobtn
* @author Bogdanov Andrey (swarzone2100@yandex.ru)
*/
namespace nmobtn\Admin\Controllers;
use nmobtn\DataBase;

class Settings
{
    public function __construct()
    {
        add_action('admin_menu', function()
        {
            add_submenu_page(
                'settings_nmobtn',
                'Настройки',
                'Настройки',
                'administrator',
                'settings_nmobtn_3',
                array($this, 'nmobtnSettingsConfigCallback')
            );
        });
    }

    function nmobtnSettingsConfigCallback()
    {
        ob_start();
        include  WP_PLUGIN_DIR . '/nmobtn/Source/Admin/View/Settings.php';
        echo ob_get_clean();
    }
}
