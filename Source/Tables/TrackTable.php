<?php
/**
 * @package nmobtn
 * @author Bogdanov Andrey (swarzone2100@yandex.ru)
 */
namespace nmobtn\Tables;
use nmobtn\Interfaces\ITable;

class TrackTable implements ITable
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
         "CREATE TABLE `" . $this->wpdb->prefix . "nmobtn_user_track`
         (
         id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         event_id INT(6) UNSIGNED NOT NULL,
         user_id INT(6) UNSIGNED NOT NULL,
         first_date DATETIME,
         last_date DATETIME,
         UNIQUE KEY id (id)
         )"
      );
  }
  public function Drop()
  {
      $this->wpdb->get_results(
        "DROP TABLE `" . $this->wpdb->prefix . "nmobtn_user_track`"
      );
  }

  public function Get($event_id, $user_id)
  {
    return $this->wpdb->get_results(
        "SELECT *
         FROM `" . $this->wpdb->prefix . "nmobtn_user_track`
         WHERE `event_id` = " . $event_id . " AND `user_id` = " . $user_id . " ORDER BY id DESC",
         ARRAY_A
    )[0];
  }

  public function GetSumMinute( $user_id, $event_id )
  {
      $results = $this->wpdb->get_results(
          "SELECT * FROM `" . $this->wpdb->prefix . "nmobtn_user_track`
           WHERE `event_id` = " . $event_id . " AND `user_id` = " . $user_id . " ORDER BY id DESC",
           ARRAY_A
      );

      $sum = 0;
      $last_date = null;
      foreach ($results as $result) {
          if ($last_date !== null) {
              $sum += (strtotime($last_date) - strtotime($result['first_date'])) / 60;
          }
          $last_date = $result['last_date'];
      }
      return $sum;
  }

  public function GetSumMinuteAll( $user_id, $start_date, $end_date )
  {
      $query = $this->wpdb->prepare(
          "SELECT SUM(TIMESTAMPDIFF(MINUTE, `first_date`, `last_date`)) AS total_minutes FROM `{$this->wpdb->prefix}nmobtn_user_track` WHERE `user_id` = %d AND `first_date` >= %s AND `last_date` <= %s",
          $user_id,
          $start_date,
          $end_date
      );
      $sum = $this->wpdb->get_var($query);

      return ( $sum !== null ? intval( $sum ) : 0 );
  }

  public function GetEventDate($event_id, $date_start, $date_end)
  {
    return $this->wpdb->get_results(
        "SELECT *
         FROM `" . $this->wpdb->prefix . "nmobtn_user_track`
         WHERE `event_id` = " . $event_id . " AND
         ( `first_date` >=  STR_TO_DATE('" . $date_start . "', '%Y-%m-%d %H:%i:%s') AND `first_date` <=  STR_TO_DATE('" . $date_end . "', '%Y-%m-%d %H:%i:%s') ) AND
         ( `last_date` >=  STR_TO_DATE('" . $date_start . "', '%Y-%m-%d %H:%i:%s') AND `last_date` <=  STR_TO_DATE('" . $date_end . "', '%Y-%m-%d %H:%i:%s') )
         ORDER BY event_id DESC",
         ARRAY_A
    );
  }


  public function GetAllDate($date_start, $date_end)
  {
    return $this->wpdb->get_results(
        "SELECT *
         FROM `" . $this->wpdb->prefix . "nmobtn_user_track`
         WHERE
         ( `first_date` >=  STR_TO_DATE('" . $date_start . "', '%Y-%m-%d %H:%i:%s') AND `first_date` <=  STR_TO_DATE('" . $date_end . "', '%Y-%m-%d %H:%i:%s') ) AND
         ( `last_date` >=  STR_TO_DATE('" . $date_start . "', '%Y-%m-%d %H:%i:%s') AND `last_date` <=  STR_TO_DATE('" . $date_end . "', '%Y-%m-%d %H:%i:%s') )
         ORDER BY event_id DESC",
         ARRAY_A
    );
  }

  public function GetCrossDate($date_start, $date_end)
  {
    return $this->wpdb->get_results(
        "SELECT *
         FROM `" . $this->wpdb->prefix . "nmobtn_user_track`
         WHERE
         ( ( `first_date` >=  STR_TO_DATE('" . $date_start . "', '%Y-%m-%d %H:%i:%s') AND
           `first_date` <=  STR_TO_DATE('" . $date_end . "', '%Y-%m-%d %H:%i:%s')
         ) OR
         ( `last_date` >=  STR_TO_DATE('" . $date_start . "', '%Y-%m-%d %H:%i:%s') AND
           `last_date` <=  STR_TO_DATE('" . $date_end . "', '%Y-%m-%d %H:%i:%s')
         ) ) OR
         ( ( STR_TO_DATE('" . $date_start . "', '%Y-%m-%d %H:%i:%s') >= `first_date` AND
           STR_TO_DATE('" . $date_end . "', '%Y-%m-%d %H:%i:%s') <= `first_date`
         ) OR
         (  STR_TO_DATE('" . $date_start . "', '%Y-%m-%d %H:%i:%s') >= `last_date` AND
            STR_TO_DATE('" . $date_end . "', '%Y-%m-%d %H:%i:%s') <= `last_date`
         ) )
         ORDER BY event_id DESC",
         ARRAY_A
    );
  }

  public function GetGroupAll($user_key, $count)
  {
      return $this->wpdb->get_results(
          "SELECT *
           FROM ( SELECT `meta_value` AS name, COUNT(`user_id`) AS counts
                  FROM `" . $this->wpdb->prefix . "usermeta`
                  WHERE `meta_key` = '" . $user_key . "' AND `meta_value` != ''
                  GROUP BY `meta_value`
                  ORDER BY COUNT(`user_id`) DESC
                ) AS t2
           WHERE `counts` > " . $count,
           ARRAY_A
      );
  }

  public function Add($event_id, $user_id)
  {
      date_default_timezone_set('Europe/Moscow');
      $date_now = date("Y-m-d H:i:s");

      $this->wpdb->get_results(
          "INSERT INTO `" . $this->wpdb->prefix . "nmobtn_user_track` (`user_id`, `event_id`, `first_date`, `last_date`)
           VALUES (" . $user_id . ", " . $event_id . ", '" . $date_now . "', '" . $date_now . "')"
      );
  }

  public function Update($event_id, $user_id)
  {
      date_default_timezone_set('Europe/Moscow');
      $date_now = date("Y-m-d H:i:s");
      $last_id = $this->wpdb->get_results("SELECT MAX(`id`) as idmax FROM `" . $this->wpdb->prefix . "nmobtn_user_track` WHERE user_id = {$user_id} AND event_id = {$event_id}", ARRAY_A)[0]['idmax'];

      if( !empty($last_id) )
      {
          $this->wpdb->get_results(
            "UPDATE `{$this->wpdb->prefix}nmobtn_user_track`
             SET `last_date`= '{$date_now}'
             WHERE `event_id` = {$event_id} AND `id` = {$last_id} AND `user_id` = {$user_id}"
          );
      }
  }

  public function Delete($event_id, $user_id)
  {
      $this->wpdb->get_results(
        "DELETE FROM `" . $this->wpdb->prefix . "nmobtn_user_track` WHERE user_id = " . $user_id . " AND `event_id` = " . $event_id
      );
  }
}
