<?php
/**
 * @package nmobtn
 * @author Bogdanov Andrey (swarzone2100@yandex.ru)
 */

namespace nmobtn;
use WP_REST_Request;
use wpdb;
use nmobtn\Tables\PresenceTable;
use nmobtn\Tables\PreviewCheckTable;
use nmobtn\Tables\EventsTable;
use nmobtn\Tables\EventUsersTable;
use nmobtn\Tables\TrackTable;
use nmobtn\Tables\SettingsTable;
use nmobtn\Tables\VisitsTable;
use nmobtn\Tables\ChatTable;

class Database
{
   private $wpdb;
   public $tables;

   public function __construct()
   {
       global $wpdb;
       $this->wpdb = $wpdb;

       $this->tables =
       [
         'presence' => new PresenceTable,
         'previewCheck' => new PreviewCheckTable,
         'events' => new EventsTable,
         'eventUsers' => new EventUsersTable,
         'track' => new TrackTable,
         'settings' => new SettingsTable,
         'visits' => new VisitsTable,
         'chat' => new ChatTable
       ];
   }

   public function Install()
   {
     foreach ($this->tables as $table)
       $table->Create();
   }

   public function Uninstall()
   {
     foreach ($this->tables as $table)
       $table->Drop();
   }
}
?>
