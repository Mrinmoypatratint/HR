/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        charcoal: {
          DEFAULT: "#1C1C1E",
          50: "#F6F6F7",
          100: "#E6E6E8",
          200: "#C9C9CD",
          300: "#9C9CA3",
          400: "#6B6B75",
          500: "#45454F",
          600: "#32323A",
          700: "#24242A",
          800: "#1C1C1E",
          900: "#121214",
        },
        brand: {
          orange: "#FF6B1A",
          "orange-hover": "#E55607",
          "orange-light": "#FFF3EB",
          "orange-border": "#FFD4BD",
        },
        status: {
          present: "#16A34A",
          "present-bg": "#ECFDF5",
          "present-border": "#A7F3D0",
          late: "#F59E0B",
          "late-bg": "#FFFBEB",
          "late-border": "#FDE68A",
          absent: "#DC2626",
          "absent-bg": "#FEF2F2",
          "absent-border": "#FECACA",
          leave: "#2563EB",
          "leave-bg": "#EFF6FF",
          "leave-border": "#BFDBFE",
        },
        surface: {
          DEFAULT: "#FAFAF8",
          card: "#FFFFFF",
          muted: "#EDEDEA",
          border: "#E2E8F0",
        },
      },
      fontFamily: {
        heading: ["Plus Jakarta Sans", "Bricolage Grotesque", "sans-serif"],
        body: ["Plus Jakarta Sans", "DM Sans", "sans-serif"],
        mono: ["JetBrains Mono", "monospace"],
      },
      borderRadius: {
        card: "16px",
        button: "10px",
      },
      boxShadow: {
        soft: "0 1px 3px rgba(15, 23, 42, 0.04), 0 1px 2px rgba(15, 23, 42, 0.02)",
        elevated: "0 4px 6px -1px rgba(15, 23, 42, 0.07), 0 2px 4px -2px rgba(15, 23, 42, 0.05)",
        floating: "0 20px 25px -5px rgba(15, 23, 42, 0.1), 0 8px 10px -6px rgba(15, 23, 42, 0.05)",
      },
    },
  },
  plugins: [],
};
