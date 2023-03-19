<?php
/**
 * @package nmobtn
 * @author Bogdanov Andrey (swarzone2100@yandex.ru)
 */
namespace nmobtn\Tables;
use nmobtn\Interfaces\ITable;

class VisitsTable implements ITable
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
         "CREATE TABLE `" . $this->wpdb->prefix . "nmobtn_user_visits`
         (
         id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         user_id INT(6) UNSIGNED NOT NULL,
         visit_date DATETIME,
         url VARCHAR(2048),
         UNIQUE KEY id (id)
         )"
      );
  }

  public function Drop()
  {
      $this->wpdb->get_results(
        "DROP TABLE `" . $this->wpdb->prefix . "nmobtn_user_visits`"
      );
  }

  public function GetToDate( $date_start, $date_end )
  {
    return $this->wpdb->get_results(
        "SELECT *
         FROM `" . $this->wpdb->prefix . "nmobtn_user_visits`
         WHERE `visit_date` >=  STR_TO_DATE('" . $date_start . "', '%Y-%m-%d %H:%i:%s') AND `visit_date` <=  STR_TO_DATE('" . $date_end . "', '%Y-%m-%d %H:%i:%s') ",
         ARRAY_A
    );
  }

  public function GetToUrl( $url )
  {
    return $this->wpdb->get_results(
        "SELECT *
         FROM `" . $this->wpdb->prefix . "nmobtn_user_visits`
         WHERE `url` LIKE '%" . $url . "%'",
         ARRAY_A
    );
  }

  public function GetToUser( $user_id )
  {
    return $this->wpdb->get_results(
        "SELECT *
         FROM `" . $this->wpdb->prefix . "nmobtn_user_visits`
         WHERE `user_id` = " . $user_id,
         ARRAY_A
    );
  }

  public function Add( $user_id, $url )
  {
      date_default_timezone_set('Europe/Moscow');
      $date_now = date("Y-m-d H:i:s");

      $this->wpdb->get_results(
          "INSERT INTO `" . $this->wpdb->prefix . "nmobtn_user_visits` (`user_id`, `visit_date` , `url`)
           VALUES (" . $user_id . ", '" . $date_now . "', '" . $url . "')"
      );
  }

  public function DeleteToUser( $user_id )
  {
    $this->wpdb->get_results(
      "DELETE FROM `" . $this->wpdb->prefix . "nmobtn_user_visits` WHERE user_id = " . $user_id
    );
  }

  public function DeleteToUrl( $url )
  {
    $this->wpdb->get_results(
      "DELETE FROM `" . $this->wpdb->prefix . "nmobtn_user_visits`  WHERE `url` LIKE '%" . $url . "%'"
    );
  }

  public function DeleteAll()
  {
    $this->wpdb->get_results( "DELETE FROM `" . $this->wpdb->prefix . "nmobtn_user_visits`" );
  }
}
