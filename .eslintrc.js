module.exports = {
  env: {
    browser: true,
    es6: true,
    amd: true,
    jquery: true,
    node: true,
  },
  extends: ["airbnb-base", "plugin:import/errors", "prettier"],
  plugins: ["import"],
  parser: "babel-eslint",
  rules: {
    "no-console": "off",
  },
  overrides: [
    {
      files: ["*.ts"],
      extends: [
        "airbnb-typescript/base",
        "plugin:@typescript-eslint/recommended",
        "plugin:@typescript-eslint/recommended-requiring-type-checking",
        "prettier",
      ],
      plugins: ["@typescript-eslint"],
      parser: "@typescript-eslint/parser",
      parserOptions: {
        project: "./tsconfig.json",
      },
      rules: {
        "@typescript-eslint/no-unsafe-call": "warn",
        "import/extensions": [
          1,
          "ignorePackages",
          {
            js: "always",
            scss: "always",
            css: "always",
          },
        ],
      },
    },
  ],
};
