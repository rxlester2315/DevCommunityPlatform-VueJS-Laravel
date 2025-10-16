import "../css/app.css";
import { createApp } from "vue";
import App from "./components/App.vue";
import router from "./router";
import "../js/javascriptcp";
import Swal from "sweetalert2";
import axios from "axios";

axios.defaults.withCredentials = true; // this is for cookies and session when mag rerequest
axios.defaults.withXSRFToken = true; // back-end or laravel protection
axios.defaults.baseURL = "http://127.0.0.1:8000"; // axios call direct laravel back-end

axios.interceptors.request.use((config) => {
    const token = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");
    if (token) {
        config.headers["X-CSRF-TOKEN"] = token;
    }
    return config;
});

createApp(App).use(router).mount("#app");
