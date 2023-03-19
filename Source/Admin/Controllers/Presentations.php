<?php
/**
 * @package nmobtn
 * @author Bogdanov Andrey (swarzone2100@yandex.ru)
 */
 namespace nmobtn\Admin\Controllers;
 use nmobtn\DataBase;

class Presentations
{
    public function __construct()
    {
        add_action('admin_menu', function()
        {
            add_submenu_page(
                'settings_nmobtn',
                'Выступления',
                'Выступления',
                'administrator',
                'settings_nmobtn_4',
                array($this, 'nmobtnSettingsPresentationsCallback')
            );
        });

        $this->postCallback();
    }

    public function nmobtnSettingsPresentationsCallback()
    {
        ob_start();
        include  WP_PLUGIN_DIR . '/nmobtn/Source/Admin/View/Presentations.php';
        echo ob_get_clean();
    }

    private function postCallback()
    {
        add_action('plugins_loaded', function()
        {
            if( !empty( $_POST['nmobtnPresentationsAddNonce'] ) )
            {
                $file = $_FILES['nmobtn-csv-presentations-add'];

                if( !empty( $file['tmp_name'] ) )
                {
                    if ( $file['error'] !== UPLOAD_ERR_OK || $file['error'] === UPLOAD_ERR_NO_FILE )
                    {
                        echo '<p style="margin-left:190px;">Файл не загрузился из-за ошибки: ' . $file['error'] . '</p><br>';
                        return '';
                    }

                    $file_content = file_get_contents( $file['tmp_name'] );

                    $file_rows = explode("\r\n", $file_content);

                    foreach ($file_rows as $row)
                    {
                        if( !empty($row) )
                        {
                            $cells = explode(';', $row);
                            DataBase::$tables['presentations']->Add( $cells[0], $cells[1], $cells[2], $cells[3], $cells[4], $cells[5] );
                        }
                    }

                    return '';
                }

                if( empty( $_POST['nmobtn-name-presentations-add'] ) || !isset( $_POST['nmobtn-name-presentations-add'] ) || !isset( $_POST['nmobtn-datestart-presentations-add'] ) || !isset( $_POST['nmobtn-dateend-presentations-add'] ) )
                {
                    echo 'Данные введны не верно!';
                    return '';
                }

                DataBase::$tables['presentations']->Add(
                    $_POST['nmobtn-number-presentations-add'],
                    $_POST['nmobtn-name-presentations-add'],
                    $_POST['nmobtn-lector-presentations-add'],
                    $_POST['nmobtn-event-presentations-add'],
                    $_POST['nmobtn-datestart-presentations-add'],
                    $_POST['nmobtn-dateend-presentations-add']
                );
            }

            if( !empty( $_POST['nmobtnPresentationsRemoveNonce'] ) )
            {
                if( !isset( $_POST['nmobtn-number-presentations-remove'] ) )
                {
                    echo 'Код выступления не вписан!';
                    return '';
                }

                DataBase::$tables['presentations']->DeleteFromNumber( $_POST['nmobtn-number-presentations-remove'] );
            }
        });
    }
}
