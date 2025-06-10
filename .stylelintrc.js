module.exports = {
  extends: [
    "stylelint-config-airbnb", // https://github.com/nao215/css-style-guide
    "stylelint-config-sass-guidelines", // https://github.com/bjankord/stylelint-config-sass-guidelines
    "stylelint-config-recess-order",
    "stylelint-config-prettier",
  ],
  plugins: [
    "stylelint-scss",
    "stylelint-order",
    "stylelint-no-unsupported-browser-features",
  ],
  rules: {
    // アルファベット順に並べる機能を切る
    "order/properties-alphabetical-order": null,
    // 不要なショートハンドを制限する
    "declaration-block-no-shorthand-property-overrides": true,
    // ブラウザに対応していない機能を使っている場合は警告する
    "plugin/no-unsupported-browser-features": [
      true,
      {
        severity: "warning",
        message: "IE11で機能しない可能性があります。",
      },
    ],
    // BEM パターンに沿ってセレクタを書く
    "selector-class-pattern": [
      "^(?:(?:o|c|u|t|s|is|has|_|js|qa)-)?[a-zA-Z0-9]+(?:-[a-zA-Z0-9]+)*(?:__[a-zA-Z0-9]+(?:-[a-zA-Z0-9]+)*)?(?:--[a-zA-Z0-9]+(?:-[a-zA-Z0-9]+)*)?(?:\\[.+\\])?$",
      {
        message: "classの命名規則がmindBEMingに沿っていません。",
      },
    ],
    // ネストの深さを制限する
    "max-nesting-depth": [
      2,
      {
        message: "ネストを2以上深くしないでください。",
      },
    ],
  },
};
