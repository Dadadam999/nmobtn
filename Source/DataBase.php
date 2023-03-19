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
use nmobtn\Tables\PresentationsTable;

class Database
{
   public static $wpdb;
   public static $tables;

   public static function init()
   {
       global $wpdb;
       self::$wpdb = $wpdb;

       self::$tables = [
         'presence' => new PresenceTable,
         'previewCheck' => new PreviewCheckTable,
         'events' => new EventsTable,
         'eventUsers' => new EventUsersTable,
         'track' => new TrackTable,
         'settings' => new SettingsTable,
         'visits' => new VisitsTable,
         'chat' => new ChatTable,
         'presentations' => new PresentationsTable
       ];
   }

   public static function Install()
   {
     foreach( self::$tables as $table )
       $table->Create();
   }

   public static function Uninstall()
   {
     foreach( self::$tables as $table )
       $table->Drop();
   }
}
?>
