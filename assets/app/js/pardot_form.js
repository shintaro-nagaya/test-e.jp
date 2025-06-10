/* eslint-disable func-names */
const $ = require("jquery");

let submitted = false;
const submit = (form) => {
    if(submitted) return;
    form.trigger("submit");
    submitted = true;
}
export const pardotForm = () => {
    const form = $('.js_pardot-form');
    if(!form.length) return;
    submit(form);
    const sendButton = $('.js_pardot-form__message--send');
    if(!sendButton) return;
    sendButton.on('click', function() {
        submit(form);
    });
}