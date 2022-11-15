<?php
/**
 * @package nmobtn
 * @author Bogdanov Andrey (swarzone2100@yandex.ru)
 */
namespace nmobtn\Tables;
use nmobtn\Interfaces\ITable;

class EventsTable implements ITable
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
         "CREATE TABLE `" . $this->wpdb->prefix . "nmobtn_events`
         (
         id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         event_id BIGINT(20) UNSIGNED NOT NULL,
         name VARCHAR(255) NOT NULL,
         UNIQUE KEY id (id)
         )"
      );
  }

  public function Drop()
  {
      $this->wpdb->get_results(
        "DROP TABLE `" . $this->wpdb->prefix . "nmobtn_events`"
      );
  }

  public function GetAll()
  {
    return $this->wpdb->get_results(
       "SELECT `event_id`, `name`
       FROM `" . $this->wpdb->prefix . "nmobtn_events`",
       ARRAY_A
      );
  }

  public function Get($event_id)
  {
    return $this->wpdb->get_results(
       "SELECT `event_id`, `name`
       FROM `" . $this->wpdb->prefix . "nmobtn_events`
       WHERE event_id = " . $event_id,
       ARRAY_A
      )[0];
  }

  public function Add($event_id, $event_name)
  {
    $this->wpdb->get_results(
      "INSERT INTO `" . $this->wpdb->prefix . "nmobtn_events` (`event_id`, `name`)
      VALUES (" . $event_id . ", '" . $event_name . "')"
    );
  }

  public function Delete($event_id)
  {
      $this->wpdb->get_results(
      "DELETE FROM `" . $this->wpdb->prefix . "nmobtn_events` WHERE `event_id` = " . $event_id
      );
  }
}
