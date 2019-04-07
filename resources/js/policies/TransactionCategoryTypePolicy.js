export default class TransactionCategoryTypePolicy
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
