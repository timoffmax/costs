<template>
    <div class="container container-fluid mt-5" v-if="$gate.allow('viewAll', 'statistic')">
        <div class="row control">
            <div class="col md-12">
                <v-md-date-range-picker @change="loadStatisticData">
                </v-md-date-range-picker>
            </div>
        </div>
        <div class="row totals mt-5">
            <div class="col-md-3">
                <div v-if="statistic.costs && $gate.allow('viewAll', 'transaction')" class="info-box bg-danger">
                    <span class="info-box-icon"><i class="fas fa-coins"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Costs</span>
                        <span class="info-box-number">{{ statistic.costs.grandTotal | price }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div v-if="statistic.incomes && $gate.allow('viewAll', 'transaction')" class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-donate"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Incomes</span>
                        <span class="info-box-number">{{ statistic.incomes.grandTotal | price }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div v-if="statistic.deposits && $gate.allow('viewAll', 'transaction')" class="info-box bg-secondary">
                    <span class="info-box-icon"><i class="fas fa-file-invoice-dollar"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Deposits</span>
                        <span class="info-box-number">{{ statistic.deposits.grandTotal | price }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div v-if="statistic.moneybox && $gate.allow('viewAll', 'transaction')" class="info-box bg-primary">
                    <span class="info-box-icon"><i class="fas fa-piggy-bank"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Moneybox</span>
                        <span class="info-box-number">{{ statistic.moneybox.grandTotal | price }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div v-if="statistic.savings && $gate.allow('viewAll', 'transaction')" class="info-box bg-info">
                    <span class="info-box-icon"><i class="fas fa-funnel-dollar"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Other savings</span>
                        <span class="info-box-number">{{ statistic.savings.grandTotal | price }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-5" v-if="statistic">
            <div v-if="statistic.costs && $gate.allow('viewAll', 'transaction')" class="col-md-4">
                <div class="card" v-if="notEmpty(statistic.costs.byPlace)">
                    <div class="card-header border-transparent">
                        <h3 class="card-title">Costs By Place</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table m-0">
                                <thead>
                                    <tr>
                                        <th class="d-none d-sm-block text-left">Place</th>
                                        <th class="text-right">Sum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="place in statistic.costs.byPlace">
                                        <td class="d-none d-sm-block text-left">
                                            <router-link :to="getFilterString('place_id', place.id)">
                                                {{ place.name | capitalize }}
                                            </router-link>
                                        </td>
                                        <td class="text-right text-bold">
                                            {{ place.sum }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="statistic.costs && $gate.allow('viewAll', 'transaction')" class="col-md-4">
                <div class="card" v-if="notEmpty(statistic.costs.byCategory)">
                    <div class="card-header border-transparent">
                        <h3 class="card-title">Costs By Category</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table m-0">
                                <thead>
                                    <tr>
                                        <th class="d-none d-sm-block text-left">Category</th>
                                        <th class="text-right">Sum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="category in statistic.costs.byCategory">
                                        <td class="d-none d-sm-block text-left">
                                            <router-link :to="getFilterString('category_id', category.id)">
                                                {{ category.name | capitalize }}
                                            </router-link>
                                        </td>
                                        <td class="text-right text-bold">
                                            {{ category.sum }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="statistic.costs && $gate.allow('viewAll', 'transaction')" class="col-md-4">
                <div class="card" v-if="notEmpty(statistic.costs.byDay)">
                    <div class="card-header border-transparent">
                        <h3 class="card-title">Costs By Day</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table m-0">
                                <thead>
                                    <tr>
                                        <th class="d-none d-sm-block text-left">Day</th>
                                        <th class="text-right">Sum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="day in statistic.costs.byDay">
                                        <td class="d-none d-sm-block text-left">
                                            <router-link :to="`/transactions?date=${day.date}`">
                                                {{ day.date | dateMoment('MMMM Do YYYY') }}
                                            </router-link>
                                        </td>
                                        <td class="text-right text-bold">
                                            {{ day.sum }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="statistic.costs && $gate.allow('viewAll', 'transaction')" class="col-md-4">
                <div class="card" v-if="notEmpty(statistic.costs.byAccount)">
                    <div class="card-header border-transparent">
                        <h3 class="card-title">Costs By Account</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table m-0">
                                <thead>
                                    <tr>
                                        <th class="d-none d-sm-block text-left">Account</th>
                                        <th class="text-right">Sum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="account in statistic.costs.byAccount">
                                        <td class="d-none d-sm-block text-left">
                                            <router-link :to="getFilterString('account_id', account.id)">
                                                {{ account.name | capitalize }}
                                            </router-link>
                                        </td>
                                        <td class="text-right text-bold">
                                            {{ account.sum }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                settings: {
                    currentUser: user,
                },
                userInfo: {},
                statistic: {},
                dateFrom: moment().startOf('month').format('YYYY-MM-DD'),
                dateTo: moment().endOf('month').format('YYYY-MM-DD'),
            };
        },
        methods: {
            loadStatisticData(momentObjects, datesArray) {
                // Get values from datepicker
                if (Array.isArray(datesArray)) {
                    this.dateFrom = datesArray[0];
                    this.dateTo = datesArray[1];
                }

                axios.get(`api/statistic/${this.dateFrom}/${this.dateTo}`).then(
                    (response) => {
                        this.statistic = response.data;
                    },
                );
            },
            loadUserData() {
                // Get current user data
                axios.get(`api/user/${this.settings.currentUser.id}`).then(
                    (response) => {
                        this.userInfo = response.data;
                    },
                );
            },
            notEmpty(object) {
                return Object.keys(object).length > 0;
            },
            getFilterString(parameter, value) {
                let result = `/transactions?${parameter}=${value}&date=${this.dateFrom}&date=${this.dateTo}`;

                return result
            },
        },
        created() {
            this.loadUserData();
            this.loadStatisticData();
        },
        mounted() {
        }
    }
</script>
