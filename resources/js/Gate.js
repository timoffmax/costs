import UserPolicy from './policies/UserPolicy';
import ApiUserPolicy from "./policies/ApiUserPolicy";
import UserRolePolicy from "./policies/UserRolePolicy";
import AccountTypePolicy from "./policies/AccountTypePolicy";
import AccountPolicy from "./policies/AccountPolicy";
import TransactionTypePolicy from "./policies/TransactionTypePolicy";

export default class Gate
{
    constructor(user)
    {
        this.user = user;

        this.policies = {
            user: UserPolicy,
            userRole: UserRolePolicy,
            apiUser: ApiUserPolicy,
            account: AccountPolicy,
            accountType: AccountTypePolicy,
            transactionType: TransactionTypePolicy,
        };
    }

    before()
    {
        return this.user.role.name === 'admin';
    }

    allow(action, type, model = null)
    {
        if (this.before()) {
            return true;
        }

        return this.policies[type][action](this.user, model);
    }

    deny(action, type, model = null)
    {
        return !this.allow(action, type, model);
    }
}
