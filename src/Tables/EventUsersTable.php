<?php
/**
 * @package nmobtn
 * @author Bogdanov Andrey (swarzone2100@yandex.ru)
 */
namespace nmobtn\Tables;
use nmobtn\Interfaces\ITable;

class EventUsersTable implements ITable
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
         "CREATE TABLE `" . $this->wpdb->prefix . "nmobtn_event_users`
         (
         id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         event_id BIGINT(20) UNSIGNED NOT NULL,
         user_id BIGINT(20) UNSIGNED NOT NULL,
         UNIQUE KEY id (id)
         )"
      );
  }

  public function Drop()
  {
      $this->wpdb->get_results(
        "DROP TABLE `" . $this->wpdb->prefix . "nmobtn_event_users`"
      );
  }

   public function Get($event_id)
    {
      return $this->wpdb->get_results(
          "SELECT `user_id`
           FROM `" . $this->wpdb->prefix . "nmobtn_event_users`
           WHERE event_id = " . $event_id,
           ARRAY_A
      );
    }

    public function GetCount($event_id)
     {
       return $this->wpdb->get_results(
           "SELECT COUNT(`user_id`) AS counter
            FROM `" . $this->wpdb->prefix . "nmobtn_event_users`
            WHERE event_id = " . $event_id,
            ARRAY_A
       )[0]['counter'];
     }

  public function Add($event_id, $user_id)
  {
      $this->wpdb->get_results(
          "INSERT INTO `" . $this->wpdb->prefix . "nmobtn_event_users` (`event_id`, `user_id`)
           VALUES (" . $event_id . ", '" . $user_id . "')"
      );
  }

  public function Delete($user_id)
  {
      $this->wpdb->get_results(
        "DELETE FROM `" . $this->wpdb->prefix . "nmobtn_event_users` WHERE `user_id` = " . $user_id
      );
  }
}
