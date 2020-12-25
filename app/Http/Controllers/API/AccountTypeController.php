<?php
declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\AccountType;
use App\Interfaces\RestApiControllerInterface;
use Illuminate\Http\Request;

/**
 * User account type REST controller
 */
class AccountTypeController extends BaseController implements RestApiControllerInterface
{
    /**
     * @inheritDoc
     */
    public function index(Request $request)
    {
        $this->authorize('viewAll', AccountType::class);

        $types = AccountType::all();

        // Prepare a simple list (id => name)
        if (isset($request['mode']) && $request['mode'] === 'simple') {
            $simplifiedList = [];

            foreach ($types as $type) {
                $simplifiedList[$type->id] = $type->name;
            }
        }

        return $simplifiedList ?? $types;
    }

    /**
     * @inheritDoc
     */
    public function store(Request $request)
    {
        $this->authorize('create', AccountType::class);

        $this->validate($request, [
            AccountType::NAME => 'required|string|max:50',
            AccountType::LABEL => 'required|string|max:50',
        ]);

        $result = AccountType::create([
            AccountType::NAME => $request[AccountType::NAME],
            AccountType::LABEL => $request[AccountType::LABEL],
        ]);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function show(int $id)
    {
        $result = AccountType::findOrFail($id);

        $this->authorize('view', $result);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function update(Request $request, int $id)
    {
        $accountTypeModel = AccountType::findOrFail($id);

        $this->authorize('update', $accountTypeModel);

        $this->validate($request, [
            AccountType::NAME => 'required|string|max:50',
            AccountType::LABEL => 'required|string|max:50',
        ]);

        $formData = $request->all();
        $accountTypeModel->fill($formData);
        $accountTypeModel->save();
    }

    /**
     * @inheritDoc
     */
    public function destroy(int $id)
    {
        $accountTypeModel = AccountType::findOrFail($id);

        $this->authorize('delete', $accountTypeModel);

        $accountTypeModel->delete();
    }
}
