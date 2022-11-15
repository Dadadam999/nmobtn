<?php
/**
 * @package nmobtn
 * @author Bogdanov Andrey (swarzone2100@yandex.ru)
 */
namespace nmobtn\Tables;
use nmobtn\Interfaces\ITable;

class SettingsTable implements ITable
{
  private $wpdb;

  public function __construct()
  {
      global $wpdb;
      $this->wpdb = $wpdb;
  }

  public function Create()
  {
      $this->wpdb->get_results(
         "CREATE TABLE `" . $this->wpdb->prefix . "nmobtn_settings`
         (
         id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         meta_name VARCHAR(255),
         meta_value TEXT,
         UNIQUE KEY id (id)
         )"
      );

      $this->Add('interval_chat', '30');
      $this->Add('interval_nmo', '60');

      $default_template_usermeta = [
          'phone' => 'Телефон'
      ];

      $this->Add( 'template_usermeta', json_encode($default_template_usermeta, JSON_UNESCAPED_UNICODE ) );


      // $default_template_symposiums = [
      //     'Открытие' => [ 'hall' => '1', 'date_start' => '' ]'Телефон'
      // ];
      //
      // $this->Add( 'template_symposiums', json_encode($default_template_symposiums, JSON_UNESCAPED_UNICODE ) );
  }

  public function Drop()
  {
      $this->wpdb->get_results(
        "DROP TABLE `" . $this->wpdb->prefix . "nmobtn_settings`"
      );
  }

  public function Get($name)
  {
    return $this->wpdb->get_results(
        "SELECT `meta_value`
         FROM `" . $this->wpdb->prefix . "nmobtn_settings`
         WHERE `meta_name` = '" . $name . "'",
         ARRAY_A
    )[0]['meta_value'];
  }


  public function GetUserMetaKey( $exclude )
  {
    $sql = "SELECT `meta_key`
    FROM `" . $this->wpdb->prefix . "usermeta`";

    if( !empty( $exclude ) )
    {
      $sql .= " WHERE `meta_key` NOT IN (";

      foreach ($exclude as $key => $value)
          $sql .= "'" .  $key . "', ";

      $sql = substr( $sql,0,-2 );
      $sql .= ") ";
    }

    $sql .= ' GROUP BY `meta_key`';

    return $this->wpdb->get_results( $sql, ARRAY_A );
  }

  public function Add($name, $value)
  {
    $this->wpdb->get_results(
        "INSERT INTO `" . $this->wpdb->prefix . "nmobtn_settings` (`meta_name`, `meta_value`) VALUES ('" . $name . "', '" . $value . "')"
    );
  }

  public function Update($name, $value)
  {
      $key = $this->wpdb->get_results(
          "SELECT `meta_name` FROM `" . $this->wpdb->prefix . "nmobtn_settings` WHERE `meta_name` = '" . $name . "'",
           ARRAY_A
      )[0];

      if( empty( $key ) )
          $this->Add( $name, $value );
      else
          $this->wpdb->get_results( "UPDATE `" . $this->wpdb->prefix . "nmobtn_settings` SET `meta_value` = '" . $value . "'  WHERE `meta_name` = '" . $name . "'" );
  }

  public function Delete($name)
  {
    $this->wpdb->get_results(
      "DELETE FROM `" . $this->wpdb->prefix . "nmobtn_settings` WHERE `meta_name` = '" . $name . "'"
    );
  }
}
