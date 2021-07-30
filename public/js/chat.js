const chat = {
    editor:'',
    socket:'',
    init:function() {
        app.socket = new WebSocket('ws://localhost:8080');
        app.socket.onopen = function(e) {
           // 
        };
        if(document.querySelector('.editor')) {
        let currentChatId =  document.querySelector('#chatId').value;
        app.socket.onmessage = function(event) {
            const list = document.querySelector('.chat-messages');
            let data = JSON.parse(event.data);
            if(data.chat_id == currentChatId) {
                let templateElement = document.querySelector('#message-template');
                let messageFragment = templateElement
                .content
                .cloneNode(true)
                .querySelector('.chat-message');
                messageFragment.querySelector('.chat-message__author').textContent = data.author;
                let date = new Date(data.created_at);
                let month = date.getMonth()+1;
                let displayMonth = month >9 ? month : '0'+month;
                let displayDay = date.getDate() >9 ? date.getDate() : '0'+date.getDate();
                let displayHours = date.getHours() >9 ? date.getHours() : '0'+date.getHours();
                let displayMinutes = date.getMinutes() >9 ? date.getMinutes() : '0'+date.getMinutes();
                messageFragment.querySelector('.chat-message__time').textContent =  displayDay+'/'+displayMonth+' ' + displayHours+':'+ displayMinutes;
                messageFragment.querySelector('.chat-message__body').classList.add (data.author_id == document.querySelector('#userId').value ? 'isAuthor' : 'isNotAuthor');
                messageFragment.querySelector('.chat-message').classList.add (data.author_id == document.querySelector('#userId').value ? 'isAuthor' : 'isNotAuthor');
                messageFragment.querySelector('.chat-message__body').innerHTML = data.message;
                list.prepend(messageFragment);

                // when a user connects onton a chat all the unread messages that are not his messages for this chat are noted as read
                let config = {
                    method: 'GET',
                    mode: "same-origin",
                    credentials: "same-origin",
                    cache: 'no-cache',
                }
                let request = fetch(app.baseUrl +'chat/'+ currentChatId +'/markAsRead/', config);
            }
        }
        
        chat.handleMessageFormat();
        document.querySelector('#newPostForm').addEventListener('submit', chat.generateHTML);
        
       }
      
    },
    handleMessageFormat:function() {
        var toolbarOptions = [
            ['bold', 'italic'],
            [{ 'size': ['small', false, 'large', 'huge'] }],
            [{ 'color': [] }, { 'background': [] }]
          ]
        app.editor = new Quill('.editor', {
            placeholder: 'Entrez votre message',
            modules: {
                toolbar: toolbarOptions,
                history: {
                    delay: 1000,
                    maxStack: 500,
                    userOnly: true
                  }
              },
            theme:'snow'
        });
    },
    
    generateHTML:function(e) {
        e.preventDefault();
        let chatId= document.querySelector('#chatId').value;
    
        document.querySelector('#newPostMessage').value = app.editor.root.innerHTML;
        app.editor.root.innerHTML = "";
        
        let config = {
            method: 'POST',
            mode: "same-origin",
            credentials: "same-origin",
            cache: 'no-cache',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body : JSON.stringify({message:document.querySelector('#newPostMessage').value})
        }
       
        let request = fetch(app.baseUrl+'message/add/chat/'+chatId, config);
        request.then(response=> { 
            if (!response.ok) {
                throw Error(response.statusText);
            }
            return response.json();
        })
        .then(response=> {
            console.log(response);
            let data = {
                message:document.querySelector('#newPostMessage').value,
                chat_id:document.querySelector('#chatId').value,
                author:document.querySelector('#authorName').value,
                author_id:document.querySelector('#authorId').value,
                created_at:Date.now()
            }
            app.socket.send(JSON.stringify(data));
        })
        .catch(error=> {
            const list = document.querySelector('.chat-messages');
            let errorMsg = document.createElement('div');
            errorMsg.textContent = "Il y a eu une erreur, veuillez réessayer";
            list.appendChild(errorMsg);

        })
       
    }
    
}
