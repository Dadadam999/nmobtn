<?php
/**
 * Plugin Name: nmobtn
 * Plugin URI: https://github.com/
 * Description: Плагин, для создания кнопок НМО на любой странице. Кнопка вызывается у пользователей вручную. Есть возможность выгрузить статистику нажатий в CSV.
 * Version: 1.0.0
 * Author: Bogdanov Andrey
 * Author URI: mailto://swarzone2100@yandex.ru
 *
 * @package Кнопка НМО
 * @author Bogdanov Andrey (swarzone2100@yandex.ru)
 * @since 1.0.9
*/
require_once __DIR__.'/nmobtn-autoload.php';

use nmobtn\DataBase;
use nmobtn\Main;

register_activation_hook(__FILE__, 'Install');
register_deactivation_hook(__FILE__, 'Uninstall');

function Install()
{
  $tables = new DataBase();
  $tables->Install();
}

function Uninstall()
{
  $tables = new DataBase();
  $tables->Uninstall();
}

new Main();
