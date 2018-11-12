export const routes = [
    { path: '/', component: require('./components/Dashboard') },
    { path: '/dashboard', component: require('./components/Dashboard') },
    { path: '/profile', component: require('./components/Profile') },
    { path: '/users', component: require('./components/Users') },
    { path: '/apiUsers', component: require('./components/ApiUsers') },
    { path: '/forbidden', component: require('./components/errors/Forbidden') },
    { path: '*', component: require('./components/errors/NotFound') },
];
