<?php
  use nmobtn\DataBase;
  $meta_key_saved = json_decode( DataBase::$tables['settings']->Get('template_usermeta') );
  $meta_keys = DataBase::$tables['settings']->GetUserMetaKey( $meta_key_saved );
?>

<div class="container">
<h1 class="h3 text-center my-5">Настройка выводимых полей</h1>
<div style=" max-width: 65%; margin: 0px auto;">

<div style="display: flex;">

<div style="width: 50%; border: 1px solid; border-radius:5px; padding:10px;">
<form action="" method="post">
<?php wp_nonce_field('nmobtnUserMetaAddNonce-wpnp', 'nmobtnUserMetaAddNonce'); ?>
<label style="margin-top:20px; min-width: 50%;" for="nmobtn-usermeta-select-add" class="form-label">Выбрать метаполе:</label>
<br>
<select style="min-width: 50%;" name="nmobtn-usermeta-select-add" class="form-control form-control-sm">
<option disabled selected value="-1">Выберите</option>

<?php if( !empty( $meta_keys ) ) : ?>
   <?php foreach ( $meta_keys as $row): ?>
       <option value="<?= $row['meta_key'];  ?>"><?= $row['meta_key']; ?></option>
   <?php endforeach; ?>
<?php endif; ?>

</select>
<br><br>
<label style="margin-top:20px; min-width: 50%;" for="nmobtn-name-usermeta-add" class="form-label">Заголовок</label>
<br>
<input type="text" id="nmobtn-name-usermeta-add" name="nmobtn-name-usermeta-add">
<br><br>
<button type="submit" style="margin-top:20px;" class="button button-primary">Добавить поле</button>
</form>
</div>

<div style="width: 50%;  border: 1px solid; border-radius:5px; padding:10px; margin-left:10px">
<form action="" method="post">
<?php wp_nonce_field('nmobtnUserMetaRemoveNonce-wpnp', 'nmobtnUserMetaRemoveNonce'); ?>
<label style="margin-top:20px; min-width: 50%;" for="nmobtn-usermeta-select-remove" class="form-label">Выбрать метаполе:</label>
<br>
<select style="min-width: 50%;" name="nmobtn-usermeta-select-remove" class="form-control form-control-sm">
<option disabled selected value="-1">Выберите</option>

<?php if( !empty( $meta_key_saved ) ) : ?>
    <?php foreach ( $meta_key_saved as $key => $value) : ?>
        <option value="<?= $key ?>"><?= $value ?></option>
     <?php endforeach; ?>
<?php endif; ?>

</select>
<br><br>
<button type="submit" style="margin-top:20px;" class="button button-primary">Удалить поле</button>
</form>
</div>
</div>

<table style="width: 100%; text-align: center;margin-top:25px">
<tr>
<th>Мета-ключ</th>
<th>Заголовок</th>
</tr>

<?php if( !empty( $meta_key_saved ) ) : ?>
   <?php foreach ( $meta_key_saved as $key => $value ) : ?>
       <tr>
       <td style="border: 1px solid;" ><?= $key; ?></td>
       <td style="border: 1px solid;"><?= $value; ?></td>
       </tr>
    <?php endforeach; ?>
<?php endif; ?>

</table>
</div>
</div>
