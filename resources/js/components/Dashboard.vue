<template>
    <div class="container container-fluid mt-5" v-if="$gate.allow('viewAll', 'dashboard')">
        <div v-if="info.transactions" class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ info.transactions.currentMonth.costs | price }}</h3>
                        <p>Costs</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-coins"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ info.transactions.currentMonth.incomes | price }}</h3>
                        <p>Incomes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-donate"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ info.transactions.currentMonth.count }}</h3>
                        <p>Transactions</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-random"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div v-if="userInfo.accounts" class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ allAccountsAmount }}</h3>
                        <p>Total Amount</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
        <div class="row" v-if="info.transactions && $gate.allow('viewAll', 'transaction')">
            <div class="d-none d-md-block col-md-4">
                <div class="info-box mb-3 bg-success">
                    <span class="info-box-icon"><i class="fas fa-donate"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Last Month Incomes</span>
                        <span class="info-box-number">{{ info.transactions.lastMonth.incomes | price }}</span>
                    </div>
                </div>
                <div class="info-box mb-3 bg-danger">
                    <span class="info-box-icon"><i class="fas fa-coins"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Last Month Costs</span>
                        <span class="info-box-number">{{ info.transactions.lastMonth.costs | price }}</span>
                    </div>
                </div>
                <div class="info-box mb-3 bg-primary">
                    <span class="info-box-icon"><i class="fas fa-random"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Last Month Transactions</span>
                        <span class="info-box-number">{{ info.transactions.lastMonth.count }}</span>
                    </div>
                </div>
            </div>
            <div v-if="info.transactions.latest && $gate.allow('viewAll', 'transaction')" class="col-md-8">
                <div class="card card-primary" v-if="notEmpty(info.charts.thisMonth.costs.byCategory)">
                    <div class="card-header">
                        <h3 class="card-title">This Month Costs By Category</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body" id="this-month-costs">
                        <doughnut-chart-custom :raw-data="info.charts.thisMonth.costs.byCategory"
                        >
                        </doughnut-chart-custom>
                    </div>
                </div>
                <div class="card card-secondary" v-if="notEmpty(info.charts.lastMonth.costs.byCategory)">
                    <div class="card-header">
                        <h3 class="card-title">Last Month Costs By Category</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <doughnut-chart-custom :raw-data="info.charts.lastMonth.costs.byCategory"
                        >
                        </doughnut-chart-custom>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header border-transparent">
                        <h3 class="card-title">Latest Transactions</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table m-0">
                                <thead>
                                <tr>
                                    <th class="d-none d-sm-block text-center">ID</th>
                                    <th class="text-center">Date</th>
                                    <th class="d-none d-sm-block text-center">Account</th>
                                    <th class="text-right">Sum</th>
                                    <th class="d-none d-sm-block text-center">Category</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="transaction in info.transactions.latest">
                                    <td class="d-none d-sm-block text-center">
                                        <router-link :to="`/transaction/${transaction.id}`">
                                            {{ transaction.id }}
                                        </router-link>
                                    </td>
                                    <td class="text-center">
                                        {{ transaction.date | dateMoment('MMMM Do YYYY') }}
                                    </td>
                                    <td class="d-none d-sm-block text-center">
                                        {{ transaction.account.name | capitalize }}
                                    </td>
                                    <td class="text-right text-bold" :class="getAmountClass(transaction, 'text')">
                                        {{ transaction | transactionAmount }}
                                    </td>
                                    <td class="d-none d-sm-block text-center">
                                            <span class="badge badge-dark text-white">
                                                {{ transaction.category.name | capitalize }}
                                            </span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer clearfix">
                        <router-link :to="`/transactions`" class="btn btn-sm btn-outline-dark float-left">
                            All transactions
                        </router-link>
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
                info: {},
            };
        },
        methods: {
            loadDashboardData() {
                // Get current user dashboard data
                axios.get(`api/dashboard`).then(
                    (response) => {
                        this.info = response.data;
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
            getAmountClass(transaction, prefix = null, amount = null) {
                let colorClass = prefix ? `${prefix}-` : '';

                if (null === amount) {
                    switch (transaction.type.name) {
                        case 'income':
                            colorClass += 'success';
                            break;

                        case 'cost':
                            colorClass += 'danger';
                            break;

                        default:
                            colorClass += 'info';
                    }
                } else {
                    switch (true) {
                        case amount > 0:
                            colorClass += 'success';
                            break;

                        case amount < 0:
                            colorClass += 'danger';
                            break;

                        default:
                            colorClass += 'info';
                    }
                }

                return colorClass;
            },
            notEmpty(object) {
                return Object.keys(object).length > 0;
            },
        },
        computed: {
            allAccountsAmount() {
                let sum = 0;

                for (let account of this.userInfo.accounts) {
                    if (account.calculate_costs) {
                        sum += parseFloat(account.balance);
                    }
                }

                return sum.toFixed(2);
            },
            monthTransactions() {
                let sum = 0;

                for (let account of this.userInfo.accounts) {
                    sum += parseFloat(account.balance);
                }

                return sum.toFixed(2);
            }
        },
        created() {
            this.loadUserData();
            this.loadDashboardData();
        },
        mounted() {
        }
    }
</script>

<style scoped lang="scss">
    .small-box {
        .inner {
            h3 {
                font-size: 22px;
            }
        }
    }
</style>
