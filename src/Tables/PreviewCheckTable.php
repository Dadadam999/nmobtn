<?php
/**
 * @package nmobtn
 * @author Bogdanov Andrey (swarzone2100@yandex.ru)
 */
namespace nmobtn\Tables;
use nmobtn\Interfaces\ITable;

class PreviewCheckTable implements ITable
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
         "CREATE TABLE `" . $this->wpdb->prefix . "nmobtn_nmo_preview`
         (
         id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         event_id BIGINT(20) UNSIGNED NOT NULL,
         user_id BIGINT(20) UNSIGNED NOT NULL,
         click MEDIUMINT(10) UNSIGNED NOT NULL,
         UNIQUE KEY id (id)
         )"
      );
  }
  public function Drop()
  {
      $this->wpdb->get_results(
        "DROP TABLE `" . $this->wpdb->prefix . "nmobtn_nmo_preview`"
      );
  }

  public function Get($event_id, $user_id)
  {
    return $this->wpdb->get_results(
        "SELECT `click`
         FROM `" . $this->wpdb->prefix . "nmobtn_nmo_preview`
         WHERE `event_id` = " . $event_id . " AND `user_id` = " . $user_id,
         ARRAY_A
    );
  }

  public function GetUsers($event_id)
  {
    return $this->wpdb->get_results(
        "SELECT `user_id`
         FROM `" . $this->wpdb->prefix . "nmobtn_nmo_preview`
         WHERE event_id = " . $event_id,
         ARRAY_A
    );
  }

  public function Add($event_id, $user_id)
  {
    $this->wpdb->get_results(
        "INSERT INTO `" . $this->wpdb->prefix . "nmobtn_nmo_preview` (`user_id`, `event_id`, `click`) VALUES (" . $user_id . ", " . $event_id . ", 1)"
    );
  }

  public function Delete($event_id, $user_id)
  {
    $this->wpdb->get_results(
      "DELETE FROM `" . $this->wpdb->prefix . "nmobtn_nmo_preview` WHERE `user_id` = " . $user_id . " AND `event_id` = " . $event_id
    );
  }
}
