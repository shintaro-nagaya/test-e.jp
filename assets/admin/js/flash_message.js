/* eslint-disable eqeqeq */
/* eslint-disable func-names */
import $ from "jquery";

const flashMessages = $(".js_flash");
export const flashMessage = () => {
  if (flashMessages.length === 0) return;
  flashMessages.each(function () {
    $(this).on("click", function () {
      $(this).fadeOut(240);
    });
  });
};
