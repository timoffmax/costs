export default class TransactionPolicy
{
    static create(user)
    {
        return true;
    }

    static viewAll(user)
    {
        return true;
    }

    static view(user, model)
    {
        return user.id === model.user_id;
    }

    static delete(user, model)
    {
        return user.id === model.user_id;
    }

    static update(user, model)
    {
        return user.id === model.user_id;
    }
}
