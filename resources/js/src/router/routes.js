const routes = [
    {
        path: '/spa',
        component: import('@/src/pages/Home.vue')
    },
    {
        path: '/spa/login',
        component: import('@/src/pages/Login.vue')
    },
    {
        path: '/spa/events',
        component: import('@/src/pages/Events.vue')
    },
];

export default routes;
