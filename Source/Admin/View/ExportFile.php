<?php
  use nmobtn\DataBase;
  $events = DataBase::$tables['events']->GetAll();
  $date_now = date('Y-m-d H:i:s');
?>

<div class="container">
    <h1 class="h3 text-center my-5">Скачать статистику по НМО</h1>
    <div style=" max-width: 500px; margin: 0px auto;">
        <form action="" method="post">
            <?php wp_nonce_field('nmobtnDownloadFileNonce-wpnp', 'nmobtnDownloadFileNonce'); ?>
            <label style="margin-top:20px; min-width: 50%;" for="nmobtn-file-template" class="form-label">Выберите шаблон:</label>
            <br>
            <select style="min-width: 50%;" name="nmobtn-file-template" class="form-control form-control-sm">
                <option disabled selected value="-1">Выберите</option>
                <option value="1">НМО</option>
                <option value="2">Слушатели</option>
                <option value="3">География</option>
                <option value="4">По специальностям</option>
                <option value="5">Посещения</option>
                <option value="6">Чат</option>
                <option value="7">Общая</option>
                <option value="8">Симпозиумы</option>
                <option value="9">НМО суммы</option>
            </select>
            <br><br>
            <label style="margin-top:20px; min-width: 50%;" for="nmobtn-file-select" class="form-label">Выберите мероприятие:</label>
            <br>
            <select style="min-width: 50%;" name="nmobtn-file-select" class="form-control form-control-sm">
                <option disabled selected value="-1">Выберите</option>
                <option value="all">Все</option>
                <?php if( !empty( $events ) ) : ?>
                    <?php foreach ($events as $event) : ?>
                        <option value="<?= $event['event_id']; ?>"><?= $event['name']; ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <br><br>
            <label style="margin-top:20px; min-width: 50%;" for="nmobtn-start-date" class="form-label">С даты:</label>
            <br>
            <input type="datetime-local" id="nmobtn-start-date" name="nmobtn-start-date" value="<?= $date_now; ?>">
            <br><br>
            <label style="margin-top:20px; min-width: 50%;" for="nmobtn-end-date" class="form-label">По дату:</label>
            <br>
            <input type="datetime-local" id="nmobtn-end-date" name="nmobtn-end-date" value="<?= $date_now; ?>">
            <br><br>
            <label style="margin-top:20px; min-width: 50%;" for="nmobtn-url-filter" class="form-label">URL:</label>
            <br>
            <input type="text" id="nmobtn-url-filter" name="nmobtn-url-filter" value="">
            <br><br>
            <button type="submit" style="margin-top:20px;" class="button button-primary">Выгрузить CSV</button>
        </form>
    </div>
</div>
