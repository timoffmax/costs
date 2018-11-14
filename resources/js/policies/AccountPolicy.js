export default class AccountPolicy
{
    static create(user)
    {
        return false;
    }

    static viewAll(user)
    {
        return false;
    }

    static view(user, model)
    {
        return user.id === model.id;
    }

    static delete(user, model)
    {
        return user.id === model.id;
    }

    static update(user, model)
    {
        return user.id === model.id;
    }
}
