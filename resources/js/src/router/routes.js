import store from "@/src/store";

const checkAuth = (to, from, next) => {
    if (!store.state.auth.isAuthenticated && to.name !== 'Login') {
        next({ name: 'Login' });
    } else if(store.state.auth.isAuthenticated && to.name === 'Login') {
        next({ name: 'Home' });
    } else {
        next();
    }
}

const routes = [
    {
        path: '/spa',
        component: () => import('@/src/pages/Home.vue'),
        beforeEnter: checkAuth,
        name: 'Home'
    },
    {
        path: '/spa/events',
        component: () => import('@/src/pages/Events.vue'),
        beforeEnter: checkAuth,
        name: 'Events'
    },
    {
        path: '/spa/login',
        component: () => import('@/src/pages/Login.vue'),
        beforeEnter: checkAuth,
        name: 'Login'
    }
];

export default routes;
