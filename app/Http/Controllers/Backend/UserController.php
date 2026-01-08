<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreUserRequest;
use App\Http\Requests\Backend\UpdateUserRequest;
use App\Services\Backend\UserFilterService;
use App\Services\Backend\UserService;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private const PAGINATION_LIMIT = 10;

    public function __construct(
        protected UserFilterService $filterService,
        protected UserService $userService
    ) {
        $this->middleware(['auth', 'permission:manage-users']);
    }

    public function index(Request $request): View
    {
        $users = $this->filterService->getFilteredUsers($request, self::PAGINATION_LIMIT);
        $roles = Role::orderBy('name')->get();

        return view('backend.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        if (request()->ajax()) {
            return view('backend.users._form', compact('roles'));
        }
        return view('backend.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        try {
            $user = $this->userService->createUser(
                $request->validated(),
                $request->roles ?? []
            );

            $token = $this->userService->createApiToken($user, 'Auto Generated Token');
            $message = 'Пользователь создан успешно. API токен: ' . $token->plainTextToken;

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'redirect' => route('backend.users.index')
                ]);
            }

            return redirect()->route('backend.users.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('User creation failed') . ': ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', __('User creation failed'));
        }
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('name')->toArray();
        if (request()->ajax()) {
            return view('backend.users._form', compact('user', 'roles', 'userRoles'));
        }
        return view('backend.users.edit', compact('user', 'roles', 'userRoles'));
    }

    public function update(UpdateUserRequest $request, User $user): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        try {
            $data = $request->validated();
            if ($request->password) {
                $data['password'] = $request->password;
            }

            $this->userService->updateUser($user, $data, $request->roles ?? []);

            if ($request->ajax()) {
                session()->flash('success', 'Пользователь успешно обновлен');
                return response()->json([
                    'success' => true,
                    'redirect' => route('backend.users.index')
                ]);
            }

            return redirect()->route('backend.users.index')->with('success', 'Пользователь успешно обновлен');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('User update failed') . ': ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', __('User update failed'));
        }
    }

    public function destroy(User $user): RedirectResponse
    {
        try {
            $this->userService->deleteUser($user);
            return redirect()->route('backend.users.index')
                ->with('success', __('User deleted successfully'));
        } catch (\Exception $e) {
            return back()->with('error', __('User deletion failed'));
        }
    }

    public function generateApiToken(User $user): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        try {
            $token = $this->userService->createApiToken($user, 'Admin Generated Token');
            $message = 'API токен успешно сгенерирован: ' . $token->plainTextToken;

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    // Ideally we should update the modal content or at least reloading handles it
                    'redirect' => null // No redirect needed if we just show toast, but modal reload would be nice
                ]);
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка при генерации API токена: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Ошибка при генерации API токена: ' . $e->getMessage());
        }
    }

    public function revokeApiToken(User $user): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        try {
            $this->userService->revokeApiTokens($user);
            $message = 'API токен успешно отозван';

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'redirect' => null
                ]);
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка при отзыве API токена: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Ошибка при отзыве API токена: ' . $e->getMessage());
        }
    }
}
