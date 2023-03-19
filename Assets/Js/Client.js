const nmobtnClient = {
    emec: 0,
    listener: false,
    status_click: false,

    close: async (buttonId, event, user, key) => {
        document.getElementById("blockcentr").style.display = 'none'; //$(".blockcentr").slideToggle("2000");
        document.getElementById("blockall").remove(); //$(".blockall").remove();
        nmobtnClient.status_click = false;
    },

    click: async (buttonId, event, user, key) => {
        const button = document.getElementById(buttonId);

        const span = document.createElement('span');
        span.setAttribute('id', buttonId+'-wait');

        span.innerHTML = 'Подождите...';

        document.getElementById(buttonId+'-content-0').setAttribute('style', 'display: none;');
        document.getElementById(buttonId+'-content-1').setAttribute('style', 'display: none;');

        button.appendChild(span);

        const data = new FormData;

        data.append('nmobtn-button-event', event);
        data.append('nmobtn-button-user', user);
        data.append('nmobtn-button-key', key);

        const request = await fetch(
            '/wp-json/nmobtn/v1/click',
            {
                method: 'POST',
                credentials: 'include',
                body: data
            }
        );

        if (request.ok)
        {
            const answer = await request.json();

            if (answer.code == 0) console.log('nmobtnClient.click(): success.');
            else console.error('nmobtnClient.click(): API error.');
        }
        else console.error('nmobtnClient.click(): network error.');

        nmobtnClient.status_click = false;

        button.removeChild(span);
        document.getElementById(buttonId+'-content-0').removeAttribute('style');
        document.getElementById("blockcentr").style.display = 'none';
        document.getElementById("blockall").remove();
    },

    sendnmo: async (event) => {

        send_nmo_span = document.getElementById('send-nmo-span');
        send_nmo_span.innerHTML = 'Подождите...';
        const data = new FormData;

        data.append('nmobtn-button-event', event);

        const request = await fetch(
            '/wp-json/nmobtn/v1/sendnmo',
            {
                method: 'POST',
                credentials: 'include',
                body: data
            }
        );

        if (request.ok)
        {
            const answer = await request.json();

            if (answer.code == 0) console.log('nmobtnClient.sendnmo(): success.');
            else console.error('nmobtnClient.sendnmo(): API error.');
        }
        else console.error('nmobtnClient.sendnmo(): network error.');

        send_nmo_span.innerHTML = 'Разослать уведомления НМО';
    },

    checknmo: async (event, user) => {
        const data = new FormData;

        data.append('nmobtn-button-event', event);
        data.append('nmobtn-button-user', user);

        const request = await fetch(
            '/wp-json/nmobtn/v1/checknmo',
            {
                method: 'POST',
                credentials: 'include',
                body: data
            }
        );

        if (request.ok)
        {
            const answer = await request.json();

            if (answer.code == 0) {
              console.log('nmobtnClient.checknmo(): success.');

              if(answer.status == 1  && nmobtnClient.status_click == false)
              {
                nmobtnClient.status_click = true;

                document.getElementById("blockcentr").style.display = 'block';

                if(document.getElementById("blockall"))
                  document.getElementById("blockall").remove();
                else
                  document.getElementById("tytoknoall").innerHTML = '<div id="blockall" class="blockall"></div>';
              }
            }
            else
              console.error('nmobtnClient.checknmo(): API error.');
        }
        else
          console.error('nmobtnClient.checknmo(): network error.');
    },

    chatsend: async (event, user, name, key) => {
        const data = new FormData;
        const message = document.getElementById('area-msg-chat');
        const button = document.getElementById('btn-msg-chat');
        const chat = document.getElementById('chat-messages');
        let button_color = button.style.backgroundColor;
        button.innerHTML = 'Отправка';
        button.setAttribute('disabled', true);

        data.append('nmobtn-chat-event', event);
        data.append('nmobtn-chat-user', user);
        data.append('nmobtn-chat-name', name);
        data.append('nmobtn-chat-message', message.value);
        data.append('nmobtn-chat-key', key);

        if( message.value == '' )
        {
            button.innerHTML = 'Пустое поле';
            button.style.backgroundColor = '#e37606';
            button.removeAttribute('disabled');
        }
        else
        {
            const request = await fetch(
                '/wp-json/nmobtn/v1/chatsend',
                {
                    method: 'POST',
                    credentials: 'include',
                    body: data
                }
            );

            if (request.ok)
            {
                const answer = await request.json();

                if (answer.code == 0)
                {
                      console.log('chat send: success.');
                      button.innerHTML = 'Отправлено';
                      button.style.backgroundColor = '#6acd72';
                      chat.innerHTML += nmobtnClient.addmessage(answer.id , name, answer.date, message.value);
                      chat.scrollTop = chat.scrollHeight;
                      message.value = '';
                }
                else
                {
                      console.log('chat send: error.');
                      button.innerHTML = 'Ошибка';
                      button.style.backgroundColor = '#cd6a6a';
                }
            }
            else
            {
                  console.log('chat send: error.');
                  button.innerHTML = 'Ошибка';
                  button.style.backgroundColor = '#cd6a6a';
            }
        }

        setTimeout(function() {
          button.innerHTML = 'Отправить';
          button.style.backgroundColor = '#262626';
          button.removeAttribute('disabled');
        }, 1000);
    },

    chatcheck: async (event, user) => {
        const chat = document.getElementById('chat-messages');
        const listItems = chat.getElementsByTagName('li');
        var last_date = '';

        for (let i = 1; i <= listItems.length - 1; i++)
        {
            let current_date = new Date( listItems[i].getAttribute( 'write-date' ) );
            let back_date = new Date( listItems[i - 1].getAttribute( 'write-date' ) );

            if(current_date > back_date)
                last_date = listItems[i].getAttribute('write-date');
        }

        console.log( listItems.length );
        console.log( last_date );

        const data = new FormData;
        data.append('nmobtn-chat-event', event);
        data.append('nmobtn-chat-user', user);
        data.append('nmobtn-chat-last', last_date);

        const request = await fetch(
            '/wp-json/nmobtn/v1/chatcheck',
            {
                method: 'POST',
                credentials: 'include',
                body: data
            }
        );

        if (request.ok)
        {
            let answer = await request.json();

            if (answer.code == 0)
            {
                  console.log('chat check: success.');

                  if( answer.messages.length > 0 && answer.messages != undefined )
                  {
                      console.log(answer.messages);

                      answer.messages.forEach( (message) => {
                          chat.innerHTML += nmobtnClient.addmessage(message.id , message.user_name, message.write_date, message.message);
                      });
                      chat.scrollTop = chat.scrollHeight;
                  }
            }
            else
                  console.log('chat check: error.');
        }
        else
              console.log('chat check: error.');
    },

    addmessage: (id ,name, date, message) => {
        return '<li id="' + id + '" write-date="' + date + '" class="message"><span id="name' + id + '" class="name-message name-user" onClick="nmobtnClient.answering(\'name' + id + '\')">' + name + '</span><br><span class="message-text">' + message + '</span></li>';
    },

    answering: (name_id) => {
        const name_text = document.getElementById(name_id).innerHTML;
        const message = document.getElementById('area-msg-chat');
        message.value += '@' + name_text + ', ';
    }
};
