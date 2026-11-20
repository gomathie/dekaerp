<?php

namespace Webkul\Security\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\Subgroup;
use Knuckles\Scribe\Attributes\Unauthenticated;
use Webkul\Security\Models\User;

#[Group('Security API Management')]
#[Subgroup('Authentication', 'Handle user authentication')]
class AuthController extends Controller
{
    #[Endpoint('Login', 'Authenticate user and generate API token')]
    #[Unauthenticated]
    #[BodyParam('email', 'string', 'User email address', required: true, example: 'admin@example.com')]
    #[BodyParam('password', 'string', 'User password', required: true, example: 'password')]
    #[BodyParam('token_name', 'string', 'Label for the issued token, shown when auditing or revoking it', required: false, example: 'acme-warehouse-sync')]
    #[BodyParam('abilities', 'string[]', 'Scopes for the token: ["read"] for a token that cannot modify anything, ["read","write"] or ["*"] for full access. Defaults to ["*"].', required: false, example: ['read'])]
    #[Response(status: 200, description: 'Login successful', content: '{"message": "Login successful", "token": "1|abcd1234efgh5678ijkl...", "token_type": "Bearer", "user": {"id": 1, "name": "Admin User", "email": "admin@example.com"}}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"email": ["The email field is required."], "password": ["The password field is required."]}}')]
    #[Response(status: 401, description: 'Invalid credentials', content: '{"message": "The provided credentials are incorrect."}')]
    public function login(Request $request)
    {
        $request->validate([
            'email'       => 'required|email',
            'password'    => 'required',
            'token_name'  => 'sometimes|string|max:255',
            'abilities'   => 'sometimes|array',
            'abilities.*' => 'in:read,write,*',
        ]);

        $user = User::whereRaw(db_dialect()->caseInsensitiveEquals('email'), [$request->email])->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Defaults to "*" so integrations that predate scoping keep the access
        // they were issued with. A client that only reads should be given
        // ["read"], which EnforceApiTokenAbilities turns into a token that
        // cannot POST, PATCH or DELETE anything.
        $abilities = $request->input('abilities', ['*']);

        $token = $user->createToken(
            $request->input('token_name', 'api-token'),
            $abilities,
        )->plainTextToken;

        return response()->json([
            'message'    => 'Login successful',
            'token'      => $token,
            'token_type' => 'Bearer',
            'user'       => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    #[Endpoint('Logout', 'Revoke current API token')]
    #[Authenticated]
    #[Response(status: 200, description: 'Logout successful', content: '{"message": "Logout successful"}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful',
        ]);
    }
}
