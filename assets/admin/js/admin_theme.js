/* eslint-disable no-use-before-define */
/* eslint-disable no-plusplus */
/* eslint-disable func-names */
const $ = require("jquery");
const body = $('body');
export const adminTheme = () => {
    const toggle = $('.js_admin-theme-toggle');
    if(!toggle.length) return;

    toggle.on('click', function() {
        const selected = $(this);
        toggle.removeClass('active');
        selected.addClass('active');

        let lightMode;
        if(selected.data('theme') === "light") {
            body.addClass('light-mode');
            lightMode = 1;
        } else {
            body.removeClass('light-mode');
            lightMode = 0;
        }
        $.ajax({
            url: "/admin/theme-change",
            type: "post",
            dataType: "json",
            data: {
                theme: lightMode
            }
        }).done(function(json) {

        });
    });
}