<?php
declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Facades\Image;

/**
 * User REST controller
 */
class UserController extends BaseController
{
    /**
     * UserController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->authorizeResource(User::class);
    }

    /**
     * Display list of users
     *
     * @param Request $request
     * @return array
     * @throws AuthorizationException
     */
    public function index(Request $request)
    {
        $users = User::latest();

        // Prepare a simple list (id => name)
        if (isset($request['mode']) && $request['mode'] === 'simple') {
            $simplifiedList = [];

            foreach ($users->get() as $user) {
                $simplifiedList[$user->id] = $user->name;
            }

            return $simplifiedList;
        }

        // Get paginated result
        $pageSize = $request['pageSize'] ?? 10;
        $paginatedList = $users->paginate($pageSize);

        return $paginatedList;
    }

    /**
     * Store a newly created user
     *
     * @param Request $request
     * @return UserResource
     */
    public function store(Request $request): UserResource
    {
        $this->validate($request, [
            'name' => 'required|string|max:50',
            'role_id' => 'required|integer',
            'email' => 'required|string|email|max:150|unique:users',
            'password' => 'required|string|min:8|max:30',
            'passwordConfirmation' => 'required|required_with:password|same:password|string|min:8|max:30',
        ]);

        $password = Hash::make($request->password);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role_id = $request->role_id;
        $user->password = $password;
        $user->save();

        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return UserResource
     */
    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return UserResource
     * @throws ValidationException
     */
    public function update(Request $request, User $user): UserResource
    {
        $this->validate($request, [
            'name' => 'required|string|max:50',
            'role_id' => 'sometimes|required|integer',
            'email' => "required|string|email|max:150|unique:users,email,{$user->id}",
            'password' => 'sometimes|required|string|min:8|max:30',
            'passwordConfirmation' => 'required_with:password|same:password|string|min:8|max:30',
        ]);

        $originalData = clone $user;
        $user->fill($request->all());

        if (!empty($request->password)) {
            $user->password = Hash::make($request->password);
        }

        $oldPhoto = $originalData->photo;
        $photoWasChanged = false;

        if (!empty($request->photo) && $request->photo !== $originalData->photo) {
            // Get extension and create unique name
            $extension = explode('/', explode(';', $request->photo)[0])[1];
            $filename = uniqid('profile_image_', true) . ".{$extension}";
            $fullPath = public_path("img/profile/{$filename}");

            // Create image from Base64 and update user info
            $result = Image::make($request->photo)
                ->resize(128, 128)
                ->save($fullPath);

            if ($result) {
                $user->photo = "/img/profile/{$filename}";
                $photoWasChanged = true;
                $oldPhoto = public_path($oldPhoto);
            }
        }

        if ($user->save()) {
            // Remove old profile photo
            if ($photoWasChanged && !empty($oldPhoto) && file_exists($oldPhoto)) {
                unlink($oldPhoto);
            }
        }

        return new UserResource($user);
    }

    /**
     * @param User $user
     * @return Response
     * @throws \Exception
     */
    public function destroy(User $user): Response
    {
        $user->delete();

        return response()->noContent();
    }
}
