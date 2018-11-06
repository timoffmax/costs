import UserPolicy from './policies/UserPolicy';

export default class Gate
{
    constructor(user)
    {
        this.user = user;

        this.policies = {
            user: UserPolicy,
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
