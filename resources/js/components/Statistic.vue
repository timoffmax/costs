<template>
    <div class="container container-fluid mt-5" v-if="$gate.allow('viewAll', 'statistic')">
        <div class="row control">
            <v-md-date-range-picker start-date="2019-12-01"
                                    end-date="2019-12-31"
                                    @change="loadStatisticData"
            >

            </v-md-date-range-picker>
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
                                    <tr v-for="(sum, name) in statistic.costs.byPlace">
                                        <td class="d-none d-sm-block text-left">
                                            <router-link :to="`/transaction/${name}`">
                                                {{ name | capitalize }}
                                            </router-link>
                                        </td>
                                        <td class="text-right text-bold">
                                            {{ sum }}
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
                                    <tr v-for="(sum, name) in statistic.costs.byCategory">
                                        <td class="d-none d-sm-block text-left">
                                            <router-link :to="`/transaction/${name}`">
                                                {{ name | capitalize }}
                                            </router-link>
                                        </td>
                                        <td class="text-right text-bold">
                                            {{ sum }}
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
                                    <tr v-for="(sum, day) in statistic.costs.byDay">
                                        <td class="d-none d-sm-block text-left">
                                            <router-link :to="`/transaction/${day}`">
                                                {{ day | dateMoment('MMMM Do YYYY') }}
                                            </router-link>
                                        </td>
                                        <td class="text-right text-bold">
                                            {{ sum }}
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
            };
        },
        methods: {
            loadStatisticData(momentObjects, datesArray) {
                // Set default values
                let from = moment().startOf('month').format('YYYY-MM-DD');
                let to = moment().endOf('month').format('YYYY-MM-DD');

                // Get values from datepicker
                if (Array.isArray(datesArray)) {
                    from = datesArray[0];
                    to = datesArray[1];
                }

                axios.get(`api/statistic/${from}/${to}`).then(
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
        computed: {

        },
        created() {
            this.loadUserData();
            this.loadStatisticData();
        },
        mounted() {
        }
    }
</script>
