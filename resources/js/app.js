import "../css/app.css";
import { createApp } from "vue";
import App from "./components/App.vue";
import router from "./router";
import "../js/javascriptcp";
import Swal from "sweetalert2";
import axios from "axios";

axios.defaults.withXSRFToken = true; // back-end or laravel protection
axios.defaults.baseURL = import.meta.env.VITE_APP_API_BASE_URL;
axios.defaults.withCredentials = true; // Important for Sanctum
axios.interceptors.request.use((config) => {
    const token = localStorage.getItem("auth_token");
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

createApp(App).use(router).mount("#app");
