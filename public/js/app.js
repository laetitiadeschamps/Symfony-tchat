const app = {
    baseUrl:"",
    init:function() {
        // Getting base url for the project
        let getUrl = window.location;
        app.baseUrl = getUrl .protocol + "//" + getUrl.host + "/";
        if(document.querySelector('.dropdown')) {
            document.querySelectorAll('.dropdown').forEach(dropdown=> {
            dropdown.addEventListener('click', app.handleDropdownClick);
            })
        }
        //Handle hamburger click
        document.querySelector('.hamburger-icon').addEventListener('click', app.handleHamburgerClick);
        // init other modules
        login.init();
        profile.init();
        contact.init();
        chat.init();
    },
    handleDropdownClick:function(e) {
        e.currentTarget.classList.toggle('open');
    },
    handleHamburgerClick:function(e) {
        document.querySelector('#hamburger-nav').classList.toggle('open');
    }    
}

document.addEventListener('DOMContentLoaded', app.init);