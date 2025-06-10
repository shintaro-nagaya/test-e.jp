/* eslint-disable camelcase */
/* eslint-disable radix */
/* eslint-disable no-use-before-define */
/* eslint-disable eqeqeq */
/* eslint-disable func-names */
const $ = require("jquery");

const createUploadInput = function (target) {
  const holder = $(target);
  if (holder.children().length !== 0) return;
  // ファイル名を格納するForm
  const fileElement = $(holder.data("file-element"));
  if (fileElement.length === 0) {
    throw "File Uploader file input element not found. ".holder.attr(
      "data-file-element"
    );
  }
  holder.addClass("c_fileUploader");

  // width
  let widthElement = $(holder.data("width-element"));
  if (widthElement.length === 0) widthElement = null;
  // height
  let heightElement = $(holder.data("height-element"));
  if (heightElement.length === 0) heightElement = null;
  // filesize
  let filesizeElement = $(holder.data("filesize-element"));
  if (filesizeElement.length === 0) filesizeElement = null;
  // origin
  let originElement = $(holder.data("origin-element"));
  if (originElement.length === 0) originElement = null;
  // mime
  let mimeElement = $(holder.data("mime-element"));
  if (mimeElement.length === 0) mimeElement = null;

  // メインのアップローダを生成
  const uploader = $("<input/>");
  uploader.attr("type", "file");
  uploader.addClass("js_file-uploader");
  uploader.addClass("c_fileUploader--input");
  // Preview holder
  const preview = $("<div/>");
  // preview.addClass("js_file-uploader-preview");
  preview.addClass("c_fileUploader--preview");
  // progress
  const progress = $("<div/>");
  // progress.addClass("js_file-uploader-progress");
  progress.addClass("c_fileUploader--progress");
  // endpoint
  let endpointPrefix = holder.data('endpoint-prefix');
  if(!endpointPrefix) endpointPrefix = "admin";

  holder.append(uploader);
  holder.append(preview);
  holder.append(progress);

  // エンティティ
  const entity = holder.data("entity");

  // すでに登録がある場合、情報取得する
  if (fileElement.val()) {
    $.ajax({
      url: `/${endpointPrefix}/file_upload/value/${  entity  }/${  fileElement.val()}`,
      type: "get",
      dataType: "json",
    }).done((json) => {
      if (json.status === 200) {
        preview.html(create_preview(json));
      }
    });
  }

  // on changeイベントでアップロード実行
  uploader.on("change", function () {
    const fd = new FormData();
      const input = $(this);
    fd.append("file", input.prop("files")[0]);
    fd.append("entity", entity);

    preview.empty();
    progress.width(0);

    // アップロード開始
    $.ajax({
      url: `/${endpointPrefix}/file_upload/`,
      type: "post",
      data: fd,
      dataType: "json",
      contentType: false,
      processData: false,
      async: true,
      xhr () {
        const XHR = $.ajaxSettings.xhr();
        if (XHR.upload) {
          XHR.upload.addEventListener(
            "progress",
            (e) => {
              // e.loaded / e.total * 100 : progress %
              progress.css({
                width: `${parseInt((e.loaded / e.total) * 100)  }%`,
              });
            },
            false
          );
        }
        return XHR;
      },
    }).done((json) => {
      if (json.status === 200) {
        preview.html(create_preview(json));

        fileElement.val(json.remote_filename_extension);
        // width
        if (widthElement) {
          widthElement.val(json.width ? json.width : "");
        }
        // height
        if (heightElement) {
          heightElement.val(json.height ? json.height : "");
        }
        // filesize
        if (filesizeElement) {
          filesizeElement.val(json.size ? json.size : "");
        }
        // origin
        if (originElement) {
          originElement.val(json.origin_filename ? json.origin_filename_extension : "");
        }
        // mime
        if (mimeElement) {
          mimeElement.val(json.mime ? json.mime : "");
        }
      } else {
        const message = $("<span/>");
        message.addClass("error");
        message.text(json.message);
        preview.html(message);
        uploader.val('');
      }
      setTimeout(() => {
        progress.width(0);
      }, 3000);

    });
  });

  const create_preview = function (json) {
    const preview_box = $("<div/>");
    const preview_link = $("<a/>");
    preview_link.attr("target", "_blank");
    if (json.is_image) {
      // 画像
      const preview_thumb = $("<img/>");
      preview_thumb.attr("src", json.link);
      preview_thumb.addClass("c_fileUploader--preview__image");
      preview_link.html(preview_thumb);
    } else {
      // Not画像
      preview_link.addClass("btn");
      preview_link.addClass("btn-info");
      preview_link.addClass("btn-sm");
      preview_link.text("Preview");
    }
    preview_link.attr("href", json.link);
    preview_box.append(preview_link);
    const delete_box = $("<label/>");
    const delete_btn = $("<input/>");
    const delete_label = $("<span/>");
    delete_box.addClass("form-check");
    delete_btn.attr("type", "checkbox");
    delete_label.text("削除");
    delete_label.addClass('form-check-label');
    delete_box.append(delete_btn);
    delete_box.append(delete_label);
    preview_box.append(delete_box);

    delete_btn.on("change", () => {
      if (delete_btn.prop("checked")) {
        // delete.
        fileElement.attr("disabled", "disabled");
        disableElement(widthElement, true);
        disableElement(heightElement, true);
        disableElement(filesizeElement, true);
        disableElement(originElement, true);
        disableElement(mimeElement, true);
      } else {
        // no delete
        fileElement.removeAttr("disabled");
        disableElement(widthElement, false);
        disableElement(widthElement, false);
        disableElement(heightElement, false);
        disableElement(filesizeElement, false);
        disableElement(originElement, false);
        disableElement(mimeElement, false);
      }
    });

    return preview_box;
  };
  const disableElement = function (elem, dis) {
    if (elem == null) return;
    if (dis === true) {
      elem.attr("disabled", "disabled");
    } else {
      elem.removeAttr("disabled");
    }
  };
};
export const fileUploader = () => {
  $(".js_fileUploader").each(function () {
    createUploadInput(this);
  });
  $(window).on("js_fileUploader", () => {
    $(".js_fileUploader").each(function () {
      createUploadInput(this);
    });
  });
};
