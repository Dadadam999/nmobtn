<?php
/**
 * @package nmobtn
 * @author Bogdanov Andrey (swarzone2100@yandex.ru)
 */
namespace nmobtn\Tables;
use nmobtn\Interfaces\ITable;

class ChatTable implements ITable
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
         "CREATE TABLE `" . $this->wpdb->prefix . "nmobtn_chat`
         (
         id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         event_id INT(6) UNSIGNED NOT NULL,
         user_id INT(6) UNSIGNED NOT NULL,
         write_date DATETIME,
         user_name VARCHAR(256),
         message VARCHAR(2048),
         UNIQUE KEY id (id)
         )"
      );
  }

  public function Drop()
  {
      $this->wpdb->get_results(
        "DROP TABLE `" . $this->wpdb->prefix . "nmobtn_chat`"
      );
  }

  public function GetToUser($event_id, $user_id)
  {
    return $this->wpdb->get_results(
        "SELECT *
         FROM `" . $this->wpdb->prefix . "nmobtn_chat`
         WHERE `event_id` = " . $event_id . " AND `user_id` = " . $user_id,
         ARRAY_A
    );
  }

  public function GetToUserLast($event_id, $user_id)
  {
    return $this->wpdb->get_results(
        "SELECT *
         FROM `" . $this->wpdb->prefix . "nmobtn_chat`
         WHERE `event_id` = " . $event_id . " AND `user_id` = " . $user_id . " ORDER BY `write_date` DESC",
         ARRAY_A
    )[0];
  }

  public function GetToEvent($event_id)
  {
    return $this->wpdb->get_results(
        "SELECT *
         FROM `" . $this->wpdb->prefix . "nmobtn_chat`
         WHERE `event_id` = " . $event_id . ' ORDER BY `write_date`',
         ARRAY_A
    );
  }

  public function GetToEventDate($event_id, $date_start, $date_end)
  {
    return $this->wpdb->get_results(
        "SELECT *
         FROM `" . $this->wpdb->prefix . "nmobtn_chat`
         WHERE `event_id` = " . $event_id . " AND
         `write_date` >= STR_TO_DATE('" . $date_start . "', '%Y-%m-%d %H:%i:%s') AND
         `write_date` <= STR_TO_DATE('" . $date_end . "', '%Y-%m-%d %H:%i:%s')
         ORDER BY `write_date`",
         ARRAY_A
    );
  }

  public function GetLast($event_id, $last_date)
  {
    return $this->wpdb->get_results(
        "SELECT *
         FROM `" . $this->wpdb->prefix . "nmobtn_chat`
         WHERE `event_id` = " . $event_id . " AND `write_date` >=  STR_TO_DATE('" . $last_date . "', '%Y-%m-%d %H:%i:%s')
         ORDER BY `write_date`",
         ARRAY_A
    );
  }

  public function GetLastWithoutUser($event_id, $user_id, $last_date)
  {
    return $this->wpdb->get_results(
        "SELECT *
         FROM `" . $this->wpdb->prefix . "nmobtn_chat`
         WHERE `event_id` = " . $event_id . " AND `write_date` >  STR_TO_DATE('" . $last_date . "', '%Y-%m-%d %H:%i:%s') AND `user_id` != " . $user_id . "
         ORDER BY `write_date`",
         ARRAY_A
    );
  }

  public function Add($event_id, $user_id, $user_name, $message)
  {
      date_default_timezone_set('Europe/Moscow');
      $date_now = date("Y-m-d H:i:s");

      $this->wpdb->get_results(
          "INSERT INTO `" . $this->wpdb->prefix . "nmobtn_chat` (`user_id`, `event_id`, `write_date`, `user_name` , `message`)
           VALUES (" . $user_id . ", " . $event_id . ", '" . $date_now . "', '" . $user_name . "', '" . $message . "')"
      );
  }

  public function Update( $id, $message )
  {
    $this->wpdb->get_results(
      "UPDATE `" . $this->wpdb->prefix . "nmobtn_chat`
       SET `message`= '" . $message . "'
       WHERE `id` = " . $id
    );
  }

  public function Delete( $id )
  {
    $this->wpdb->get_results(
      "DELETE FROM `" . $this->wpdb->prefix . "nmobtn_chat` WHERE id = " . $id
    );
  }
}
