<?php
/**
 * @package nmobtn
 * @author Bogdanov Andrey (swarzone2100@yandex.ru)
 */
namespace nmobtn\Admin;
use nmobtn\DataBase;


class SettingsPage
{
  private $database;

  public function __construct()
  {
      $this->database = new DataBase();

      add_action('admin_menu', function()
      {
          add_menu_page(
              'НМО',
              'Плагин НМО',
              'administrator',
              'settings_nmobtn',
              array($this, 'nmobtn_settings_callback'),
              'dashicons-admin-generic',
              20
          );

          add_submenu_page(
              'settings_nmobtn',
              'Мета поля шаблонов',
              'Мета поля',
              'administrator',
              'settings_nmobtn_1',
              array($this, 'nmobtn_settings_usermeta_callback')
          );

          add_submenu_page(
              'settings_nmobtn',
              'Выгрузка шаблонов',
              'Выгрузка шаблонов',
              'administrator',
              'settings_nmobtn_2',
              array($this, 'nmobtn_settings_file_callback')
          );

          add_submenu_page(
              'settings_nmobtn',
              'Настройки',
              'Настройки',
              'administrator',
              'settings_nmobtn_3',
              array($this, 'nmobtn_settings_config_callback')
          );

          // add_submenu_page(
          //     'settings_nmobtn',
          //     'Симпозиумы',
          //     'Симпозиумы',
          //     'administrator',
          //     'settings_nmobtn_4',
          //     array($this, 'nmobtn_settings_symposiums_callback')
          // );
      });

      $this->fileClicks();
      $this->userMetaClicks();
      $this->symposiumsClicks();
  }

  function nmobtn_settings_symposiums_callback()
  {
    $html = '<div class="container">';
    $html .= '<h1 class="h3 text-center my-5">Настройка симпозиумов</h1>';
    $html .= '<div style=" max-width: 65%; margin: 0px auto;">';

    $html .= '<div style="display: flex;">';

    $html .= '<div style="width: 50%; border: 1px solid; border-radius:5px; padding:10px;">';
    $html .= '<form action="" method="post" enctype="multipart/form-data">';
    $html .=  wp_nonce_field('nmobtnSymposiumsAddNonce-wpnp', 'nmobtnSymposiumsAddNonce');

    $html .= '<label style="margin-top:20px; min-width: 50%;" for="nmobtn-name-symposiums-add" class="form-label">Номер или имя симпозиума</label>';
    $html .= '<br>';
    $html .= '<input type="text" id="nmobtn-name-symposiums-add" name="nmobtn-name-symposiums-add">';
    $html .= '<br><br>';
    $html .= '<label style="margin-top:20px; min-width: 50%;" for="nmobtn-hall-symposiums-add" class="form-label">Зал</label>';
    $html .= '<br>';
    $html .= '<input type="text" id="nmobtn-hall-symposiums-add" name="nmobtn-hall-symposiums-add">';
    $html .= '<br><br>';
    $html .= '<label style="margin-top:20px; min-width: 50%;" for="nmobtn-datestart-symposiums-add" class="form-label">Время начала</label>';
    $html .= '<br>';
    $html .= '<input type="text" id="nmobtn-datestart-symposiums-add" name="nmobtn-datestart-symposiums-add" placeholder="1970-01-01 00:00:00" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}">';
    $html .= '<br><br>';
    $html .= '<label style="margin-top:20px; min-width: 50%;" for="nmobtn-dateend-symposiums-add" class="form-label">Время конца</label>';
    $html .= '<br>';
    $html .= '<input type="text" id="nmobtn-dateend-symposiums-add" name="nmobtn-dateend-symposiums-add" placeholder="1970-01-01 00:00:00" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}">';
    $html .= '<br><br>';

    $html .= '<label style="margin-top:20px; min-width: 50%;" for="nmobtn-csv-symposiums-add" class="form-label">Или загрузить симпозиумы файлом</label>';
    $html .= '<br>';
    $html .= '<input type="file" id="nmobtn-csv-symposiums-add" name="nmobtn-csv-symposiums-add">';
    $html .= '<br><br>';
    $html .= '<button type="submit" style="margin-top:20px;" class="button button-primary">Добавить симпозиум(ы)</button>';
    $html .= '</form>';
    $html .= '</div>';

    $html .= '<div style="width: 50%;  border: 1px solid; border-radius:5px; padding:10px; margin-left:10px">';
    $html .= '<form action="" method="post">';
    $html .=  wp_nonce_field('nmobtnSymposiumsRemoveNonce-wpnp', 'nmobtnSymposiumsRemoveNonce');

    $html .= '<label style="margin-top:20px; min-width: 50%;" for="nmobtn-name-symposiums-remove" class="form-label">Номер или имя симпозиума</label>';
    $html .= '<br>';
    $html .= '<input type="text" id="nmobtn-name-symposiums-remove" name="nmobtn-name-symposiums-remove">';
    $html .= '<br><br>';
    $html .= '<button type="submit" style="margin-top:20px;" class="button button-primary">Удалить симпозиум</button>';
    $html .= '</form>';
    $html .= '</div>';
    $html .= '</div>';

    $html_table .= '<table style="width: 100%; text-align: center;margin-top:25px">';
    $html_table .= '<tr>';
    $html_table .= '<th>Симпозиум</th>';
    $html_table .= '<th>Зал</th>';
    $html_table .= '<th>Время начала</th>';
    $html_table .= '<th>Время конца</th>';
    $html_table .= '</tr>';

    $symposiums_saved = json_decode( $this->database->tables['settings']->Get('template_symposiums') );
    $count = 0;

    if( !empty( $symposiums_saved ) )
       foreach ( $symposiums_saved as $key => $value )
       {
           $html_table .= '<tr>';
           $html_table .= '<td style="border: 1px solid;" >' . $key .  '</td>';
           $html_table .= '<td style="border: 1px solid;">' . $value->hall . '</td>';
           $html_table .= '<td style="border: 1px solid;">' . $value->date_start . '</td>';
           $html_table .= '<td style="border: 1px solid;">' . $value->date_end . '</td>';
           $html_table .= '</tr>';
           $count++;
       }

    $html_table .= '</table>';
    $html .= $html_table;
    $html .= '<p>Всего: ' . $count . '</p>';
    $html .= '</div>';
    $html .= '</div>';
    echo $html;
  }

  private function symposiumsClicks()
  {
      add_action('plugins_loaded', function()
      {
          if( !empty( $_POST['nmobtnSymposiumsAddNonce'] ) )
          {
              if( isset( $_FILES['nmobtn-csv-symposiums-add'] ) AND !empty( file_get_contents( $_FILES['nmobtn-csv-symposiums-add']['tmp_name'] ) ) )
              {
                  if ($_FILES['nmobtn-csv-symposiums-add']['error'] > 0 AND $_FILES['nmobtn-csv-symposiums-add']['error'] != 4)
                  {
                      echo '<p style="margin-left:190px;">Файл не загрузился из-за ошибки: ' . $_FILES['nmobtn-csv-symposiums-add']['error'] . '</p><br>';
                      return '';
                  }

                  $file_content = file_get_contents($_FILES['nmobtn-csv-symposiums-add']['tmp_name']);
                  $file_rows = explode("\r\n", $file_content);
                  $symposiums = array();

                  foreach ($file_rows as $row)
                  {
                      if( !empty($row) )
                      {
                          $cells = explode(';', $row);

                          $symposiums +=
                          [
                              $cells[0] =>
                              [
                                'hall' => $cells[1],
                                'date_start' => $cells[2],
                                'date_end' => $cells[3]
                              ]
                          ];
                      }
                  }

                  $symposiums_saved = json_decode( $this->database->tables['settings']->Get('template_symposiums'), true );

                  if( empty( $symposiums_saved ) )
                      $symposiums_saved = $symposiums;
                  else
                      $symposiums_saved += $symposiums;

                  $this->database->tables['settings']->Update( 'template_symposiums', json_encode( $symposiums_saved, JSON_UNESCAPED_UNICODE ) );

                  echo '';
                  return '';
              }

              if( empty( $_POST['nmobtn-name-symposiums-add'] ) || !isset( $_POST['nmobtn-name-symposiums-add'] ) || !isset( $_POST['nmobtn-datestart-symposiums-add'] ) || !isset( $_POST['nmobtn-dateend-symposiums-add'] ) )
              {
                  echo 'Данные введны не верно!';
                  return '';
              }

              $symposiums_saved = json_decode( $this->database->tables['settings']->Get('template_symposiums'), true );

              if( empty( $symposiums_saved ) )
              {
                  $symposiums_saved = [
                        $_POST['nmobtn-name-symposiums-add'] => ['hall' => $_POST['nmobtn-hall-symposiums-add'],
                        'date_start' => $_POST['nmobtn-datestart-symposiums-add'],
                        'date_end' => $_POST['nmobtn-dateend-symposiums-add']]
                  ];
              }
              else
              {
                  $symposiums_saved += [
                        $_POST['nmobtn-name-symposiums-add'] => ['hall' => $_POST['nmobtn-hall-symposiums-add'],
                        'date_start' => $_POST['nmobtn-datestart-symposiums-add'],
                        'date_end' => $_POST['nmobtn-dateend-symposiums-add']]
                  ];
              }

              $this->database->tables['settings']->Update( 'template_symposiums', json_encode( $symposiums_saved, JSON_UNESCAPED_UNICODE ) );
          }

          if( !empty( $_POST['nmobtnSymposiumsRemoveNonce'] ) )
          {
              if( !isset( $_POST['nmobtn-name-symposiums-remove'] ) )
              {
                  echo 'Симпозиум не вписан!';
                  return '';
              }

              $symposiums_saved = json_decode( $this->database->tables['settings']->Get('template_symposiums'), true );

              if( !empty($symposiums_saved) )
              {
                  unset( $symposiums_saved[ $_POST['nmobtn-name-symposiums-remove'] ] );
                  $this->database->tables['settings']->Update( 'template_symposiums', json_encode( $symposiums_saved, JSON_UNESCAPED_UNICODE ) );
              }
          }
      });
  }

  function nmobtn_settings_usermeta_callback()
  {

    $html = '<div class="container">';
    $html .= '<h1 class="h3 text-center my-5">Настройка выводимых полей</h1>';
    $html .= '<div style=" max-width: 65%; margin: 0px auto;">';

    $html .= '<div style="display: flex;">';

    $html .= '<div style="width: 50%; border: 1px solid; border-radius:5px; padding:10px;">';
    $html .= '<form action="" method="post">';
    $html .=  wp_nonce_field('nmobtnUserMetaAddNonce-wpnp', 'nmobtnUserMetaAddNonce');

    $html .= '<label style="margin-top:20px; min-width: 50%;" for="nmobtn-usermeta-select-add" class="form-label">Выбрать метаполе:</label>';
    $html .= '<br>';
    $html .= '<select style="min-width: 50%;" name="nmobtn-usermeta-select-add" class="form-control form-control-sm">';
    $html .= '<option disabled selected value="-1">Выберите</option>';

    $meta_key_saved = json_decode( $this->database->tables['settings']->Get('template_usermeta') );
    $meta_keys = $this->database->tables['settings']->GetUserMetaKey( $meta_key_saved );

    if( !empty( $meta_keys ) )
       foreach ( $meta_keys as $row)
           $html .= '<option value="' . $row['meta_key'] . '">'. $row['meta_key'] .'</option>';

    $html .= '</select>';
    $html .= '<br><br>';
    $html .= '<label style="margin-top:20px; min-width: 50%;" for="nmobtn-name-usermeta-add" class="form-label">Заголовок</label>';
    $html .= '<br>';
    $html .= '<input type="text" id="nmobtn-name-usermeta-add" name="nmobtn-name-usermeta-add">';
    $html .= '<br><br>';
    $html .= '<button type="submit" style="margin-top:20px;" class="button button-primary">Добавить поле</button>';
    $html .= '</form>';
    $html .= '</div>';

    $html .= '<div style="width: 50%;  border: 1px solid; border-radius:5px; padding:10px; margin-left:10px">';
    $html .= '<form action="" method="post">';
    $html .=  wp_nonce_field('nmobtnUserMetaRemoveNonce-wpnp', 'nmobtnUserMetaRemoveNonce');

    $html .= '<label style="margin-top:20px; min-width: 50%;" for="nmobtn-usermeta-select-remove" class="form-label">Выбрать метаполе:</label>';
    $html .= '<br>';
    $html .= '<select style="min-width: 50%;" name="nmobtn-usermeta-select-remove" class="form-control form-control-sm">';
    $html .= '<option disabled selected value="-1">Выберите</option>';

    $meta_key_saved = json_decode( $this->database->tables['settings']->Get('template_usermeta') );

    if( !empty( $meta_key_saved ) )
       foreach ( $meta_key_saved as $key => $value)
           $html .= '<option value="' . $key . '">'. $value .'</option>';

    $html .= '</select>';
    $html .= '<br><br>';
    $html .= '<button type="submit" style="margin-top:20px;" class="button button-primary">Удалить поле</button>';
    $html .= '</form>';
    $html .= '</div>';
    $html .= '</div>';

    $html_table .= '<table style="width: 100%; text-align: center;margin-top:25px">';
    $html_table .= '<tr>';
    $html_table .= '<th>Мета-ключ</th>';
    $html_table .= '<th>Заголовок</th>';
    $html_table .= '</tr>';

    if( !empty( $meta_key_saved ) )
       foreach ( $meta_key_saved as $key => $value )
       {
           $html_table .= '<tr>';
           $html_table .= '<td style="border: 1px solid;" >' . $key .  '</td>';
           $html_table .= '<td style="border: 1px solid;">' . $value . '</td>';
           $html_table .= '</tr>';
       }

    $html_table .= '</table>';
    $html .= $html_table;
    $html .= '</div>';
    $html .= '</div>';
    echo $html;
  }

  function nmobtn_settings_callback()
  {
     $html = '<div class="container">';
     $html .= '<h1 class="h3 text-center my-5">Cтатистикa по НМО</h1>';
     $html .= '<div style=" max-width: 500px; margin: 0px auto;">';

     $html .= '</div>';
     $html .= '</div>';

     echo $html;
  }

  function nmobtn_settings_config_callback()
  {
     $html = '<div class="container">';
     $html .= '<h1 class="h3 text-center my-5">Настройки</h1>';
     $html .= '<div style=" max-width: 500px; margin: 0px auto;">';

     $html .= '</div>';
     $html .= '</div>';

     echo $html;
  }

  function nmobtn_settings_file_callback()
  {
     $html = '<div class="container">';
     $html .= '<h1 class="h3 text-center my-5">Скачать статистику по НМО</h1>';
     $html .= '<div style=" max-width: 500px; margin: 0px auto;">';
     $html .= '<form action="" method="post">';
     $html .=  wp_nonce_field('nmobtnDownloadFileNonce-wpnp', 'nmobtnDownloadFileNonce');

     $html .= '<label style="margin-top:20px; min-width: 50%;" for="nmobtn-file-template" class="form-label">Выберите шаблон:</label>';
     $html .= '<br>';
     $html .= '<select style="min-width: 50%;" name="nmobtn-file-template" class="form-control form-control-sm">';
     $html .= '<option disabled selected value="-1">Выберите</option>';
     $html .= '<option value="1">НМО</option>';
     $html .= '<option value="2">Слушатели</option>';
     $html .= '<option value="3">География</option>';
     $html .= '<option value="4">По специальностям</option>';
     $html .= '<option value="5">Посещения</option>';
     $html .= '<option value="6">Чат</option>';
     $html .= '<option value="7">Общая</option>';
     $html .= '<option value="8">Симпозиумы</option>';
     $html .= '</select>';
     $html .= '<br><br>';
     $html .= '<label style="margin-top:20px; min-width: 50%;" for="nmobtn-file-select" class="form-label">Выберите мероприятие:</label>';
     $html .= '<br>';
     $html .= '<select style="min-width: 50%;" name="nmobtn-file-select" class="form-control form-control-sm">';
     $html .= '<option disabled selected value="-1">Выберите</option>';
     $html .= '<option value="all">Все</option>';
     $events = $this->database->tables['events']->GetAll();

     if( !empty( $events ) )
        foreach ($events as $event)
            $html .= '<option value="' . $event['event_id'] . '">'. $event['name'] .'</option>';

     $html .= '</select>';
     $html .= '<br><br>';
     $html .= '<label style="margin-top:20px; min-width: 50%;" for="nmobtn-start-date" class="form-label">С даты:</label>';
     $html .= '<br>';
     $html .= '<input type="datetime-local" id="nmobtn-start-date" name="nmobtn-start-date" value="'. date('Y-m-d H:i:s') .'">';
     $html .= '<br><br>';
     $html .= '<label style="margin-top:20px; min-width: 50%;" for="nmobtn-end-date" class="form-label">По дату:</label>';
     $html .= '<br>';
     $html .= '<input type="datetime-local" id="nmobtn-end-date" name="nmobtn-end-date" value="'. date('Y-m-d H:i:s') .'">';
     $html .= '<br><br>';

     $html .= '<label style="margin-top:20px; min-width: 50%;" for="nmobtn-url-filter" class="form-label">URL:</label>';
     $html .= '<br>';
     $html .= '<input type="text" id="nmobtn-url-filter" name="nmobtn-url-filter" value="">';
     $html .= '<br><br>';

     $html .= '<button type="submit" style="margin-top:20px;" class="button button-primary">Выгрузить CSV</button>';
     $html .= '</form>';
     $html .= '</div>';
     $html .= '</div>';

     echo $html;
  }

  private function getFileNmo($event_id, $date_start, $date_end)
  {
      $head_table = [
           "User ID",
           "Минимальная продолжительность (минуты)",
           "Фактическая продолжительность (минуты)",
           "Минимальное кол-во подтверждений",
           "Фактическое кол-во подтверждений",
           "Минимальное число правильных ответов теста",
           "Фактическое число правльных ответов теста",
           "Дата регистрации",
           "Адрес электронной почты (email)"
      ];
      $meta_key_saved = json_decode( $this->database->tables['settings']->Get('template_usermeta') );

      foreach ( $meta_key_saved as $value)
          array_push($head_table, $value);

      $file = implode(";", $head_table) . "\r\n";

      if( $event_id === 'all' )
          $presence = $this->database->tables['presence']->GetGroupAll($date_start, $date_end);
      else
          $presence = $this->database->tables['presence']->GetGroup($event_id, $date_start, $date_end);

      $count=0;

      foreach ( $presence as $click )
      {
          $user_track = $this->database->tables['track']->Get($event_id, $click['user_id']);
          $time = ( int )( ( strtotime( $user_track['last_date'] ) - strtotime( $user_track['first_date'] ) ) / 60 );

          $content = [
              $click['user_id'],
              $time,
              $time,
              $click['clicks'],
              $click['clicks'],
              '-',
              '-',
              get_userdata( $click['user_id'] )->user_registered,
              get_userdata( $click['user_id'] )->user_email
          ];

          foreach ( $meta_key_saved as $key => $value)
          {
              $meta = get_user_meta( $click['user_id'], $key, true );
              $meta = str_replace("\r\n", " ", $meta);
              array_push($content, $meta);
          }

          $count++;
          $file .= implode(";", $content) . "\r\n";
      }

      return $file;
  }

  private function getFileUsers($event_id, $date_start, $date_end)
  {
      $head_table = [
           "User ID",
           "Мероприятие",
           "Дата захода",
           "Дата выхода",
           "Дата регистрации",
           "Адрес электронной почты (email)"
      ];

      $meta_key_saved = json_decode( $this->database->tables['settings']->Get('template_usermeta') );

      foreach ( $meta_key_saved as $value)
          array_push($head_table, $value);

      $file = implode(";", $head_table) . "\r\n";

      if( $event_id === 'all' )
          $responce = $this->database->tables['track']->GetAllDate($date_start, $date_end);
      else
          $responce = $this->database->tables['track']->GetEventDate($event_id, $date_start, $date_end);

      $count=0;

      foreach ( $responce as $row )
      {
          $content = [
              $row['user_id'],
              $this->database->tables['events']->Get($event_id)['name'],
              $row['first_date'],
              $row['last_date'],
              get_userdata( $row['user_id'] )->user_registered,
              get_userdata( $row['user_id'] )->user_email
          ];

          foreach ( $meta_key_saved as $key => $value)
          {
              $meta = get_user_meta( $row['user_id'], $key, true );
              $meta = str_replace("\r\n", " ", $meta);
              array_push($content, $meta);
          }

          $count++;
          $file .= implode(";", $content) . "\r\n";
      }

      return $file;
  }

  private function getFileGroup($meta_key, $count, $head_key)
  {
      $head_table = [
           $head_key,
           'Количество',
      ];

      $file = implode(";", $head_table) . "\r\n";
      $responce = $this->database->tables['track']->GetGroupAll($meta_key, $count);

      foreach ( $responce as $row )
      {
          if( empty($row['counts']) || empty($row['name']) )
              continue;

          $content = [
              $row['name'],
              $row['counts'],
          ];

          $file .= implode(";", $content) . "\r\n";
      }

      return $file;
  }

  private function getFileVisits($event_id, $date_start, $date_end, $url)
  {
      $head_table = [
           "User ID",
           "Дата перехода",
           "URL",
           "Дата регистрации",
           "Адрес электронной почты (email)"
      ];
      $meta_key_saved = json_decode( $this->database->tables['settings']->Get('template_usermeta') );

      foreach ( $meta_key_saved as $value)
          array_push($head_table, $value);

      $file = implode(";", $head_table) . "\r\n";

      if( empty( $url ) )
          $responce = $this->database->tables['visits']->GetToDate( $date_start, $date_end );
      else
          $responce = $this->database->tables['visits']->GetToUrl( $url );

      $count=0;

      foreach ( $responce as $row )
      {
          $content = [
              $row['user_id'],
              $row['visit_date'],
              $row['url'],
              get_userdata( $row['user_id'] )->user_registered,
              get_userdata( $row['user_id'] )->user_email
          ];

          foreach ( $meta_key_saved as $key => $value)
          {
              $meta = get_user_meta( $row['user_id'], $key, true );
              $meta = str_replace("\r\n", " ", $meta);
              array_push($content, $meta);
          }

          $count++;
          $file .= implode(";", $content) . "\r\n";
      }

      return $file;
  }

  private function getFileChat($event_id, $date_start, $date_end)
  {
      $head_table = [
           "User ID",
           "Дата написания",
           "Сообщение",
           "Дата регистрации",
           "Адрес электронной почты (email)"
      ];
      $meta_key_saved = json_decode( $this->database->tables['settings']->Get('template_usermeta') );

      foreach ( $meta_key_saved as $value)
          array_push($head_table, $value);

      $file = implode(";", $head_table) . "\r\n";
      $responce = $this->database->tables['chat']->GetToEvent($event_id);
      $count=0;

      foreach ( $responce as $row )
      {
          $content = [
              $row['user_id'],
              $row['write_date'],
              $row['message'],
              get_userdata( $row['user_id'] )->user_registered,
              get_userdata( $row['user_id'] )->user_email
          ];

          foreach ( $meta_key_saved as $key => $value)
          {
              $meta = get_user_meta( $row['user_id'], $key, true );
              $meta = str_replace("\r\n", " ", $meta);
              array_push($content, $meta);
          }

          $count++;
          $file .= implode(";", $content) . "\r\n";
      }

      return $file;
  }

  private function getFileMain($event_id, $date_start, $date_end)
  {
      $head_table = [
           "User ID",
           "Мероприятие",
           "Дата захода",
           "Дата выхода",
           "Время просмотра",
           "Клики НМО",
           "Форма участия",
           "Дата регистрации",
           "Адрес электронной почты (email)"
      ];

      $meta_key_saved = json_decode( $this->database->tables['settings']->Get('template_usermeta') );

      foreach ( $meta_key_saved as $value)
          array_push($head_table, $value);

      $file = implode(";", $head_table) . "\r\n";

      if( $event_id === 'all' )
      {
          $tracks = $this->database->tables['track']->GetAllDate($date_start, $date_end);
          $presence = $this->database->tables['presence']->GetGroupAll($date_start, $date_end);
          $users = get_users( [ 'role' => 'subscriber' ] );

           // $ids_users = [1];
           //
           // $users = get_users( [
           //     'include' => $ids_users
           // ] );
      }
      else
      {
          $tracks = $this->database->tables['track']->GetEventDate($event_id, $date_start, $date_end);
          $presence = $this->database->tables['presence']->GetGroup($event_id, $date_start, $date_end);
          $users_id = $this->database->tables['eventUsers']->Get($event_id);
          $ids = array();

          foreach ( $users_id as $user )
            array_push( $ids, $user['user_id'] );

          $users = get_users( [ 'role' => 'subscriber', 'include' => $ids ] );
      }

      $count=0;
      //$users = get_users( [ 'role' => 'subscriber' ] );

      foreach ( $users as $user )
      {
          $first_date = '';
          $last_date = '';
          $duration = '';
          $clicks = '';

          foreach ( $tracks as $track )
          {
            if( $track['user_id'] == $user->ID )
            {
                $start_time = strtotime($track['first_date']);
                $last_time = strtotime($track['last_date']);
                $presence_time = $last_time - $start_time;

                $first_date = date('Y-m-d H:i:s', $start_time);
                $last_date = date('Y-m-d H:i:s', $last_time);
                $duration = date('H:i:s', $presence_time);
                break;
            }
          }

          foreach ( $presence as $click )
          {
            if( $click['user_id'] == $user->ID )
            {
                $clicks = $click['clicks'];
                break;
            }
          }

          $is_auth = $first_date == '' ? 'Зарегистрирован' : 'Участвовал';

          $content = [
              $user->ID,
              $this->database->tables['events']->Get($event_id)['name'],
              $first_date,
              $last_date,
              $duration,
              $clicks,
              $is_auth,
              get_userdata( $user->ID )->user_registered,
              get_userdata( $user->ID )->user_email
          ];

          foreach ( $meta_key_saved as $key => $value)
          {
              $meta = get_user_meta(  $user->ID, $key, true );
              $meta = str_replace("\r\n", " ", $meta);
              array_push($content, $meta);
          }

          $count++;
          $file .= implode(";", $content) . "\r\n";
      }

      return $file;
  }

  private function getFileSimposiums()
  {
      $head_table = [
           "Симпозиум",
           "Зал",
           "Дата начала симпозиума",
           "Дата конца симпозиума",
           "User ID",
           "Дата захода",
           "Дата выхода",
           "Дата регистрации",
           "Адрес электронной почты (email)"
      ];

      $meta_key_saved = json_decode( $this->database->tables['settings']->Get('template_usermeta') );
      $symposiums_saved = json_decode( $this->database->tables['settings']->Get('template_symposiums') );

      foreach ( $meta_key_saved as $value)
          array_push($head_table, $value);

      $file = implode(";", $head_table) . "\r\n";

      foreach ( $symposiums_saved as $simposium_name => $simposium )
      {
          $simposium->date_start = date( 'Y-m-d H:i:s', strtotime($simposium->date_start) );
          $simposium->date_end = date( 'Y-m-d H:i:s', strtotime($simposium->date_end) );
          $responce = $this->database->tables['track']->GetCrossDate($simposium->date_start, $simposium->date_end);

          foreach ($responce as $row)
          {
              $content = [
                  $simposium_name,
                  $simposium->hall,
                  $simposium->date_start,
                  $simposium->date_end,
                  $row['user_id'],
                  $row['first_date'],
                  $row['last_date'],
                  get_userdata( $row['user_id'] )->user_registered,
                  get_userdata( $row['user_id'] )->user_email
              ];

              foreach ( $meta_key_saved as $key => $value)
              {
                  $meta = get_user_meta( $row['user_id'], $key, true );
                  $meta = str_replace("\r\n", " ", $meta);
                  array_push($content, $meta);
              }

              $file .= implode(";", $content) . "\r\n";
          }
      }

      return $file;
  }

  private function fileClicks()
  {
      if( !isset( $_POST['nmobtnDownloadFileNonce'] ) || !isset($_POST['nmobtn-file-select']) || !isset($_POST['nmobtn-file-template']) )
          return 'Шаблон или мероприятие не выбраны!';

      add_action('plugins_loaded', function()
      {
          $path = plugin_dir_path(__FILE__);
          $template_id = $_POST['nmobtn-file-template'];
          $event_id = $_POST['nmobtn-file-select'];
          $event_name = $this->database->tables['events']->Get($event_id)['name'];
          $date_start = str_replace('T', ' ', $_POST['nmobtn-start-date']);
          $date_end = str_replace('T', ' ', $_POST['nmobtn-end-date']);
          $file = '';

          if($template_id == 1)
              $file = $this->getFileNmo($event_id, $date_start, $date_end);

          if($template_id == 2)
              $file = $this->getFileUsers($event_id, $date_start, $date_end);

          if($template_id == 3)
          {
              $file = $this->getFileGroup('city', 3, 'Город');
              $file .= "\r\n";
              $file .= "\r\n";
              $file .= $this->getFileGroup('country', 0, 'Страны');
              $file .= "\r\n";
              $file .= "\r\n";
              $file .= $this->getFileGroup('region', 0, 'Регионы');
          }
          if($template_id == 4)
              $file = $this->getFileGroup('Specialty', 0, 'Специальность');

          if($template_id == 5)
              $file = $this->getFileVisits($event_id, $date_start, $date_end, $_POST['nmobtn-url-filter']);

          if($template_id == 6)
              $file = $this->getFileChat($event_id, $date_start, $date_end);

          if($template_id == 7)
              $file = $this->getFileMain($event_id, $date_start, $date_end);

          if($template_id == 8)
              $file = $this->getFileSimposiums();

          if( empty( $file ) )
              $file = 'Шаблон выбран не верно';


          $file = mb_convert_encoding($file, 'windows-1251', 'utf-8');
          header('Content-type: application; charset=windows-1251');
          header('Content-disposition: attachment; filename=' . $event_name . '-' . date('Ymd-His') . '.csv');
          echo $file;
          die;
      });
  }

  private function userMetaClicks()
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

              $meta_key_saved = json_decode( $this->database->tables['settings']->Get('template_usermeta'), true );
              $meta_key_saved += [ $_POST['nmobtn-usermeta-select-add'] => $_POST['nmobtn-name-usermeta-add'] ];
              $this->database->tables['settings']->Update( 'template_usermeta', json_encode( $meta_key_saved, JSON_UNESCAPED_UNICODE ) );
          }

          if( !empty( $_POST['nmobtnUserMetaRemoveNonce'] ) )
          {
              if( !isset( $_POST['nmobtn-usermeta-select-remove'] ) )
              {
                  echo 'Поля не выбраны!';
                  return '';
              }

              $meta_key_saved = json_decode( $this->database->tables['settings']->Get('template_usermeta'), true );
              unset( $meta_key_saved[ $_POST['nmobtn-usermeta-select-remove'] ] );
              $this->database->tables['settings']->Update( 'template_usermeta', json_encode( $meta_key_saved, JSON_UNESCAPED_UNICODE ) );
          }
      });
  }
}
          //echo '<pre style="margin-left:190px;">';
          //var_dump($meta_key_saved[ $_POST['nmobtn-usermeta-select-remove'] ]);
          //echo '</pre>';
