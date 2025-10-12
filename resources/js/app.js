import "../css/app.css";
import { createApp } from "vue";
import App from "./components/App.vue";
import router from "./router";
import "../js/javascriptcp";
import Swal from "sweetalert2";
createApp(App).use(router).mount("#app");
