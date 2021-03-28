window.addEventListener('DOMContentLoaded', function (event) {
    let inputToggles = document.querySelectorAll('.et_ct_switch');
    if (inputToggles.length > 0) {

        inputToggles.forEach(function (toggle) {

            toggle.addEventListener('click', function () {
                let toggleCheckBox = toggle.querySelector('input[type="checkbox"]');

                if (toggleCheckBox.checked) {
                    toggleCheckBox.value = 'checked'
                } else {
                    toggleCheckBox.value = 'unchecked'
                }

            }, false);

        });

    }
});