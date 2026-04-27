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

    public function __construct()
    {
        $user = auth()->user();

        /**
         * Load companies
         */
        $this->companies = CompanyUser::query()
            ->where('user_id', $user->id)
            ->with('company')
            ->get();

        /**
         * Detect mode
         */
        $this->isPersonal = ActiveContext::isPersonal();

        /**
         * Resolve active company (company mode only)
         */
        $this->active = null;

        if (! $this->isPersonal) {

            $this->active = $this->companies->firstWhere(function ($company) {
                return $company->company_id == ActiveContext::id()
                    && $company->company_type == ActiveContext::type();
            });
        }

        /**
         * Build menu
         */
        /**
         * Build menu (policy filtered + remove empty headers)
         */
        $menu = collect(
            MenuService::get(
                MenuContext::context($user),
                MenuContext::metrics($user)
            )
        );

        /**
         * Step 1: filter by policy
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
 * Step 2: remove empty headers correctly
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

    private function can($roles): bool
    {
        if (!$roles) return true;

        $role = ActiveContext::role();

        return in_array($role, (array) $roles);
    }
}
