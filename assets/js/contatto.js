document.addEventListener('DOMContentLoaded', function() {
    let captcha = document.querySelector("a[title ~= 'BotDetect']");
    // console.log(captcha.innerHTML);
    captcha.removeAttribute("style");
    captcha.removeAttribute("href");
    captcha.style.visibility = "hidden";
})
