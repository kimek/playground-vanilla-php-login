if(typeof loginForm !== 'undefined') {
    loginForm.addEventListener('submit', ajaxRequest);
    registerForm.addEventListener('submit', ajaxRequest);
}

if(typeof logoutForm !== 'undefined') {
    logoutForm.addEventListener('submit', ajaxRequest);
}
function ajaxRequest(event) {
    event.preventDefault();
    document.body.classList.add('loading');
    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'api.php', true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            document.body.classList.remove('loading');
            let response = JSON.parse(xhr.responseText).response;
            if (xhr.status === 200) {
                if(response) {
                    alert(response);
                } else {
                    document.location = location.protocol+'//'+location.host+location.pathname;
                }
            } else {
                alert(response);
            }
        }
    };
    xhr.send(new FormData(event.target));
}