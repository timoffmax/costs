export default class TransactionTypePolicy
{
    static create(user)
    {
        return false;
    }

    static viewAll(user)
    {
        return true;
    }

    static view(user, model)
    {
        return false;
    }

    static delete(user, model)
    {
        return false;
    }

    static update(user, model)
    {
        return false;
    }
}
