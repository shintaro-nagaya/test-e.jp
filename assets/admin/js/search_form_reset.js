/* eslint-disable func-names */
import $ from "jquery";

const resetSearchForm = function (form) {
  const reset = $(".js_search-form-reset", form);
  if (reset.length === 0) return;
  reset.on("click", (e) => {
    e.preventDefault();
    form
      .find(
        "input[type=text],input[type=number],input[type=date],input[type=email]"
      )
      .each(function () {
        $(this).val("");
      });
    form.find("select").each(function () {
      $(this).find("option").first().prop("selected", "selected");
    });
    form.find("input[type=checkbox]").each(function () {
      $(this).prop("checked", false);
    });
    form.submit();
  });
};
export const searchFormReset = () => {
  $(".js_search-form").each(function () {
    resetSearchForm($(this));
  });
};
