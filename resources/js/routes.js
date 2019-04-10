export const routes = [
    { path: '/', component: require('./components/Dashboard') },
    { path: '/dashboard', component: require('./components/Dashboard') },
    { path: '/profile', component: require('./components/Profile') },
    { path: '/users', component: require('./components/admin/Users') },
    { path: '/user/:id', component: require('./components/admin/User') },
    { path: '/apiUsers', component: require('./components/admin/ApiUsers') },
    { path: '/accounts', component: require('./components/Accounts') },
    { path: '/accountTypes', component: require('./components/admin/AccountTypes') },
    { path: '/transactions', component: require('./components/Transactions') },
    { path: '/transactionTypes', component: require('./components/admin/TransactionTypes') },
    { path: '/transactionCategoryTypes', component: require('./components/admin/TransactionCategoryTypes') },
    { path: '/transactionCategories', component: require('./components/admin/TransactionCategories') },

    { path: '/forbidden', component: require('./components/errors/Forbidden') },
    { path: '*', component: require('./components/errors/NotFound') },
];
