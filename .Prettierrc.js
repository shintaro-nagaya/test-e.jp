module.exports = {
  printWidth: 80,
  tabWidth: 2,
  plugins: ["./node_modules/prettier-plugin-twig-melody"],
  twigMelodyPlugins: ["node_modules/prettier-plugin-twig-enhancements"],
  overrides: [
    {
      files: ["*.html.twig"],
      options: {
        tabWidth: 4,
      },
    },
  ],
};
