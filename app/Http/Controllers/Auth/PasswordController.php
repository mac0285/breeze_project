<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Lorisleiva\Actions\Concerns\AsAction;
use Lorisleiva\Actions\ActionRequest;
use Illuminate\Validation\Validator;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */

     use AsAction;

     // ...

     public function rules()
     {
         return [
             'current_password' => ['required'],
             'password' => ['required', 'confirmed'],
         ];
     }
     public function withValidator(Validator $validator, ActionRequest $request)
    {
        $validator->after(function (Validator $validator) use ($request) {
            if (! Hash::check($request->get('current_password'), $request->user()->password)) {
                $validator->errors()->add('current_password', 'The current password does not match.');
            }
        });
    }

    public function asController(ActionRequest $request)
    {
        $this->handle(
            $request->user(),
            $request->get('password')
        );

        return redirect()->back();
    }
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}
