const login = {
    init:function() {
        document.querySelectorAll('.passwordDisplay').forEach(icon=>  {
            icon.addEventListener('click', login.handlePasswordDisplay);
        })
        document.querySelector('.form-container--login') ? document.querySelector('.form-container--login form').addEventListener('submit', login.handleLoginFormSubmit):'';
        document.querySelector('.form-container--create') ? document.querySelector('.form-container--create form').addEventListener('submit', login.handleCreateFormSubmit):'';
        document.querySelectorAll('.form-container--create input').forEach(input=> {
            if(input.id=='birthdate') {
                input.addEventListener('change', login.handleFormValidation);
            } else {
                input.addEventListener('keyup', login.handleFormValidation);
            }          
        })
        document.querySelectorAll('form.profile input').forEach(input=> {
            input.addEventListener('keyup', login.handleFormValidation);
        })      
    },
    // Checking if date is valid and before present date
    checkDate:function(dateInput) {
        let date = new Date(dateInput);
        if(date && date < Date.now()) {
            return true;
        } else {
            return false;
        }
    },
    // Checking input validity and displaying errors accordingly either on create form submit or live when a user types on a given input field
    checkInput:function(input) {
        input.parentNode.querySelector('small').textContent = '';
        if(input.id=='password' ||input.id=='passwordRepeat') {
            let pwd =  document.querySelector('#password');
            let repeat = document.querySelector('#passwordRepeat');
            if(!/[0-9]/.test(pwd.value)) {
                pwd.parentNode.querySelector('small').textContent = 'Le mot de passe doit contenir au moins un chiffre';
            }
            if(!/[a-z]/.test(pwd.value)) {
                pwd.parentNode.querySelector('small').textContent = 'Le mot de passe doit contenir au moins une minuscule';
            }
            if(!/[A-Z]/.test(pwd.value)) {
                pwd.parentNode.querySelector('small').textContent = 'Le mot de passe doit contenir au moins une majuscule';
            }
            if(pwd.value.length < 8) {  
                pwd.parentNode.querySelector('small').textContent = 'Le mot de passe doit contenir au moins 7 caractères';
            }
            if(pwd.value == repeat.value) { 
                repeat.parentNode.querySelector('small').textContent = '';
                if(input.value) {
                return true;
                }
            } else {
                repeat.parentNode.querySelector('small').textContent = 'Les mots de passe ne correspondent pas.';
            }
            return false;
        }
        if(input.id == 'firstname' || input.id=='lastname' || input.id=='login') {
            if(input.value.length > 2) {
                return true;
            } else {  
                input.parentNode.querySelector('small').textContent = "Vous devez saisir un texte de plus de 2 lettres";
            }
            return false;
        }
        if(input.id=='mail') {
            if(!/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/g.test(input.value)) {
                input.parentNode.querySelector('small').textContent = 'Le mail n\'est pas valide';
            }
            return true;
        }
        if(input.id=='birthdate') {
            if(!login.checkDate(input.value)) {
                input.parentNode.querySelector('small').textContent = 'La date de naissance n\'est pas valide';
                return false;
            }
            return true;
        }
    },
    // On create form submit, checking if create form has been properly filled out and displaying errors accordingly
    handleCreateFormSubmit:function(e) {
        let isValid = true;
        // For each input, we check its validity and if one of them is false, we stoop submitting and display appropriate errors
        document.querySelectorAll('.form-container--create input').forEach(input=> {
            if(!login.checkInput(input)) {
                isValid=false;
            }
        })
        if(!isValid) {
            e.preventDefault();
            document.querySelector('.form__error').textContent = "Veuillez vérifier vos champs !"
        }
    },
    // Method fired to check validity of a given input while the user is typing on this input field
    handleFormValidation:function(e) {
       let input = e.currentTarget;
       login.checkInput(input);
    },
    // On login form submit, checking if all fields are filled out before submitting
    handleLoginFormSubmit:function(e) {
        let loginValue = document.querySelector('#login').value.trim();
        let passwordValue = document.querySelector('#password').value.trim();
        if(!loginValue || !passwordValue) {
            e.preventDefault();
            document.querySelector('.form__error').textContent = "Les champs ne peuvent pas être vides !"
        }      
    }, 
    // Toggle hide/show password according to user action
    handlePasswordDisplay:function(e)  {
        let fieldset = e.currentTarget.closest('fieldset');
         if(e.currentTarget.classList.contains('fa-eye-slash')) {
             fieldset.querySelector('input').type='password';
             fieldset.querySelector('.fa-eye-slash').classList.add('hidden');
             fieldset.querySelector('.fa-eye').classList.remove('hidden');
         } else {
             fieldset.querySelector('input').type='text';
             fieldset.querySelector('.fa-eye').classList.add('hidden');
             fieldset.querySelector('.fa-eye-slash').classList.remove('hidden');
         }
     }
}