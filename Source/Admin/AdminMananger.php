<?php
/**
 * @package nmobtn
 * @author Bogdanov Andrey (swarzone2100@yandex.ru)
 */
namespace nmobtn\Admin;
use nmobtn\Admin\Controllers\Events;
use nmobtn\Admin\Controllers\Presentations;
use nmobtn\Admin\Controllers\MetaData;
use nmobtn\Admin\Controllers\ExportFile;
use nmobtn\Admin\Controllers\Settings;

class AdminMananger
{
    public function __construct()
    {
        new Events;
        new Presentations;
        new MetaData;
        new ExportFile;
        new Settings;
    }
}
