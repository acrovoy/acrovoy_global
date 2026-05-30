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
    public $personalMode; // buyer / supplier / null
    public $role;

    public function __construct()
    {
        $user = auth()->user();

        /**
         * Load companies
         */
        $this->companies = CompanyUser::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->with('company')
            ->get();

        /**
         * Detect personal mode
         */
        $this->isPersonal = ActiveContext::isPersonal();

        $this->role = ActiveContext::role();

        /**
         * Personal mode type (buyer / supplier)
         */
        $this->personalMode = $this->isPersonal
            ? session('platform_mode', 'buyer')
            : null;

        /**
         * Resolve active company
         */
        $this->active = null;

        if (ActiveContext::isCompany()) {

            $this->active = $this->companies->firstWhere(function ($company) {
                return $company->company_id == ActiveContext::id()
                    && $company->company_type == ActiveContext::type();
            });
        }

        /**
         * Build menu
         */
        $menu = collect(
            MenuService::get(
                MenuContext::context($user),
                MenuContext::metrics($user)
            )
        );

        /**
         * Filter by policy
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

        /**
         * Remove empty headers
         */
        $filtered = collect();
        $items = $menu->values();

        for ($i = 0; $i < $items->count(); $i++) {

            $item = $items[$i];

            if (($item['type'] ?? null) === 'header') {

                $hasLinkInsideSection = false;

                for ($j = $i + 1; $j < $items->count(); $j++) {

                    if (($items[$j]['type'] ?? null) === 'header') {
                        break;
                    }

                    if (($items[$j]['type'] ?? null) === 'link') {
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