import sweetalert2 from "sweetalert2";

export function alertErrorWithTitle(title, message) {
    sweetalert2.fire({
        icon: 'error',
        title: title,
        text: message,
        confirmButtonColor: '#1B2F33',
        background: '#FEE1C7',
    })
}

export function alertError(message) {
    sweetalert2.fire({
        icon: 'error',
        text: message,
        confirmButtonColor: '#1B2F33',
        background: '#FEE1C7',
    })
}