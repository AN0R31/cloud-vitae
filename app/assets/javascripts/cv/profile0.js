import axios from "axios";

let qrcode = new QRCode("qr_code", {
    text: "http://localhost:8080/" + userId,
    width: 64,
    height: 64,
    colorDark : "#000000",
    colorLight : "#ffffff",
    correctLevel : QRCode.CorrectLevel.H
});

function getQr() {
    let opt = {
        // margin: [0, 0, 0, 0],
        filename: 'my_cloud_vitae_qr.pdf',
        // image: {type: 'jpeg', quality: 1},
        // backgroundColor: '#000000',
        // html2canvas: {scale: 3, scrollY: 1, backgroundColor: '#000000', windowWidth: 1, windowHeight: 1},
        // jsPDF: {unit: 'in', format: 'B6', orientation: 'landscape', precision: '12', putOnlyUsedFonts: true, width: 10, height: 10, fillColor: '#000000'}
    };
    // html2pdf().set(opt).from(document.getElementById('qr_code')).save()
    var exporter = new html2pdf(document.getElementById('qr_code'), {
        margin:       0,
        filename:     'myfile.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2, logging: true, dpi: 192, letterRendering: true, width: 70, height: 70},
        jsPDF:        { unit: 'px', format: [70, 70], orientation: 'portrait' }
    });
    exporter.getPdf(true).then((pdf) => {
        console.log('pdf file downloaded');
    });
}

document.getElementById('get-qr').addEventListener("click", ev => {
    getQr()
})

function myFunction(x) {
    // if (x <= 550) { // If media query matches
    //     if (x <= 400) {
    //         document.getElementById('navbar').style.height = '0';
    //         document.getElementById('header').style.height = '15%';
    //         document.getElementById('content').style.height = '80%';
    //         document.getElementById('side-content').style.height = '80%';
    //     } else {
    //         document.getElementById('navbar').style.height = '0';
    //         document.getElementById('header').style.height = '20%';
    //         document.getElementById('content').style.height = '75%';
    //         document.getElementById('side-content').style.height = '75%';
    //     }
    // } else {
    //     document.getElementById('navbar').style.height = '5%';
    //     document.getElementById('header').style.height = '25%';
    //     document.getElementById('content').style.height = '70%';
    //     document.getElementById('side-content').style.height = '70%';
    // }
}

for (let i = 0; i < 4; i++) {
    document.getElementById('thumb' + i).addEventListener("click", event => {
        const dataToSend = new FormData()
        dataToSend.set('theme', String(i))
        axios.post('/changeTheme', dataToSend)
            .then(function (response) {
                if (response.data.status === true) {
                    window.location.reload()
                }
            })
    })
}

if (isOwner) {
    document.getElementById('changeThemeButton').addEventListener("click", event => {
        document.getElementById('popup').style.display = 'flex';
    })

    document.getElementById('cancelChangeThemeButton').addEventListener("click", event => {
        document.getElementById('popup').style.display = 'none';
    })

    document.getElementById('change-profile-picture-button').addEventListener("click", event => {
        document.getElementById('image').click()
    })
}

document.getElementById('copy').addEventListener("click", event => {
    let copyText = 'cloud-vitae.com/profile/' + event.target.getAttribute('data-id');
    navigator.clipboard.writeText(copyText).then()
})

let x = screen.width
myFunction(x) // Call listener function at run time

window.onresize = function () {
    x = screen.width
    myFunction(x)
}

document.getElementById('image').onchange = function (e) {
    const dataToSend = new FormData()
    dataToSend.set('image', document.getElementById('image').files[0])
    axios.post('/changeProfilePicture', dataToSend)
        .then(function (response) {
            if (response.data.status === true) {
                window.location.reload()
            }
        })
}

document.getElementById('download').addEventListener("click", event => {
    generatePDF()
    // tetsPDF()
})

const delay = ms => new Promise(res => setTimeout(res, ms));
const generatePDF = async () => {
    // let contentHeight = document.getElementById('content').height
    // document.getElementById('content').height = 'auto'
    // document.getElementById('content').overflowY = 'visible'
    document.body.overflow = 'visible'

    let body = document.body,
        html = document.documentElement;

    let height = Math.max( body.scrollHeight, body.offsetHeight,
        html.clientHeight, html.scrollHeight, html.offsetHeight );
    let noPages = height/3508
    noPages = Math.floor(noPages)

    height = (noPages * 3508)+(3508-(height-(noPages * 3508)))

    let toHide = document.getElementsByClassName('owner')
    for (let i = 0; i < toHide.length; i++) {
        toHide[i].style.display = 'none'
    }
    // Choose the element id which you want to export.
    let element = document.getElementsByTagName('html')[0];
    let opt = {
        margin: [0, 0, 0, 0],
        filename: 'my_cloud_vitae.pdf',
        image: {type: 'jpeg', quality: 1},
        html2canvas: {scale: 2.5, scrollY: 1, windowHeight: height},
        jsPDF: {unit: 'in', format: 'a3', orientation: 'landscape', precision: '12', putOnlyUsedFonts: true}
    };
    // choose the element and pass it to html2pdf() function and call the save() on it to save as pdf.
    html2pdf().set(opt).from(element).save()
    // document.getElementById('content').height = contentHeight
    // document.getElementById('content').overflowY = 'scroll'
    document.body.overflow = 'hidden'

    await delay(1000);
    for (let i = 0; i < toHide.length; i++) {
        toHide[i].style.display = 'flex'
    }
}

function tetsPDF() {

}


// function saveAsPdf() {
//
//     window.jsPDF = window.jspdf.jsPDF;
//     window.html2canvas = html2canvas;
//     let doc = new jsPDF(
//         'landscape', 'mm', 'a4'
//     );
//     doc.setFillColor(0, 0, 0);
//     doc.rect(0, 0, 297, 420, 'F');
//     let img = new Image()
//     img.src = 'assets/img/profile.png'
//     doc.html(document.querySelector("html"), {
//         callback: function (pdf) {
//             pdf.save("cv-a4.pdf");
//
//         },
//         x: 0,
//         y: 10,
//         autoPaging: 'text',
//         width: 297, //target width in the PDF document
//         windowWidth: screen.width //window width in CSS pixels
//     });
// }