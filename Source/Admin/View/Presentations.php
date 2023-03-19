<?php
    use nmobtn\DataBase;
    $presentations = DataBase::$tables['presentations']->GetAll();
    $count = 0;
 ?>

<div class="container">
    <h1 class="h3 text-center my-5">Настройка выступлений</h1>
    <div style=" max-width: 65%; margin: 0px auto;">
        <div style="display: flex;">
            <div style="width: 50%; border: 1px solid; border-radius:5px; padding:10px;">
                <form action="" method="post" enctype="multipart/form-data">
                    <?php wp_nonce_field('nmobtnPresentationsAddNonce-wpnp', 'nmobtnPresentationsAddNonce'); ?>
                    <label style="margin-top:20px; min-width: 50%;" for="nmobtn-number-presentations-add" class="form-label">Номер</label>
                    <br>
                    <input type="text" id="nmobtn-number-presentations-add" name="nmobtn-number-presentations-add">
                    <br><br>
                    <label style="margin-top:20px; min-width: 50%;" for="nmobtn-name-presentations-add" class="form-label">Имя</label>
                    <br>
                    <input type="text" id="nmobtn-name-presentations-add" name="nmobtn-name-presentations-add">
                    <br><br>
                    <label style="margin-top:20px; min-width: 50%;" for="nmobtn-lector-presentations-add" class="form-label">Лектор</label>
                    <br>
                    <input type="text" id="nmobtn-name-presentations-add" name="nmobtn-lector-presentations-add">
                    <br><br>
                    <label style="margin-top:20px; min-width: 50%;" for="nmobtn-event-presentations-add" class="form-label">Зал</label>
                    <br>
                    <input type="text" id="nmobtn-hall-presentations-add" name="nmobtn-event-presentations-add">
                    <br><br>
                    <label style="margin-top:20px; min-width: 50%;" for="nmobtn-datestart-presentations-add" class="form-label">Время начала</label>
                    <br>
                    <input type="text" id="nmobtn-datestart-presentations-add" name="nmobtn-datestart-presentations-add" placeholder="1970-01-01 00:00:00" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}">
                    <br><br>
                    <label style="margin-top:20px; min-width: 50%;" for="nmobtn-dateend-presentations-add" class="form-label">Время конца</label>
                    <br>
                    <input type="text" id="nmobtn-dateend-presentations-add" name="nmobtn-dateend-presentations-add" placeholder="1970-01-01 00:00:00" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}">
                    <br><br>
                    <label style="margin-top:20px; min-width: 50%;" for="nmobtn-csv-presentations-add" class="form-label">Или загрузить выступления файлом</label>
                    <br>
                    <input type="file" id="nmobtn-csv-presentations-add" name="nmobtn-csv-presentations-add">
                    <br><br>
                    <button type="submit" style="margin-top:20px;" class="button button-primary">Добавить выступление(я)</button>
                </form>
            </div>
            <div style="width: 50%;  border: 1px solid; border-radius:5px; padding:10px; margin-left:10px">
                <form action="" method="post">
                    <?php wp_nonce_field('nmobtnPresentationsRemoveNonce-wpnp', 'nmobtnPresentationsRemoveNonce'); ?>
                    <label style="margin-top:20px; min-width: 50%;" for="nmobtn-number-presentations-remove" class="form-label">Номер выступления</label>
                    <br>
                    <input type="text" id="nmobtn-number-presentations-remove" name="nmobtn-number-presentations-remove">
                    <br><br>
                    <button type="submit" style="margin-top:20px;" class="button button-primary">Удалить выступление</button>
                </form>
            </div>
        </div>

        <table style="width: 100%; text-align: center;margin-top:25px">
            <tr>
              <th>Код</th>
              <th>Название</th>
              <th>Лектор</th>
              <th>Код зала</th>
              <th>Время начала</th>
              <th>Время конца</th>
            </tr>
            <?php if( !empty( $presentations ) ): ?>
               <?php foreach ( $presentations as $presentation ) : ?>
                   <tr>
                     <td style="border: 1px solid;" ><?= $presentation['number']; ?></td>
                     <td style="border: 1px solid;" ><?= $presentation['name']; ?></td>
                     <td style="border: 1px solid;" ><?= $presentation['lector']; ?></td>
                     <td style="border: 1px solid;"><?= $presentation['event_id']; ?></td>
                     <td style="border: 1px solid;"><?= $presentation['start_date']; ?></td>
                     <td style="border: 1px solid;"><?= $presentation['end_date']; ?></td>
                   </tr>
                   <?php $count++; ?>
               <?php endforeach; ?>
            <?php endif; ?>
        </table>
        <p>Всего: <?= $count; ?></p>
      </div>
    </div>
