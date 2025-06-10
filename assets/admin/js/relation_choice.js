const $ = require("jquery");

export const relationChoice = () => {
    const elements = $('.js_relation-choice');
    if(!elements.length) return;

    elements.each(function() {
        addEvent($(this));
    });
}
const addEvent = (element) => {
    const parentElement = $(element.data('relation-choice-parent'));
    if(!parentElement.length) return;

    const apiUrl = element.data('relation-choice-api');
    if(!apiUrl) {
        throw error('relation choice api url required. set data-relation-choice-api');
    }

    const emptyItem = element.data('relation-choice-empty');
    parentElement.on('change', function() {
        updateChoice(element, parentElement, apiUrl, emptyItem);
    });
}
const updateChoice = (element, parentElement, apiUrl, empty) => {
    let choiceValue = parentElement.val();
    if(!choiceValue) {
        let emptyText = (empty)? empty : "--";
        element.html(`<option value disabled>${emptyText}</option>`);
        return;
    }
    element.html('<option value disabled></option>');
    $.ajax({
        url: apiUrl+"/"+choiceValue,
        type: "get",
        dataType: "json"
    }).done(function(json) {
        const choiceList = [];
        if(empty) {
            choiceList.push(`<option value>${empty}</option>`);
        }
        $(json).each(function () {
            choiceList.push(`<option value='${this.id}'>${this.name}</option>`);
        });
        element.html(choiceList.join('')).trigger('change').trigger('relation-choice-update');
    });
}