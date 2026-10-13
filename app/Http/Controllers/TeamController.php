<?php

namespace App\Http\Controllers;

use App\Domain\Email\Services\EmailService;
use App\Http\Controllers\Controller;
use App\Models\CompanyInvite;
use App\Models\CompanyUser;
use App\Models\Supplier;
use App\Models\User;
use App\Services\Company\ActiveContextService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TeamController extends Controller
{
    public function __construct(
        private ActiveContextService $context,
        private EmailService $emailService,
    ) {
    }

    /**
     * Return the supplier represented by the current context.
     */
    private function currentSupplier(): Supplier
    {
        $supplier = $this->context->supplierProfile();

        abort_unless($supplier instanceof Supplier, 403,
            'The active context is not a supplier.'
        );

        return $supplier;
    }

    public function index()
    {
        $supplier = $this->currentSupplier();

        $membersCount = CompanyUser::query()
            ->where('company_type', Supplier::class)
            ->where('company_id', $supplier->id)
            ->count();

        $pendingInvitesCount = CompanyInvite::query()
            ->where('company_type', Supplier::class)
            ->where('company_id', $supplier->id)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->count();

        return view(
            'dashboard.supplier.company.team.index',
            compact('membersCount', 'pendingInvitesCount')
        );
    }

    public function members()
    {
        $supplier = $this->currentSupplier();

        $members = CompanyUser::with('user')
            ->where('company_type', Supplier::class)
            ->where('company_id', $supplier->id)
            ->latest()
            ->get();

        return view(
            'dashboard.supplier.company.team.members',
            compact('members')
        );
    }

    public function invite()
    {
        $supplier = $this->currentSupplier();

        $invites = CompanyInvite::query()
            ->where('company_type', Supplier::class)
            ->where('company_id', $supplier->id)
            ->latest()
            ->get();

        return view(
            'dashboard.supplier.company.team.invite',
            compact('invites')
        );
    }

    /**
     * Create an invitation and send the invitation email.
     */
    public function sendInvite(Request $request)
    {
        $validated = $request->validate([
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
            ],
            'role' => [
    'required',
    'string',
    'in:sales,administrator,representative',
],
        ]);

        $supplier = $this->currentSupplier();
        $email = Str::lower(trim($validated['email']));

        if (Str::lower((string) auth()->user()->email) === $email) {
            throw ValidationException::withMessages([
                'email' => 'You cannot invite your own email address.',
            ]);
        }

        $existingUser = User::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if ($existingUser) {
            $alreadyMember = CompanyUser::query()
                ->where('user_id', $existingUser->id)
                ->where('company_type', Supplier::class)
                ->where('company_id', $supplier->id)
                ->exists();

            if ($alreadyMember) {
                throw ValidationException::withMessages([
                    'email' => 'This user is already a member of the company.',
                ]);
            }
        }

        $pendingInvite = CompanyInvite::query()
            ->where('company_type', Supplier::class)
            ->where('company_id', $supplier->id)
            ->whereRaw('LOWER(email) = ?', [$email])
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->first();

        if ($pendingInvite) {
            throw ValidationException::withMessages([
                'email' => 'A valid invitation has already been sent to this email.',
            ]);
        }

        $expiresAt = now()->addDays(7);

        $invite = CompanyInvite::create([
            'company_id' => $supplier->id,
            'company_type' => Supplier::class,
            'email' => $email,
            'role' => $validated['role'],
            'token' => (string) Str::uuid(),
            'expires_at' => $expiresAt,
            'invited_by' => auth()->id(),
        ]);

        $actionUrl = URL::temporarySignedRoute(
            'team.invitation.accept',
            $expiresAt,
            ['token' => $invite->token]
        );

        try {
            $this->emailService->sendSystemEmail(
                recipient: $invite->email,
                title: 'You are invited to join a company on ACROVOY',
                messageText:
                    'You have been invited to join a supplier company on ACROVOY. '
                    . 'Open the invitation link to sign in or create an account '
                    . 'using this email address, then accept the invitation. '
                    . 'This invitation expires in 7 days.',
                actionUrl: $actionUrl,
                actionLabel: 'Accept invitation',
            );
        } catch (\Throwable $exception) {
            $invite->delete();

            report($exception);

            return back()->withErrors([
                'email' => 'The invitation email could not be queued. Please try again.',
            ])->withInput();
        }

        return back()->with(
            'success',
            'Invitation created and email queued successfully.'
        );
    }

    /**
     * Process an invitation link.
     *
     * Guest users are redirected to registration with the invitation
     * token preserved in the session.
     */
   public function acceptInvitation(Request $request, string $token)
{
    $invite = CompanyInvite::query()
        ->where('token', $token)
        ->firstOrFail();

    if (!$invite->isPending()) {
        return redirect()
            ->route('login')
            ->withErrors([
                'email' => 'This invitation has expired or has already been accepted.',
            ]);
    }

    // Пока принимаем приглашения только в компании Supplier.
    if ($invite->company_type !== Supplier::class) {
        abort(404);
    }

    /*
    |--------------------------------------------------------------------------
    | GUEST: LOGIN OR REGISTRATION
    |--------------------------------------------------------------------------
    */
    if (!auth()->check()) {
        session([
            'company_invite_token' => $invite->token,
            'company_invite_url' => $request->fullUrl(),
        ]);

        $existingUser = User::whereRaw(
            'LOWER(email) = ?',
            [Str::lower($invite->email)]
        )->exists();

        if ($existingUser) {
            return redirect()
                ->route('login')
                ->with(
                    'status',
                    'Please sign in using the email address that received this invitation.'
                );
        }

        return redirect()
            ->route('register')
            ->with(
                'status',
                'Create an account to accept your team invitation.'
            );
    }

    $user = $request->user();

    /*
    |--------------------------------------------------------------------------
    | EMAIL MUST MATCH
    |--------------------------------------------------------------------------
    */
    if (Str::lower((string) $user->email) !== Str::lower($invite->email)) {
        return redirect()
            ->route('dashboard.home')
            ->with(
                'error',
                'This invitation was sent to a different email address. Sign in with the invited account.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | EMAIL VERIFICATION
    |--------------------------------------------------------------------------
    */
    if (!$user->hasVerifiedEmail()) {
        session([
            'company_invite_token' => $invite->token,
            'company_invite_url' => $request->fullUrl(),
        ]);

        return redirect()->route('verification.notice');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCEPT INVITATION
    |--------------------------------------------------------------------------
    */
    $membership = DB::transaction(function () use ($invite, $user) {
        $lockedInvite = CompanyInvite::query()
            ->whereKey($invite->id)
            ->lockForUpdate()
            ->firstOrFail();

        if (!$lockedInvite->isPending()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => 'This invitation has expired or has already been accepted.',
            ]);
        }

        // Recheck email inside the acceptance operation.
        if (Str::lower((string) $user->email) !== Str::lower($lockedInvite->email)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => 'The signed-in email does not match the invitation.',
            ]);
        }

        $membership = CompanyUser::query()
            ->where('user_id', $user->id)
            ->where('company_type', $lockedInvite->company_type)
            ->where('company_id', $lockedInvite->company_id)
            ->first();

        if (!$membership) {
            $role = $lockedInvite->role;

            $membership = CompanyUser::create([
                'user_id' => $user->id,
                'company_type' => $lockedInvite->company_type,
                'company_id' => $lockedInvite->company_id,
                'role' => $role,
            ]);
        }

        $membership->status = 'active';
        $membership->save();

        $lockedInvite->accepted_at = now();
        $lockedInvite->save();

        return $membership;
    });

    /*
    |--------------------------------------------------------------------------
    | ACTIVATE THE JOINED COMPANY
    |--------------------------------------------------------------------------
    */
    session([
        'active_mode' => 'company',
        'active_company_type' => $membership->company_type,
        'active_company_id' => $membership->company_id,
        'active_company_role' => $membership->role,
        'active_personal_mode' => $user->setting('platform_mode', 'buyer'),
    ]);

    $user->setSetting('last_company_user_id', $membership->id);

    session()->forget([
        'company_invite_token',
        'company_invite_url',
    ]);

    return redirect()
        ->route('dashboard.home')
        ->with('success', 'You have joined the supplier company.');
}

public function continueInvitation(string $token)
{
    $invite = CompanyInvite::query()
        ->where('token', $token)
        ->firstOrFail();

    if (!$invite->isPending()) {
        session()->forget([
            'company_invite_token',
            'company_invite_url',
        ]);

        return redirect()
            ->route('dashboard.home')
            ->with('error', 'This invitation has expired or has already been accepted.');
    }

    if ($invite->company_type !== Supplier::class) {
        abort(404);
    }

    $user = auth()->user();

    if (Str::lower((string) $user->email) !== Str::lower($invite->email)) {
        return redirect()
            ->route('dashboard.home')
            ->with('error', 'Please sign in using the email address that received this invitation.');
    }

    if (!$user->hasVerifiedEmail()) {
        return redirect()->route('verification.notice');
    }

    // Передаём управление существующему обработчику принятия приглашения.
    return app(TeamController::class)->acceptInvitation(
        request(),
        $token
    );
}



    public function roles()
    {
        $roles = [
            [
                'name' => 'owner',
                'permissions' => [
                    'Full company access',
                    'Manage team members',
                    'Manage products',
                    'Manage orders',
                    'Manage billing',
                ],
            ],
            [
                'name' => 'admin',
                'permissions' => [
                    'Manage team members',
                    'Manage products',
                    'Manage orders',
                ],
            ],
            [
                'name' => 'member',
                'permissions' => [
                    'Manage products',
                    'View orders',
                ],
            ],
            [
                'name' => 'viewer',
                'permissions' => [
                    'View company data only',
                ],
            ],
        ];

        return view(
            'dashboard.supplier.company.team.roles',
            compact('roles')
        );
    }

    
}
