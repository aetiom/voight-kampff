var captchaImages = document.querySelectorAll('.sc-img');

var _loop = function _loop(i) {
    captchaImages[i].addEventListener('click', function () {
        captchaImages[i].classList.toggle('selected');
    });
};

for (var i = 0; i < captchaImages.length; i++) {
    _loop(i);
}