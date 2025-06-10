import "./scss/admin.scss";
import "@popperjs/core/lib/popper.js";
import "bootstrap/dist/js/bootstrap.js";
import {flashMessage} from "./js/flash_message.js";
import {fileUploader} from "./js/file_uploader.js";
import {cmsChildren} from "./js/cms_children.js";
import {inputFormSubmit} from "./js/input_form_submit.js";
import {searchFormReset} from "./js/search_form_reset.js";
import {relationChoice} from "./js/relation_choice";
import {recaptcha} from "./js/recaptcha";
import {adminTheme} from "./js/admin_theme";
import {jqSelect2} from "./js/jq_select2";

window.addEventListener("DOMContentLoaded", () => {
    flashMessage();
    fileUploader();
    cmsChildren();
    inputFormSubmit();
    searchFormReset();
    relationChoice();
    recaptcha();
    adminTheme();
    jqSelect2();
});
