import "../css/app.css";
import { createApp } from "vue";
import App from "./components/App.vue";
import router from "./router";
import "../js/javascriptcp";
import Swal from "sweetalert2";
import axios from "axios";

axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;
axios.defaults.baseURL = "http://127.0.0.1:8000";

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
