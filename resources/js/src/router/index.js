import { createRouter, createWebHistory } from "vue-router";
import routes from "@/src/router/routes.js";

const router = createRouter({
    history: createWebHistory(),
    routes,
    linkActiveClass: 'active'
});

export default router;
