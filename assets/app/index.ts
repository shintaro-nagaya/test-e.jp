import "./scss/app.scss";
import {cmsHeadline} from "./js/cms_headline";
import {recaptcha} from "./js/recaptcha";
import {pardotForm} from "./js/pardot_form";
import {formValidation} from "./js/form_validation";

window.addEventListener("DOMContentLoaded", () => {
    cmsHeadline();
    recaptcha();
    pardotForm();
    formValidation();
});
