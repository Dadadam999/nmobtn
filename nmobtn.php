<?php
/**
 * Plugin Name: nmobtn
 * Plugin URI: https://github.com/
 * Description: Плагин, для создания кнопок НМО на любой странице. Кнопка вызывается у пользователей вручную. Есть возможность выгрузить статистику нажатий в CSV.
 * Version: 2.1.0
 * Author: Bogdanov Andrey
 * Author URI: mailto://swarzone2100@yandex.ru
*/
require_once __DIR__.'/nmobtn-autoload.php';

use nmobtn\DataBase;
use nmobtn\Main;

DataBase::init();

register_activation_hook(__FILE__, 'nmoButtonInstall');
register_deactivation_hook(__FILE__, 'nmoButtonUninstall');

function nmoButtonInstall()
{
    DataBase::Install();
}

function nmoButtonUninstall()
{
    DataBase::Uninstall();
}

new Main();
