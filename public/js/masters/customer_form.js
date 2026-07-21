/**
 * NovaAdmin - Customer Form Alpine Component
 */
function customerForm(config) {
    return {
        type: config.type || 'B2C',
        countryId: config.countryId || '',
        stateId: config.stateId || '',
        cityId: config.cityId || '',
        isBranchWise: config.isBranchWise ?? false,
        statesList: [],
        citiesList: [],

        init() {
            if (this.countryId && this.statesList.length === 0) {
                this.fetchStates(false);
            }
            if (this.stateId && this.citiesList.length === 0) {
                this.fetchCities(false);
            }
        },

        fetchStates(resetState = true) {
            if (!this.countryId) {
                this.statesList = [];
                this.citiesList = [];
                return;
            }
            if (resetState) {
                this.stateId = '';
                this.cityId = '';
                this.citiesList = [];
            }
            fetch(`/locations/states/${this.countryId}`)
                .then(res => res.json())
                .then(data => {
                    this.statesList = data;
                })
                .catch(err => console.error('Error fetching states:', err));
        },

        fetchCities(resetCity = true) {
            if (!this.stateId) {
                this.citiesList = [];
                return;
            }
            if (resetCity) {
                this.cityId = '';
            }
            fetch(`/locations/cities/${this.stateId}`)
                .then(res => res.json())
                .then(data => {
                    this.citiesList = data;
                })
                .catch(err => console.error('Error fetching cities:', err));
        }
    };
}
