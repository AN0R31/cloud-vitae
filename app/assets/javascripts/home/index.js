import {alertError} from "../features/customAlert.js";

//INDEX SIGN IN BUTTON
let start = document.getElementById('start')
start.addEventListener("click", event => {
    window.location.href = "/register";
})