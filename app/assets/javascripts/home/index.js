import {alertError} from "../features/customAlert.js";

//INDEX SIGN IN BUTTON
let start = document.getElementById('start')
start.addEventListener("click", event => {
    Swal.fire({
        title: 'Sign up options:',
        color: '#8000FF',
        confirmButtonText: 'LinkedIn',
        confirmButtonColor: '#8000FF',
        cancelButtonText: 'Create an account',
        cancelButtonColor: '#8000FF',
        focusConfirm: false,
        showCancelButton: true,
    }).then(response => {
        if (response.isConfirmed) {
            window.location.href = "/register/linkedin";
        } else {
            window.location.href = "/register";
        }
    })
})