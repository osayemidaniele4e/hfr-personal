import withMT from "@material-tailwind/react/utils/withMT";

module.exports = withMT({
  content: [
    "./app/**/*.{js,ts,jsx,tsx,mdx}",
    "./pages/**/*.{js,ts,jsx,tsx,mdx}",
    "./components/**/*.{js,ts,jsx,tsx,mdx}",
  ],
  daisyui: {
    themes: [
      {
        mytheme: {
          primary: "#2563EB",
          secondary: "#ff6600",
          grey: "#6B6D70",
          neutral: "#191D24",
          place: "#23272e",
          "base-100": "#FFF",
          info: "#3ABFF8",
          success: "#36D399",
          warning: "#FBBD23",
          error: "#F87272",
        },
        // "accent": "#019a4a",
      },
    ],
  },
  theme: {
    extend: {
      color: {
        primary: "#2563EB",
        secondary: "#ff6600",
        grey: "#6B6D70",
        neutral: "#191D24",
        place: "#23272e",
        white: "#FFF",
        info: "#3ABFF8",
        success: "#36D399",
        warning: "#FBBD23",
        error: "#F87272",
        green: "#078586",
      },
      fontFamily: { body: ["Poppins"] },
    },
  },

  plugins: [require("daisyui"), require("@tailwindcss/aspect-ratio")],
});
