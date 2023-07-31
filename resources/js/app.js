import './bootstrap';
import { createApp } from "vue";
import App from './src/App.vue';
import router from "@/src/router";
import store from "@/src/store";

createApp(App)
    .use(router)
    .use(store)
    .mount('#app');
