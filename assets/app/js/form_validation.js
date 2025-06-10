/**
 * チェックするフォームのセレクター
 * @type {string[]}
 */
const selectors = [
  "input:required",
  "textarea:required",
  "select:required"
];

/**
 * 入力フォームに付与されているエラーメッセージを取得して返す
 * @param element
 * @returns string
 */
const getErrorMessage = (element) => {
  const message = element.getAttribute('data-required-invalid-message');
  return message ? message : "入力されていません";
}
/**
 * エラーメッセージを表示する
 * @param errors エラーメッセージの配列
 * @param errorDom 出力先DOM
 */
const putErrorMessage = (errors, errorDom) => {
  errorDom.replaceChildren();
  errors.forEach((message) => {
    const div = document.createElement("div");
    div.classList.add('c-form-row-body__errors--error');
    div.innerText = message;
    errorDom.appendChild(div);
  });
}
/**
 * 各入力フォームをチェックする
 * @param formElements 入力フォームの親DOM
 * @param selector チェックする入力のcssセレクタ
 * @returns array エラーメッセージ配列
 */
const checkInput = (formElements, selector) => {
  const inputs = formElements.querySelectorAll(selector);
  const errors = [];
  inputs.forEach((input) => {
    if(input.value === "") {
      input.classList.add("is-invalid");
      errors.push(getErrorMessage(input));
    } else {
      input.classList.remove('is-invalid');
    }
  });
  return errors;
}

/**
 * 1行をチェックする
 * @param row フォーム行
 * @returns {number} 0 の場合エラーなし
 */
const checkRow = (row) => {
  let errors = [];
  const formElements = row.querySelector(".js-form-validation__elements");
  const formErrorDom = row.querySelector(".js-form-validation__errors");

  selectors.forEach((selector) => {
    errors = errors.concat(checkInput(formElements, selector));
  });
  putErrorMessage(errors, formErrorDom);

  return errors.length;
}

/**
 * バリデーション開始・イベントハンドラ設定
 * @param form 対象<form>タグ
 */
const validateStart = (form) => {
  selectors.forEach((selector) => {
    const elements = form.querySelectorAll(selector);
    elements.forEach((element) => {
      element.addEventListener("change", () => {
        const row = element.closest('.js-form-validation__row');
        if(row) {
          checkRow(row);
        }
      });
    });
  });
  const rows = form.querySelectorAll('.js-form-validation__row');
  form.addEventListener('submit', (event) => {
    rows.forEach((row) => {
      if(checkRow(row)) {
        event.preventDefault();
      }
    });
  });
}
/**
 * module export
 */
export const formValidation = () => {
  const forms = document.querySelectorAll(".js-form-validation");
  if(!forms.length) return;
  forms.forEach((form) => {
    validateStart(form);
  });
}