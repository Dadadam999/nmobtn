<?php
/**
 * @package nmobtn
 * @author Bogdanov Andrey (swarzone2100@yandex.ru)
 */
namespace nmobtn\Tables;
use nmobtn\Interfaces\ITable;

class PresentationsTable implements ITable
{
  private $wpdb;
  private $name;

  public function __construct()
  {

      global $wpdb;
      $this->wpdb = $wpdb;
      $this->name = $this->wpdb->prefix . "nmobtn_presentations";
  }

  public function Create()
  {
      $table_exists = $this->wpdb->get_var( "SHOW TABLES LIKE '{$this->name}'" ) === $this->name;

      if (!$table_exists)
      {
          $this->wpdb->query(
              "CREATE TABLE {$this->name}
              (
                  id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                  event_id INT(6) UNSIGNED NOT NULL,
                  number INT(6) NOT NULL,
                  name VARCHAR(2048),
                  lector VARCHAR(255),
                  start_date DATETIME,
                  end_date DATETIME,
                  UNIQUE KEY id (id)
              )"
          );
      }
  }

  public function Drop()
  {
      $table_exists = $this->wpdb->get_var( "SHOW TABLES LIKE '{$this->name}'" ) === $this->name;

      if( $table_exists )
         $this->wpdb->query("DROP TABLE `{$this->name}`");
  }

  public function GetLast( $event_id )
  {
      $query = $this->wpdb->prepare(
          "SELECT * FROM {$this->name} WHERE event_id = %d ORDER BY id DESC LIMIT 1",
          $event_id
      );

      return $this->wpdb->get_row( $query, ARRAY_A );
  }

  public function Get( $id )
  {
      return $this->wpdb->get_row(
          $this->wpdb->prepare(
              "SELECT * FROM {$this->name} WHERE id = %d",
              $id
          ),
          ARRAY_A
      );
  }

  public function GetFromEvent( $event_id )
  {
      return $this->wpdb->get_results(
          $this->wpdb->prepare(
              "SELECT * FROM {$this->name} WHERE event_id = %d",
              $event_id
          ),
          ARRAY_A
      );
  }

  public function GetAll()
  {
      return $this->wpdb->get_results(
          "SELECT * FROM {$this->name}",
          ARRAY_A
      );
  }

  public function GetFromDate($event_id, $date_start, $date_end)
  {
      $query = $this->wpdb->prepare(
          "SELECT * FROM {$this->name} WHERE ( start_date BETWEEN %s AND %s ) AND ( end_date BETWEEN %s AND %s )",
          $date_start,
          $date_end,
          $date_start,
          $date_end
      );

      return $this->wpdb->get_results($query, ARRAY_A);
  }

  public function GetNow($event_id, $date)
  {
      $formatted_date = date( 'Y-m-d H:i:s', strtotime( $date ) );

      $query = $this->wpdb->prepare(
          "SELECT * FROM {$this->name} WHERE ( %s BETWEEN start_date AND end_date ) AND `event_id` = %d",
          $formatted_date,
          $event_id
      );

      return $this->wpdb->get_row( $query, ARRAY_A );
  }

  public function Add( $number, $name , $lector, $event_id, $date_start, $date_end )
  {
      $this->wpdb->insert(
          $this->name,
          [
              'number' => $number,
              'event_id' => $event_id,
              'name' => $name,
              'lector' => $lector,
              'start_date' => $date_start,
              'end_date' => $date_end
          ]
      );
  }

  public function Update( $id, $number, $name , $lector, $event_id, $date_start, $date_end )
  {
      $this->wpdb->update(
          $this->name,
          [
              'number' => $number,
              'event_id' => $event_id,
              'name' => $name,
              'lector' => $lector,
              'start_date' => $date_start,
              'end_date' => $date_end
          ],
          ['id' => $id]
      );
  }

  public function Delete( $id )
  {
      $this->wpdb->query( "DELETE FROM `{$this->name}` WHERE `id` = {$id}" );
  }

  public function DeleteFromNumber( $number )
  {
      $this->wpdb->query( "DELETE FROM `{$this->name}` WHERE `number` = {$number}" );
  }
}
