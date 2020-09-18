<template>
    <div class="container-fluid mt-5" v-if="$gate.allow('viewAll', 'statistic')">
        <div class="row control">
            <div class="col md-12">
                <v-md-date-range-picker @change="loadStatisticData"
                                        :startDate="dateFrom"
                                        :endDate="dateTo"
                >
                </v-md-date-range-picker>
            </div>
        </div>
        <div class="row totals mt-5">
            <div class="col-md-3">
                <div v-if="statistic.costs && $gate.allow('viewAll', 'transaction')" class="info-box bg-danger">
                    <span class="info-box-icon"><i class="fas fa-coins"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Costs</span>
                        <template v-if="Object.keys(statistic.costs.totalsByCurrency).length > 0">
                            <span v-for="(sum, currency) in statistic.costs.totalsByCurrency" v-if="sum > 0" class="info-box-number">
                                {{ currency }}{{ sum | price }}
                            </span>
                        </template>
                        <template v-else>
                            <span class="info-box-number">--</span>
                        </template>
                        <span v-if="false" class="info-box-number">
                            <!-- hide till currency conversion is implemented -->
                            <b>Total: {{ statistic.costs.grandTotal | price }}</b>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div v-if="statistic.incomes && $gate.allow('viewAll', 'transaction')" class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-donate"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Incomes</span>
                        <template v-if="Object.keys(statistic.incomes.totalsByCurrency).length > 0">
                            <span v-for="(sum, currency) in statistic.incomes.totalsByCurrency" v-if="sum > 0" class="info-box-number">
                                {{ currency }}{{ sum | price }}
                            </span>
                        </template>
                        <template v-else>
                            <span class="info-box-number">--</span>
                        </template>
                        <span v-if="false" class="info-box-number">
                            <!-- hide till currency conversion is implemented -->
                            <b>Total: {{ statistic.incomes.grandTotal | price }}</b>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div v-if="statistic.deposits && $gate.allow('viewAll', 'transaction')" class="info-box bg-secondary">
                    <span class="info-box-icon"><i class="fas fa-file-invoice-dollar"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Deposits</span>
                        <template v-if="Object.keys(statistic.deposits.totalsByCurrency).length > 0">
                            <span v-for="(sum, currency) in statistic.deposits.totalsByCurrency" v-if="sum > 0" class="info-box-number">
                                {{ currency }}{{ sum | price }}
                            </span>
                        </template>
                        <template v-else>
                            <span class="info-box-number">--</span>
                        </template>
                        <span v-if="false" class="info-box-number">
                            <!-- hide till currency conversion is implemented -->
                            <b>Total: {{ statistic.deposits.grandTotal | price }}</b>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div v-if="statistic.moneybox && $gate.allow('viewAll', 'transaction')" class="info-box bg-primary">
                    <span class="info-box-icon"><i class="fas fa-piggy-bank"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Moneybox</span>
                        <template v-if="Object.keys(statistic.moneybox.totalsByCurrency).length > 0">
                            <span v-for="(sum, currency) in statistic.moneybox.totalsByCurrency" v-if="sum > 0" class="info-box-number">
                                {{ currency }}{{ sum | price }}
                            </span>
                        </template>
                        <template v-else>
                            <span class="info-box-number">--</span>
                        </template>
                        <span v-if="false" class="info-box-number">
                            <!-- hide till currency conversion is implemented -->
                            <b>Total: {{ statistic.moneybox.grandTotal | price }}</b>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div v-if="statistic.savings && $gate.allow('viewAll', 'transaction')" class="info-box bg-info">
                    <span class="info-box-icon"><i class="fas fa-funnel-dollar"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Other savings</span>
                        <template v-if="Object.keys(statistic.savings.totalsByCurrency).length > 0">
                            <span v-for="(sum, currency) in statistic.savings.totalsByCurrency" v-if="sum > 0" class="info-box-number">
                                {{ currency }}{{ sum | price }}
                            </span>
                        </template>
                        <template v-else>
                            <span class="info-box-number">--</span>
                        </template>
                        <span v-if="false" class="info-box-number">
                            <!-- hide till currency conversion is implemented -->
                            <b>Total: {{ statistic.savings.grandTotal | price }}</b>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-5" v-if="statistic">
            <div v-if="statistic.costs && $gate.allow('viewAll', 'transaction')" class="col-md-3">
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
                                    <tr v-for="(place, placeName) in statistic.costs.byPlace">
                                        <td class="d-none d-sm-block text-left">
                                            <router-link :to="{name: 'transactions', params: {filters: {category_id: categoryId}}}">
                                                {{ placeName | capitalize }}
                                            </router-link>
                                        </td>
                                        <td class="text-right text-bold">
                                            <p v-for="(sum, currency) in place.sum">
                                                {{ currency }}{{ sum }}
                                            </p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="statistic.costs && $gate.allow('viewAll', 'transaction')" class="col-md-3">
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
                                    <tr v-for="(category, categoryId) in statistic.costs.byCategory">
                                        <td class="d-none d-sm-block text-left">
                                            <router-link :to="{name: 'transactions', params: {filters: {category_id: categoryId}}}">
                                                {{ category.name | capitalize }}
                                            </router-link>
                                        </td>
                                        <td class="text-right text-bold">
                                            <p v-for="(sum, currency) in category.sum">
                                                {{ currency }}{{ sum }}
                                            </p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="statistic.costs && $gate.allow('viewAll', 'transaction')" class="col-md-3">
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
                                    <tr v-for="(day, date) in statistic.costs.byDay">
                                        <td class="d-none d-sm-block text-left">
                                            <router-link :to="{name: 'transactions', params: {filters: {date: date}}}">
                                                {{ date | dateMoment('MMMM Do YYYY') }}
                                            </router-link>
                                        </td>
                                        <td class="text-right text-bold">
                                            <p v-for="(info, currency) in day">
                                                {{ currency }}{{ info.sum }}
                                            </p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="statistic.costs && $gate.allow('viewAll', 'transaction')" class="col-md-3">
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
                                            <router-link :to="{name: 'transactions', params: {filters: {account_id: account.id}}}">
                                                {{ account.name | capitalize }}
                                            </router-link>
                                        </td>
                                        <td class="text-right text-bold">
                                            {{ account.currency }}{{ account.sum }}
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
        },
        created() {
            this.loadUserData();
            this.loadStatisticData();
        },
        mounted() {
        }
    }
</script>
