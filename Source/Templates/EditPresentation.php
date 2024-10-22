<?php
  use nmobtn\DataBase;
  $presentations = DataBase::$tables['presentations']->GetFromEvent( $event_id );
?>
<style>
    .wrapper {
      display: flex;
      gap: 10px;
      margin: 10px;
      padding: 10px;
    }

    .wrapper > form {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .wrapper > form {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .date-wrapper {
       display: flex;
       gap: 5px;
    }
</style>
<div class="wrapper">
  <form enctype="application/x-www-form-urlencoded" method="post">
  <?php wp_nonce_field('nmobtnEditPresentations-wpnp', 'nmobtnEditPresentations-wpnp'); ?>
  <label for="presentation-select">Выберите доклад:</label>

  <select id="presentation-select" name="nmobtn-name">
    <?php foreach ($presentations as $presentation) : ?>
      <option value="<?= $presentation['id']; ?>"><?= $presentation['name']; ?></option>
    <?php endforeach; ?>
  </select>

  <label for="start-date">Дата начала:</label>
  <div class="date-wrapper">
    <input type="date" id="start-date" name="nmobtn-date_start" value="<?= date('Y-m-d'); ?>">
    <select id="start-time-hours" name="nmobtn-start-time-hours">
      <?php for ($i = 0; $i <= 23; $i++) { ?>
        <option value="<?php echo str_pad($i, 2, "0", STR_PAD_LEFT); ?>"><?php echo str_pad($i, 2, "0", STR_PAD_LEFT); ?></option>
      <?php } ?>
    </select>
    <select id="start-time-minutes" name="nmobtn-start-time-minutes">
      <?php for ($i = 0; $i <= 59; $i+=5) { ?>
        <option value="<?php echo str_pad($i, 2, "0", STR_PAD_LEFT); ?>"><?php echo str_pad($i, 2, "0", STR_PAD_LEFT); ?></option>
      <?php } ?>
    </select>
  </div>

  <label for="end-date">Дата конца:</label>
  <div class="date-wrapper">
    <input type="date" id="end-date" name="nmobtn-date_end" value="<?= date('Y-m-d'); ?>">
    <select id="end-time-hours" name="nmobtn-end-time-hours">
      <?php for ($i = 0; $i <= 23; $i++) { ?>
        <option value="<?php echo str_pad($i, 2, "0", STR_PAD_LEFT); ?>"><?php echo str_pad($i, 2, "0", STR_PAD_LEFT); ?></option>
      <?php } ?>
    </select>
    <select id="end-time-minutes" name="nmobtn-end-time-minutes">
      <?php for ($i = 0; $i <= 59; $i+=5) { ?>
        <option value="<?php echo str_pad($i, 2, "0", STR_PAD_LEFT); ?>"><?php echo str_pad($i, 2, "0", STR_PAD_LEFT); ?></option>
      <?php } ?>
    </select>
  </div>
  <input type="submit" name="nmobtn-submit" value="Изменить даты">
</form>
</div>

<div class="wrapper">
  <table>
    <thead>
      <tr>
        <th>Код</th>
        <th>Название доклада</th>
        <th>Модератор</th>
        <th>Дата начала</th>
        <th>Дата конца</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($presentations as $presentation) { ?>
        <tr>
          <td><?php echo $presentation['number']; ?></td>
          <td><?php echo $presentation['name']; ?></td>
          <td><?php echo $presentation['lector']; ?></td>
          <td><?php echo $presentation['start_date']; ?></td>
          <td><?php echo $presentation['end_date']; ?></td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>
