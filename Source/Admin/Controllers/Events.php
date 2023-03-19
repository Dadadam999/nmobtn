<?php
/**
* @package nmobtn
* @author Bogdanov Andrey (swarzone2100@yandex.ru)
*/
namespace nmobtn\Admin\Controllers;
use nmobtn\DataBase;

class Events
{
    private $database;

    public function __construct()
    {
        add_action('admin_menu', function()
        {
            add_menu_page(
                'НМО',
                'Плагин НМО',
                'administrator',
                'settings_nmobtn',
                array($this, 'nmobtnEventsCallback'),
                'dashicons-admin-generic',
                20
            );
        });
    }

    public function nmobtnEventsCallback()
    {
       ob_start();
       include  WP_PLUGIN_DIR . '/nmobtn/Source/Admin/View/Events.php';
       echo ob_get_clean();
    }
}
?>
