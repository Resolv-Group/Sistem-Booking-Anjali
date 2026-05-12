/** @type {import('tailwindcss').Config} */
export default {
    content: ["./resources/**/*.blade.php", "./resources/**/*.js"],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Manrope", "sans-serif"],
            },
            colors: {
                primary: "#0F766E",
                soft: "#F8FAFA",
                danger: "#DC2626",
            },
            borderRadius: {
                card: "20px",
            },
        },
    },

    plugins: [],
};
