/* eslint-disable no-alert */
/* eslint-disable no-restricted-globals */
/* eslint-disable func-names */
/* eslint-disable eqeqeq */
const $ = require("jquery");

const form = $("#input-form");
const submit = $("#input-form-submit");
const preview = $('#input-form-preview');
const state = {preview: false};
const deleteBtn = $('#input-form-delete');

export const inputFormSubmit = () => {
  if (form.length === 0 || submit.length === 0) {
    return false;
  }

  form.on("submit", () => {
    return !(!state.preview && !confirm("保存しますか？"));
  });
  if (preview.length !== 0) {
    const previewAction = preview.data('endpoint');
    preview.on('click', (e) => {
      e.preventDefault();
      state.preview = true;
      form.attr('action', previewAction);
      form.attr('target', 'preview');
      $(':required').each(function () {
        if (!$(this).val()) {
          $(this).trigger('focus');
          alert('入力がありません');
          throw new Error('Input required');
        }
      });
      form.get()[0].submit();
      setTimeout(() => {
        form.removeAttr('action');
        form.removeAttr('target');
        state.preview = false;
      }, 200);
    });
  }
  if(deleteBtn.length !== 0) {
    const deleteAction = deleteBtn.data('endpoint');
    const message = deleteBtn.data('message');
    const token = deleteBtn.data('token');
    const body = $('body');
    const deleteForm = $('<form/>');
    deleteForm.attr('action', deleteAction);
    deleteForm.attr('method', "POST");
    const inputMethod = $('<input/>');
    inputMethod.attr('type', "hidden");
    inputMethod.val('DELETE');
    inputMethod.attr('name', '_method')
    const inputToken = $('<input/>');
    inputToken.attr('type', "hidden");
    inputToken.val(token);
    inputToken.attr('name', '_token');
    deleteForm.append(inputMethod);
    deleteForm.append(inputToken);
    body.append(deleteForm);

    deleteBtn.on('click', (e) => {
      e.preventDefault();
      if(message && !confirm(message)) return false;
      deleteForm.trigger('submit');
    });
  }
  return true;
};
