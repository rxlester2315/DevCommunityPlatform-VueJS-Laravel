/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                background: "#0a0a0a",
                surface: "#161616",
                border: "#2a2a2a",
                primary: "#ff6b35",
                accent: "#ff6b35",
                "primary-hover": "#ff8555",
                muted: "#6b7280",
                color: "#e6e6e6",
            },
            fontFamily: {
                sans: [
                    "system-ui",
                    "-apple-system",
                    "BlinkMacSystemFont",
                    "Segoe UI",
                    "Roboto",
                    "sans-serif",
                ],
                mono: [
                    "ui-monospace",
                    "SFMono-Regular",
                    "Menlo",
                    "Monaco",
                    "Consolas",
                    "monospace",
                ],
            },
        },
    },
    plugins: [],
};
