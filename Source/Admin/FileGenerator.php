<?php
/**
 * @package nmobtn
 * @author Bogdanov Andrey (swarzone2100@yandex.ru)
 */
namespace nmobtn\Admin;
use nmobtn\DataBase;

class FileGenerator
{
    public static function getRegion( $city )
    {
        $query = DataBase::$wpdb->prepare(
            "SELECT `region` FROM `wp_cities` WHERE `city` = %s LIMIT 1",
            $city
        );

        return DataBase::$wpdb->get_row( $query, ARRAY_A )['region'];
    }

    public static function getAudienceSubsbu( $event_id )
    {
        $table = DataBase::$wpdb->prefix . "subsbu_audience";
        $subs = DataBase::$wpdb->get_results(
            "SELECT subscribers FROM `{$table}` WHERE post_id = {$event_id}",
             ARRAY_A
        );

        if(!empty($subs[0]))
            return explode(";", $subs[0]["subscribers"]);

        return null;
    }

    public static function getNmo($event_id, $date_start, $date_end)
    {
        $head_table = [
             "User ID",
             "Присутсвовал времени на мероприятии",
             "Присутсвовал времени всего",
             "Кол-во подтверждений",
             "Дата регистрации",
             "Адрес электронной почты (email)"
        ];
        $meta_key_saved = json_decode( DataBase::$tables['settings']->Get('template_usermeta') );

        foreach ( $meta_key_saved as $value)
            array_push($head_table, $value);

        $file = implode(";", $head_table) . "\r\n";

        if( $event_id === 'all' )
            $presence = DataBase::$tables['presence']->GetGroupAll($date_start, $date_end);
        else
            $presence = DataBase::$tables['presence']->GetGroup($event_id, $date_start, $date_end);

        $count=0;

        foreach ( $presence as $click )
        {
            $user_track = DataBase::$tables['track']->Get($event_id, $click['user_id']);
            $time = ( int ) DataBase::$tables['track']->GetSumMinute($click['user_id'], $event_id);
            $timeAll = ( int ) DataBase::$tables['track']->GetSumMinuteAll($click['user_id'], $date_start, $date_end );

            $content = [
                $click['user_id'],
                $time,
                $timeAll,
                $click['clicks'],
                get_userdata( $click['user_id'] )->user_registered,
                get_userdata( $click['user_id'] )->user_email
            ];

            foreach ( $meta_key_saved as $key => $value)
            {
                $meta = get_user_meta( $click['user_id'], $key, true );
                $meta = str_replace("\r\n", " ", $meta);

                if( $key == 'region' )
                  $meta = FileGenerator::getRegion( get_user_meta( $click['user_id'], 'city', true ) );

                array_push($content, $meta);
            }

            $count++;
            $file .= implode(";", $content) . "\r\n";
        }

        return $file;
    }

    public static function getNmoAll($event_id, $date_start, $date_end)
    {
        $head_table = [
             "Мероприятие",
             "User ID",
             "Присутсвовал времени на мероприятии",
             "Присутсвовал времени всего",
             "Дата нажатия",
             "Дата регистрации",
             "Адрес электронной почты (email)"
        ];
        $meta_key_saved = json_decode( DataBase::$tables['settings']->Get('template_usermeta') );

        foreach ( $meta_key_saved as $value)
            array_push($head_table, $value);

        $file = implode(";", $head_table) . "\r\n";

        if( $event_id === 'all' )
            $presence = DataBase::$tables['presence']->GetAll($date_start, $date_end);
        else
            $presence = DataBase::$tables['presence']->GetGroup($event_id, $date_start, $date_end);

        $count=0;

        foreach ( $presence as $click )
        {
            $user_track = DataBase::$tables['track']->Get($event_id, $click['user_id']);
            $time = ( int ) DataBase::$tables['track']->GetSumMinute($click['user_id'], $event_id);
            $timeAll = ( int ) DataBase::$tables['track']->GetSumMinuteAll($click['user_id'], $date_start, $date_end );

            $content = [
                DataBase::$tables['events']->Get($click['event_id'])['name'],
                $click['user_id'],
                $time,
                $timeAll,
                $click['presence_time'],
                get_userdata( $click['user_id'] )->user_registered,
                get_userdata( $click['user_id'] )->user_email
            ];

            foreach ( $meta_key_saved as $key => $value)
            {
                $meta = get_user_meta( $click['user_id'], $key, true );
                $meta = str_replace("\r\n", " ", $meta);

                if( $key == 'region' )
                  $meta = FileGenerator::getRegion( get_user_meta( $click['user_id'], 'city', true ) );

                array_push($content, $meta);
            }

            $count++;
            $file .= implode(";", $content) . "\r\n";
        }

        return $file;
    }

    public static function getUsers($event_id, $date_start, $date_end)
    {
        $head_table = [
             "User ID",
             "Мероприятие",
             "Дата захода",
             "Дата выхода",
             "Дата регистрации",
             "Адрес электронной почты (email)"
        ];

        $meta_key_saved = json_decode( DataBase::$tables['settings']->Get('template_usermeta') );

        foreach ( $meta_key_saved as $value)
            array_push($head_table, $value);

        $file = implode(";", $head_table) . "\r\n";

        if( $event_id === 'all' )
            $responce = DataBase::$tables['track']->GetAllDate($date_start, $date_end);
        else
            $responce = DataBase::$tables['track']->GetEventDate($event_id, $date_start, $date_end);

        $count=0;

        foreach ( $responce as $row )
        {
            $content = [
                $row['user_id'],
                DataBase::$tables['events']->Get($row['event_id'])['name'],
                $row['first_date'],
                $row['last_date'],
                get_userdata( $row['user_id'] )->user_registered,
                get_userdata( $row['user_id'] )->user_email
            ];

            foreach ( $meta_key_saved as $key => $value)
            {
                $meta = get_user_meta( $row['user_id'], $key, true );
                $meta = str_replace("\r\n", " ", $meta);

                if( $key == 'region' )
                  $meta = FileGenerator::getRegion( get_user_meta( $row['user_id'], 'city', true ) );

                array_push($content, $meta);
            }

            $count++;
            $file .= implode(";", $content) . "\r\n";
        }

        return $file;
    }

    public static function getGroup($meta_key, $count, $head_key)
    {
        $head_table = [
             $head_key,
             'Количество',
        ];

        $file = implode(";", $head_table) . "\r\n";
        $responce = DataBase::$tables['track']->GetGroupAll($meta_key, $count);

        foreach ( $responce as $row )
        {
            if( empty($row['counts']) || empty($row['name']) )
                continue;

            $content = [
                $row['name'],
                $row['counts'],
            ];

            $file .= implode(";", $content) . "\r\n";
        }

        return $file;
    }

    public static function getVisits($event_id, $date_start, $date_end, $url)
    {
        $head_table = [
             "User ID",
             "Дата перехода",
             "URL",
             "Дата регистрации",
             "Адрес электронной почты (email)"
        ];
        $meta_key_saved = json_decode( DataBase::$tables['settings']->Get('template_usermeta') );

        foreach ( $meta_key_saved as $value)
            array_push($head_table, $value);

        $file = implode(";", $head_table) . "\r\n";

        if( empty( $url ) )
            $responce = DataBase::$tables['visits']->GetToDate( $date_start, $date_end );
        else
            $responce = DataBase::$tables['visits']->GetToUrl( $url );

        $count=0;

        foreach ( $responce as $row )
        {
            $content = [
                $row['user_id'],
                $row['visit_date'],
                $row['url'],
                get_userdata( $row['user_id'] )->user_registered,
                get_userdata( $row['user_id'] )->user_email
            ];

            foreach ( $meta_key_saved as $key => $value)
            {
                $meta = get_user_meta( $row['user_id'], $key, true );
                $meta = str_replace("\r\n", " ", $meta);

                if( $key == 'region' )
                  $meta = FileGenerator::getRegion( get_user_meta( $row['user_id'], 'city', true ) );

                array_push($content, $meta);
            }

            $count++;
            $file .= implode(";", $content) . "\r\n";
        }

        return $file;
    }

    public static function getChat($event_id, $date_start, $date_end)
    {
        $head_table = [
             "Меропритие",
             "User ID",
             "Дата написания",
             "Сообщение",
             "Дата регистрации",
             "Адрес электронной почты (email)"
        ];
        $meta_key_saved = json_decode( DataBase::$tables['settings']->Get('template_usermeta') );

        foreach ( $meta_key_saved as $value)
            array_push($head_table, $value);

        $file = implode(";", $head_table) . "\r\n";

        if( $event_id == 'all' )
          $responce = DataBase::$tables['chat']->GetToAll($event_id);
        else
          $responce = DataBase::$tables['chat']->GetToEvent($event_id);

        $count=0;

        foreach ( $responce as $row )
        {
            $content = [
                DataBase::$tables['events']->Get($row['event_id'])['name'],
                $row['user_id'],
                $row['write_date'],
                $row['message'],
                get_userdata( $row['user_id'] )->user_registered,
                get_userdata( $row['user_id'] )->user_email
            ];

            foreach ( $meta_key_saved as $key => $value)
            {
                $meta = get_user_meta( $row['user_id'], $key, true );
                $meta = str_replace("\r\n", " ", $meta);

                if( $key == 'region' )
                  $meta = FileGenerator::getRegion( get_user_meta( $row['user_id'], 'city', true ) );

                array_push($content, $meta);
            }

            $count++;
            $file .= implode(";", $content) . "\r\n";
        }

        return $file;
    }

    public static function getMain($event_id, $date_start, $date_end)
    {
        $head_table = [
             "User ID",
             "Мероприятие",
             "Время просмотра",
             "Клики НМО",
             "Форма участия",
             "Дата регистрации",
             "Адрес электронной почты (email)"
        ];

        $meta_key_saved = json_decode( DataBase::$tables['settings']->Get('template_usermeta') );

        foreach ( $meta_key_saved as $value)
            array_push($head_table, $value);

        $file = implode(";", $head_table) . "\r\n";

        if( $event_id === 'all' )
        {
            $tracks = DataBase::$tables['track']->GetAllDate($date_start, $date_end);
            $presence = DataBase::$tables['presence']->GetGroupAll($date_start, $date_end);
            $users = get_users( [ 'role' => 'subscriber', 'include' => FileGenerator::getAudienceSubsbu( '26022' ) ] );
        }
        else
        {
            $tracks = DataBase::$tables['track']->GetEventDate($event_id, $date_start, $date_end);
            $presence = DataBase::$tables['presence']->GetGroup($event_id, $date_start, $date_end);
            $users_id = DataBase::$tables['eventUsers']->Get($event_id);
            $ids = array();

            foreach ( $users_id as $user )
              array_push( $ids, $user['user_id'] );

            $users = get_users( [ 'role' => 'subscriber', 'include' => $ids ] );
        }
        $count=0;

        foreach ( $users as $user )
        {
            $clicks = '';
            $duration = ( int ) DataBase::$tables['track']->GetSumMinuteAll($user->ID, $date_start, $date_end );

            foreach ( $presence as $click )
            {
              if( $click['user_id'] == $user->ID )
              {
                  $clicks = $click['clicks'];
                  break;
              }
            }

            $is_auth = $duration == 0 ? 'Зарегистрирован' : 'Участвовал';

            $content = [
                $user->ID,
                DataBase::$tables['events']->Get($event_id)['name'],
                $duration,
                $clicks,
                $is_auth,
                get_userdata( $user->ID )->user_registered,
                get_userdata( $user->ID )->user_email
            ];

            foreach ( $meta_key_saved as $key => $value)
            {
                $meta = get_user_meta(  $user->ID, $key, true );
                $meta = str_replace("\r\n", " ", $meta);

                if( $key == 'region' )
                  $meta = FileGenerator::getRegion( get_user_meta( $user->ID, 'city', true ) );

                array_push($content, $meta);
            }

            $count++;
            $file .= implode(";", $content) . "\r\n";
        }

        return $file;
    }

    public static function getPresentations()
    {
        $head_table = [
             "Симпозиум",
             "Зал",
             "Модераторы",
             "Дата начала",
             "Дата конца",
             "Количество просмотров"
        ];

        $meta_key_saved = json_decode( DataBase::$tables['settings']->Get('template_usermeta') );
        $symposiums_saved = json_decode( DataBase::$tables['settings']->Get('template_symposiums') );

        foreach ( $meta_key_saved as $value)
            array_push($head_table, $value);

        $file = implode(";", $head_table) . "\r\n";

        foreach ( $symposiums_saved as $simposium_name => $simposium )
        {
            $simposium->date_start = date( 'Y-m-d H:i:s', strtotime($simposium->date_start) );
            $simposium->date_end = date( 'Y-m-d H:i:s', strtotime($simposium->date_end) );
            $responce = DataBase::$tables['track']->GetCrossDate($simposium->date_start, $simposium->date_end);

            foreach ($responce as $row)
            {
                $content = [
                    $simposium_name,
                    $simposium->hall,
                    $simposium->date_start,
                    $simposium->date_end,
                    $row['user_id'],
                    $row['first_date'],
                    $row['last_date'],
                    get_userdata( $row['user_id'] )->user_registered,
                    get_userdata( $row['user_id'] )->user_email
                ];

                foreach ( $meta_key_saved as $key => $value)
                {
                    $meta = get_user_meta( $row['user_id'], $key, true );
                    $meta = str_replace("\r\n", " ", $meta);

                    if( $key == 'region' )
                      $meta = FileGenerator::getRegion( get_user_meta( $row['user_id'], 'city', true ) );

                    array_push($content, $meta);
                }

                $file .= implode(";", $content) . "\r\n";
            }
        }

        return $file;
    }
}
