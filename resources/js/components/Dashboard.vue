<template>
    <div class="container container-fluid mt-5">
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
            <div class="col-lg-3 col-6">
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
        <div class="row" v-if="info.transactions">
            <div class="col-md-4">
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
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header border-transparent">
                        <h3 class="card-title">Latest Orders</h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-widget="collapse">
                                <i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-widget="remove">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table m-0">
                                <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Item</th>
                                    <th>Status</th>
                                    <th>Popularity</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><a href="pages/examples/invoice.html">OR9842</a></td>
                                    <td>Call of Duty IV</td>
                                    <td><span class="badge badge-success">Shipped</span></td>
                                    <td>
                                        <div class="sparkbar" data-color="#00a65a" data-height="20"><canvas width="34" height="20" style="display: inline-block; width: 34px; height: 20px; vertical-align: top;"></canvas></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><a href="pages/examples/invoice.html">OR1848</a></td>
                                    <td>Samsung Smart TV</td>
                                    <td><span class="badge badge-warning">Pending</span></td>
                                    <td>
                                        <div class="sparkbar" data-color="#f39c12" data-height="20"><canvas width="34" height="20" style="display: inline-block; width: 34px; height: 20px; vertical-align: top;"></canvas></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><a href="pages/examples/invoice.html">OR7429</a></td>
                                    <td>iPhone 6 Plus</td>
                                    <td><span class="badge badge-danger">Delivered</span></td>
                                    <td>
                                        <div class="sparkbar" data-color="#f56954" data-height="20"><canvas width="34" height="20" style="display: inline-block; width: 34px; height: 20px; vertical-align: top;"></canvas></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><a href="pages/examples/invoice.html">OR7429</a></td>
                                    <td>Samsung Smart TV</td>
                                    <td><span class="badge badge-info">Processing</span></td>
                                    <td>
                                        <div class="sparkbar" data-color="#00c0ef" data-height="20"><canvas width="34" height="20" style="display: inline-block; width: 34px; height: 20px; vertical-align: top;"></canvas></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><a href="pages/examples/invoice.html">OR1848</a></td>
                                    <td>Samsung Smart TV</td>
                                    <td><span class="badge badge-warning">Pending</span></td>
                                    <td>
                                        <div class="sparkbar" data-color="#f39c12" data-height="20"><canvas width="34" height="20" style="display: inline-block; width: 34px; height: 20px; vertical-align: top;"></canvas></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><a href="pages/examples/invoice.html">OR7429</a></td>
                                    <td>iPhone 6 Plus</td>
                                    <td><span class="badge badge-danger">Delivered</span></td>
                                    <td>
                                        <div class="sparkbar" data-color="#f56954" data-height="20"><canvas width="34" height="20" style="display: inline-block; width: 34px; height: 20px; vertical-align: top;"></canvas></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><a href="pages/examples/invoice.html">OR9842</a></td>
                                    <td>Call of Duty IV</td>
                                    <td><span class="badge badge-success">Shipped</span></td>
                                    <td>
                                        <div class="sparkbar" data-color="#00a65a" data-height="20"><canvas width="34" height="20" style="display: inline-block; width: 34px; height: 20px; vertical-align: top;"></canvas></div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.table-responsive -->
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer clearfix">
                        <a href="javascript:void(0)" class="btn btn-sm btn-info float-left">Place New Order</a>
                        <a href="javascript:void(0)" class="btn btn-sm btn-secondary float-right">View All Orders</a>
                    </div>
                    <!-- /.card-footer -->
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
        },
        computed: {
            allAccountsAmount() {
                let sum = 0;

                for (let account of this.userInfo.accounts) {
                    sum += parseFloat(account.balance);
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
