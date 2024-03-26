// custom project
const goToGmail = document.getElementById('goToGmail');


if(!!goToGmail) {
    
    goToGmail.onclick = function() {
        document.getElementById('userUnRead').innerText = 0;
    }
}