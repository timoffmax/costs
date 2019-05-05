import Dashboard from "./components/Dashboard";
import Profile from "./components/Profile";
import Users from "./components/admin/Users";
import User from "./components/admin/User";
import ApiUsers from "./components/admin/ApiUsers";
import Accounts from "./components/Accounts";
import Places from "./components/Places";
import AccountTypes from "./components/admin/AccountTypes";
import Transactions from "./components/Transactions";
import Transaction from "./components/Transaction";
import TransactionTypes from "./components/admin/TransactionTypes";
import TransactionCategoryTypes from "./components/admin/TransactionCategoryTypes";
import TransactionCategories from "./components/admin/TransactionCategories";

import Forbidden from "./components/errors/Forbidden";
import NotFound from "./components/errors/NotFound";

export const routes = [
    { path: '/', component: Dashboard },
    { path: '/dashboard', component: Dashboard },
    { path: '/profile', component: Profile },
    { path: '/users', component: Users },
    { path: '/user/:id', component: User },
    { path: '/apiUsers', component: ApiUsers },
    { path: '/accounts', component: Accounts },
    { path: '/places', component: Places },
    { path: '/accountTypes', component: AccountTypes },
    { path: '/transactions', component: Transactions },
    { path: '/transaction/:id', component: Transaction },
    { path: '/transactionTypes', component: TransactionTypes },
    { path: '/transactionCategoryTypes', component: TransactionCategoryTypes },
    { path: '/transactionCategories', component: TransactionCategories },

    { path: '/forbidden', component: Forbidden },
    { path: '*', component: NotFound },
];
