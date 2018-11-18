export const routes = [
    { path: '/', component: require('./components/Dashboard') },
    { path: '/dashboard', component: require('./components/Dashboard') },
    { path: '/profile', component: require('./components/Profile') },
    { path: '/users', component: require('./components/admin/Users') },
    { path: '/user/:id', component: require('./components/admin/User') },
    { path: '/apiUsers', component: require('./components/admin/ApiUsers') },
    { path: '/accounts', component: require('./components/admin/Accounts') },
    { path: '/accountTypes', component: require('./components/admin/AccountTypes') },
    // { path: '/transactions', component: require('./components/admin/Transaction') },
    { path: '/transactionTypes', component: require('./components/admin/TransactionTypes') },

    { path: '/forbidden', component: require('./components/errors/Forbidden') },
    { path: '*', component: require('./components/errors/NotFound') },
];
