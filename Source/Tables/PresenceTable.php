<?php
/**
 * @package nmobtn
 * @author Bogdanov Andrey (swarzone2100@yandex.ru)
 */
namespace nmobtn\Tables;
use nmobtn\Interfaces\ITable;

class PresenceTable implements ITable
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
         "CREATE TABLE `" . $this->wpdb->prefix . "nmobtn_presence`
         (
         id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         event_id BIGINT(20) UNSIGNED NOT NULL,
         user_id BIGINT(20) UNSIGNED NOT NULL,
         presence_time DATETIME,
         clicks MEDIUMINT(10) UNSIGNED NOT NULL,
         UNIQUE KEY id (id)
         )"
      );
  }
  public function Drop()
  {
      $this->wpdb->get_results(
        "DROP TABLE `" . $this->wpdb->prefix . "nmobtn_presence`"
      );
  }

  public function Get($event_id, $date_start, $date_end)
  {
    return $this->wpdb->get_results(
        "SELECT *
         FROM `" . $this->wpdb->prefix . "nmobtn_presence`
         WHERE `event_id` = " . $event_id . " AND
         `presence_time` >= STR_TO_DATE('" . $date_start . "', '%Y-%m-%d %H:%i:%s') AND
         `presence_time` <= STR_TO_DATE('" . $date_end . "', '%Y-%m-%d %H:%i:%s')",
         ARRAY_A
    );
  }

  public function GetGroup($event_id, $date_start, $date_end)
  {
    return $this->wpdb->get_results(
        "SELECT `user_id`, SUM(`clicks`) AS clicks
         FROM `" . $this->wpdb->prefix . "nmobtn_presence`
         WHERE `event_id` = " . $event_id . " AND
         `presence_time` >= STR_TO_DATE('" . $date_start . "', '%Y-%m-%d %H:%i:%s') AND
         `presence_time` <= STR_TO_DATE('" . $date_end . "', '%Y-%m-%d %H:%i:%s')
         GROUP BY `user_id`",
         ARRAY_A
    );
  }

  public function GetGroupAll($date_start, $date_end)
  {
    return $this->wpdb->get_results(
        "SELECT `user_id`, SUM(`clicks`) AS clicks
         FROM `" . $this->wpdb->prefix . "nmobtn_presence`
         WHERE `presence_time` >= STR_TO_DATE('" . $date_start . "', '%Y-%m-%d %H:%i:%s') AND
         `presence_time` <= STR_TO_DATE('" . $date_end . "', '%Y-%m-%d %H:%i:%s')
         GROUP BY `user_id`",
         ARRAY_A
    );
  }

  public function GetAll($date_start, $date_end)
  {
    return $this->wpdb->get_results(
        "SELECT * FROM `" . $this->wpdb->prefix . "nmobtn_presence`
         WHERE `presence_time` >= STR_TO_DATE('" . $date_start . "', '%Y-%m-%d %H:%i:%s') AND
         `presence_time` <= STR_TO_DATE('" . $date_end . "', '%Y-%m-%d %H:%i:%s')",
         ARRAY_A
    );
  }

  public function Add($event_id, $user_id)
  {
      date_default_timezone_set('Europe/Moscow');
      $presence_time = date("Y-m-d H:i:s");

      $this->wpdb->get_results(
          "INSERT INTO `" . $this->wpdb->prefix . "nmobtn_presence` (`event_id`, `user_id`, `presence_time`, `clicks`)
           VALUES (" . $event_id . ", " . $user_id . ", '" . $presence_time . "', 1)"
      );
  }
}
