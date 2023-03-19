<?php
/**
* @package nmobtn
* @author Bogdanov Andrey (swarzone2100@yandex.ru)
*/
namespace nmobtn\Admin\Controllers;
use nmobtn\Admin\AdminMananger;
use nmobtn\Admin\FileGenerator;
use nmobtn\DataBase;

class ExportFile
{
  public function __construct()
  {

      add_action('admin_menu', function()
      {
          add_submenu_page(
              'settings_nmobtn',
              'Выгрузка шаблонов',
              'Выгрузка шаблонов',
              'administrator',
              'settings_nmobtn_2',
              array($this, 'nmobtnAdminFileCallback')
          );
      });

      $this->postCallback();
  }

  function nmobtnAdminFileCallback()
  {
      ob_start();
      include  WP_PLUGIN_DIR . '/nmobtn/Source/Admin/View/ExportFile.php';
      echo ob_get_clean();
  }

  private function postCallback()
  {
      if( !isset( $_POST['nmobtnDownloadFileNonce'] ) || !isset($_POST['nmobtn-file-select']) || !isset($_POST['nmobtn-file-template']) )
          return 'Шаблон или мероприятие не выбраны!';

      add_action('plugins_loaded', function()
      {
          $path = plugin_dir_path(__FILE__);
          $template_id = $_POST['nmobtn-file-template'];
          $event_id = $_POST['nmobtn-file-select'];
          $event_name = DataBase::$tables['events']->Get($event_id)['name'];
          $date_start = str_replace('T', ' ', $_POST['nmobtn-start-date']);
          $date_end = str_replace('T', ' ', $_POST['nmobtn-end-date']);
          $file = '';

          if($template_id == 1)
              $file = FileGenerator::getNmo($event_id, $date_start, $date_end);

          if($template_id == 2)
              $file = FileGenerator::getUsers($event_id, $date_start, $date_end);

          if($template_id == 3)
          {
              $file = FileGenerator::getGroup('city', 3, 'Город');
              $file .= "\r\n";
              $file .= "\r\n";
              $file .= FileGenerator::getGroup('country', 0, 'Страны');
              $file .= "\r\n";
              $file .= "\r\n";
              $file .= FileGenerator::getGroup('region', 0, 'Регионы');
          }
          if($template_id == 4)
              $file = FileGenerator::getGroup('Specialty', 0, 'Специальность');

          if($template_id == 5)
              $file = FileGenerator::getVisits($event_id, $date_start, $date_end, $_POST['nmobtn-url-filter']);

          if($template_id == 6)
              $file = FileGenerator::getChat($event_id, $date_start, $date_end);

          if($template_id == 7)
              $file = FileGenerator::getMain($event_id, $date_start, $date_end);

          if($template_id == 8)
              $file = FileGenerator::getPresentations();

          if( empty( $file ) )
              $file = 'Шаблон выбран не верно';


          $file = mb_convert_encoding($file, 'windows-1251', 'utf-8');
          header('Content-type: application; charset=windows-1251');
          header('Content-disposition: attachment; filename=' . $event_name . '-' . date('Ymd-His') . '.csv');
          echo $file;
          die;
      });
  }
}
