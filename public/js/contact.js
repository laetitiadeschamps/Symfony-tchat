const contact = {
    list:[],
    init:function() {
        document.querySelector('#searchText') ? contact.getAllUsers():'';
        document.querySelector('#searchText') ? document.querySelector('#searchText').addEventListener('keyup', contact.handleContactSearch):'';
        document.querySelector('.showModal') ? document.querySelectorAll('.showModal').forEach(element=> {
            element.addEventListener('click', contact.handleModalDisplay);
        }) : '';   
    },
    handleModalDisplay :function(e){
        let modal = e.currentTarget.parentNode.querySelector('.modal-confirm');
        modal.classList.add('visible');
    },
    handleContactSearch:function(e) {
        document.querySelector('.list-search__users-list').textContent='';
        let value = e.currentTarget.value;
        contact.fetchContacts(value);
    },
    fetchContacts:function(value) {
        
        console.log(contact.list);
        let contacts = contact.list.filter(contact => {
            return contact.login.indexOf(value) !== -1 || contact.firstname.indexOf(value) !== -1 || contact.lastname.indexOf(value) !== -1
        })
        document.querySelector('.list-search__searchbar').style.borderRadius="2em 2em 0 0";
        if(!contacts.length) {
            document.querySelector('.list-search__searchbar').style.borderRadius="2em";
            return;
        }
        contacts.forEach(user=> {
        let templateElement = document.querySelector('#user-template');
        let userFragment = templateElement
        .content
        .cloneNode(true)
        .querySelector('.user-card');
        userFragment.id = user.id;
        userFragment.querySelector('#user-card__name').textContent=user.firstname + ' ' + user.lastname;
        let picture = user.picture?? 'avatar-male-1.png';
        userFragment.querySelector('.user-card__picture img').src='../images/'+ picture;
        userFragment.querySelector('a.profile').href="/contact/profile/" + user.id;
        document.querySelector('.list-search__users-list').appendChild(userFragment);
        })
      
    },

    getAllUsers:function() {
        let config = {
            method: 'GET',
            mode: 'cors',
            cache: 'no-cache'
        }
        let request = fetch(app.baseUrl+'contact/search', config);
        request.then(response=> {
            return response.json();
        })
        .then (jsonResponse => {
           contact.list = jsonResponse;
        })
        .catch(error=> {
            
            console.log('Il y a eu une erreur!')
        })
    }
    
    
}