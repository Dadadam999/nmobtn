<?php
    use nmobtn\DataBase;
    $events = DataBase::$tables['events']->GetAll();
 ?>
<div class="container">
    <h1 class="h3 text-center my-5">Залы трансляций</h1>
    <div style=" max-width: 500px; margin: 0px auto;"></div>
    <div class="wrapper">
        <div class="">
            <p>Примеры шорткодов</p>
            <div class="">
              [nmo-subsbu-event-check event_id="23863" url="/event-directory/vserossijskaja-vyezdnaja-konferencija-konsorciuma-5p-detskaja-medicina-saratov"] - этот код нужен для проверки записи пользователя на мероприятие через плагин subsby
              <div class="">
            </div>
              [member-message] - шорткод, который выводит контент только для зарегистированных пользователей
            </div>
            <div class="">
              [nmo-button event_id="1" event_name="День1Зал1" id="prs-cnf-button" class="w-btn us-btn-style_1 icon_atleft"] - шорткод создающий кнопку нмо
            </div>
            <div class="">
              [nmo-online event_id="1"] - счётчик онлайн
            </div>
            <div class="">
              [nmo-chat event_id="1"] - чат
            </div>
            <div class="">
              [/member-message]
            </div>
            <div class="">
              [nmo-button-on-event event_id="13" css-class="w-btn us-btn-style_5 icon_atleft" url="/testovoe-meroprijatie" title="Зал 4" show_lector="true" ] - шорткод создающий ссылку на зал с названием текущей лекции.
            </div>
        </div>
    </div>
    <div class="wrapper">
      <table style="width: 100%; text-align: center;margin-top:25px">
          <?php if( !empty( $events ) ): ?>
             <tr>
                <th>id</th>
                <th>Название</th>
             </tr>
             <?php foreach ( $events as $event ) : ?>
                 <tr>
                   <td style="border: 1px solid;" ><?= $event['event_id']; ?></td>
                   <td style="border: 1px solid;" ><?= $event['name']; ?></td>
                 </tr>
                 <?php $count++; ?>
             <?php endforeach; ?>
          <?php else: ?>
            <p class="info">Мероприятия пока не созданы!</p>
          <?php endif; ?>
      </table>
    </div>
</div>
