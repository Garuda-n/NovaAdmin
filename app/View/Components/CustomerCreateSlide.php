<?php

namespace App\View\Components;

use App\Models\Branch;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Services\SettingService;
use Illuminate\View\Component;

class CustomerCreateSlide extends Component
{
    public $countries;
    public $states;
    public $cities;
    public $branches;
    public $defaultCountry;
    public $defaultState;
    public $customerScope;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->countries = Country::orderBy('name')->get();
        $this->defaultCountry = Country::where('is_default', true)->first() ?? $this->countries->first();
        $this->states = $this->defaultCountry ? State::where('country_id', $this->defaultCountry->id)->orderBy('name')->get() : collect();
        $this->defaultState = $this->states->where('is_default', true)->first() ?? $this->states->first();
        $this->cities = $this->defaultState ? City::where('state_id', $this->defaultState->id)->orderBy('name')->get() : collect();
        $this->branches = Branch::where('status', true)->orderBy('name')->get();
        $this->customerScope = SettingService::get('customer_scope', 'Global');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.customer-create-slide');
    }
}
