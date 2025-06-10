/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./assets/**/*.ts", "./templates/**/*.html.twig"],
  theme: {
    fontFamily: {

    },
    colors: {
      white: "#FFFFFF",
      black: "#000000",
    },
    extend: {
      spacing: {
        25: "6.25rem",
        30: "7.5rem",
        50: "12.5rem",
        100: "25rem",
      },
      fontSize: {
        xxs: ["0.625rem", null] /* 10px */,
        xs: ["0.75rem", null] /* 12px */,
        sm: ["0.875rem", null] /* 14px */,
        base: ["1rem", null] /* 16px */,
        lg: ["1.125rem", null] /* 18px */,
        xl: ["1.25rem", null] /* 20px */,
        "2xl": ["1.5rem", null] /* 24px */,
        "3xl": ["1.75rem", null] /* 28px */,
        "4xl": ["2rem", null] /* 32px */,
        "5xl": ["2.25rem", null] /* 36px */,
        "6xl": ["2.5rem", null] /* 40px */,
        "7xl": ["2.75rem", null] /* 44px */,
        "8xl": ["3rem", null] /* 48px */,
        "9xl": ["3.5rem", null] /* 56px */,
        "10xl": ["4rem", null] /* 64px */,
        "11xl": ["5rem", null] /* 80px */,
        "12xl": ["10rem", null] /* 160px */,
      },
      screens: {
        "2xs": "390px",
        xs: "414px",
        "3xl": "1920px",
        "4xl": "2560px",
      },
    },
  },
  plugins: [],
};