<?php

namespace App\View\Components\Dashboard;

use Illuminate\View\Component;
use App\Models\CompanyUser;
use App\Facades\ActiveContext;
use App\Services\Menu\MenuService;
use App\Services\Menu\MenuContext;

class Sidebar extends Component
{
    public $companies;
    public $active;
    public $menu;

    public $isPersonal;
    public $role;

    public function __construct()
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | COMPANIES
        |--------------------------------------------------------------------------
        */
        $this->companies = CompanyUser::query()
            ->where('user_id', $user->id)
            ->with('company')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | CONTEXT MODE
        |--------------------------------------------------------------------------
        */
        $this->isPersonal = ActiveContext::mode() === 'personal';

        /*
        |--------------------------------------------------------------------------
        | ACTIVE COMPANY (ONLY IF COMPANY MODE)
        |--------------------------------------------------------------------------
        */
        $this->active = null;

        if (ActiveContext::isCompany()) {

            $this->active = $this->companies->firstWhere(function ($company) {
                return $company->company_id == ActiveContext::id()
                    && $company->company_type == ActiveContext::type();
            });
        }

        /*
        |--------------------------------------------------------------------------
        | ROLE RESOLUTION (IMPORTANT FIX)
        |--------------------------------------------------------------------------
        */
        if (ActiveContext::isCompany()) {

            $this->role = $this->active?->role ?? 'member';

        } else {

            $this->role = $user->setting('platform_mode', 'buyer');
        }

        /*
        |--------------------------------------------------------------------------
        | MENU
        |--------------------------------------------------------------------------
        */
        $menu = collect(
            MenuService::get(
                MenuContext::context($user),
                MenuContext::metrics($user)
            )
        );

        /*
        |--------------------------------------------------------------------------
        | POLICY FILTER
        |--------------------------------------------------------------------------
        */
        $menu = $menu->filter(function ($item) use ($user) {

            if (!isset($item['can'])) {
                return true;
            }

            return $user->can(
                $item['can'][0],
                $item['can'][1]
            );
        });

        /*
        |--------------------------------------------------------------------------
        | REMOVE EMPTY HEADERS
        |--------------------------------------------------------------------------
        */
        $filtered = collect();
        $items = $menu->values();

        for ($i = 0; $i < $items->count(); $i++) {

            $item = $items[$i];

            if ($item['type'] === 'header') {

                $hasLinkInsideSection = false;

                for ($j = $i + 1; $j < $items->count(); $j++) {

                    if ($items[$j]['type'] === 'header') {
                        break;
                    }

                    if ($items[$j]['type'] === 'link') {
                        $hasLinkInsideSection = true;
                        break;
                    }
                }

                if (!$hasLinkInsideSection) {
                    continue;
                }
            }

            $filtered->push($item);
        }

        $this->menu = $filtered->values()->toArray();
    }

    public function render()
    {
        return view('components.dashboard.sidebar');
    }
}