const profile = {
    avatarIndex:0,
    init:function() {
        // If we are on the user-profile page 
        if(document.querySelector('#edit-profile')) {
           
            profile.avatarIndex = 0;
            //On click on edit button, we handle change of mode
            document.querySelector('.editBtn').addEventListener('click', profile.handleUserEdit);
            // We act according to edit mode
            profile.checkEditMode();
            document.querySelector('.form__image--reset').addEventListener('click', profile.handleImageReset);
            document.querySelector('#user_picture').addEventListener('change', profile.handlePhotoUpload);
        }     
    },
    checkEditMode:function() {
        let profileElement = document.querySelector('.form-container--profile');
        let src = document.querySelector('.form__image--picture').src;
        let srcArr = src.split('/');
        let fileName = srcArr[srcArr.length-1];

        if(profileElement.classList.contains('editMode')) {
           // If the picture selected is an avatar, we display arrows to change avatar
            if(document.querySelector('.avatar-input').value) {   

                document.querySelectorAll('.avatarSelect').forEach(arrow=> {
                    arrow.style.display='block';
                    arrow.addEventListener('click', profile.handleNextAvatar);
                }) } else {
                    document.querySelector('.fa-close').style.display="block";
                }
        } else {
                document.querySelectorAll('.avatarSelect').forEach(arrow=> {
                    arrow.style.display='none';
                }) 
                 document.querySelector('.fa-close').style.display="none";
        }
    }, 
    // When clicking on the cross to delete picture loaded, we display the avatars and the arrows
    handleImageReset:function() {
        document.getElementById("user_picture").value = "";
        let src = document.querySelector('.form__image--picture').src;
        let srcArr= src.split('/');
        let newSrc = srcArr[srcArr.length-1];
        document.querySelector('.form__image--picture').src = src.replace(newSrc, 'avatar-male-1.png');
        document.querySelector('.avatar-input').value='avatar-male-1.png';
        profile.checkEditMode();
    },
     // Method for previewing user loaded picture before submitting changes
     handlePhotoUpload:function(event) {
        document.querySelector('.avatar-input').value='';
        profile.checkEditMode();
        const objectURL = URL.createObjectURL(event.target.files[0]);
        document.querySelector('.form__image img').src= objectURL;
    },
    // Method for browsing through avatar choices provided by default
    handleNextAvatar:function(e) {
        let avatars =[
            'avatar-male-1.png',
            'avatar-female-1.png',
            'avatar-male-2.png',
            'avatar-female-2.png'
        ];
        if(e.currentTarget.id == 'prevAvatar') {
            profile.avatarIndex > 0 ? profile.avatarIndex-- : profile.avatarIndex= avatars.length-1;
        } else {
            profile.avatarIndex < avatars.length-1 ? profile.avatarIndex++ : profile.avatarIndex= 0;
        }
        let src = document.querySelector('.form__image--picture').src;
        let srcArr= src.split('/');
        let newSrc = srcArr[srcArr.length-1];
        document.querySelector('.form__image--picture').src = src.replace(newSrc, avatars[profile.avatarIndex]);
        document.querySelector('.avatar-input').value=avatars[profile.avatarIndex];     
    },
    // Method for toggling modes when user clicks on edit button
    handleUserEdit:function(e) {
        let profileElement = document.querySelector('.form-container--profile');
        profileElement.classList.toggle('editMode');
        profile.checkEditMode();
        e.preventDefault();
    
    }
}