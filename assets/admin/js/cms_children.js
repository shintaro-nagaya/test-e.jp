/* eslint-disable no-use-before-define */
/* eslint-disable no-plusplus */
/* eslint-disable func-names */
const $ = require("jquery");


export const cmsChildren = () => {
  const holders = $('.js_cms-child-forms');
  if (!holders.length) return;
  holders.each(function () {
    init($(this));
  });
}
const init = function (holder) {
  const add_btn = holder.next('.js_cms-child-forms-control').find('.js_cms-child-forms-add');
  if (!add_btn.length) return;
  add_btn.on('click', function () {
    addChild(holder);
  });
  sortEventTrigger(holder);
  deleteEvent(holder);
  youtubeEvent(holder);
}
const addChild = function (holder) {
  const endpoint = holder.data('add-endpoint');
  if (!endpoint) return;
  const counter = $('.js_cms-child-form', holder).length;

  $.ajax({
    url: endpoint,
    type: "get",
    dataType: "html",
    data: {
      num: counter
    }
  }).done((html) => {
    holder.append(html);
    sortEventTrigger(holder);
    deleteEvent(holder);
    youtubeEvent(holder);
    $(window).trigger("cms-child:add-child");
    $(window).trigger("js_fileUploader");
  })
}
const sortEventTrigger = function(holder) {
  $('.js_cms-child-form .js_form-child-sort', holder).off('change').on('change', function() {
    childSort(holder);
  });
}
const childSort = function(holder) {
  $('.js_cms-child-form', holder).each(function() {
    let sort = $('.js_form-child-sort', this).val();
    if(!sort) sort = 1;
    $(this).css('order', sort);
  })
}
const deleteEvent = function(holder) {
  $('.js_cms-child-form .js_cms-child-delete', holder).off('change').on('change', function() {
    const $this = $(this);
    const wrapper = $this.parents('.js_cms-child-form');
    wrapper.find('input,select,textarea').prop('readonly', $this.prop('checked'));
    $this.prop('readonly', false);
  });
}
const youtubeEvent = function(holder) {
  $('.js_cms-child-form .js_cms-child-youtube', holder).each(function() {
    const block = $(this);
    $('input', block).off('change').on('change', function() {
      const preview = $('.js_cms-child-youtube__preview', block);
      if(!preview.length) return;
      preview.empty();
      const id = $(this).val();
      if(!id) {
        return;
      }
      const img = $('<img/>');
      img.attr('src', `https://img.youtube.com/vi/${id}/default.jpg`);
      img.attr('alt', '');
      const link = $('<a/>');
      link.attr('href', `https://www.youtube.com/watch?v=${id}`);
      link.attr('rel', 'noopener noreferer');
      link.attr('target', '_blank');
      link.append(img);
      preview.append(link);
    })
  })
}