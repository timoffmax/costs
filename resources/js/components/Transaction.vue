<template>
    <div class="container container-fluid">
        <div class="row mt-5 justify-content-center" v-if="$gate.allow('view', 'transaction')">
            <div v-if="loaded" class="col-md-6 col-sm-12">
                <div class="card card-widget widget-user-2">
                    <div class="widget-user-header text-center" :class="getAmountClass('bg')">
                        <h3>{{ transaction | transactionAmount }}</h3>
                        <h5>{{ transaction.date | dateMoment('MMMM Do YYYY') }}</h5>
                    </div>
                    <div class="card-footer p-0">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <div class="nav-link">
                                    <span class="text-bold">ID</span>
                                    <span class="float-right badge bg-info">
                                        {{ transaction.id }}
                                    </span>
                                </div>
                            </li>
                            <li v-if="isAdminMode" class="nav-item">
                                <div class="nav-link">
                                    <span class="text-bold">User Name</span>
                                    <span class="float-right">
                                        <router-link :to="`/user/${transaction.user_id}`">{{ transaction.user.name }}</router-link>
                                    </span>
                                </div>
                            </li>
                            <li class="nav-item">
                                <div class="nav-link">
                                    <span class="text-bold">Date</span>
                                    <span class="float-right badge bg-warning">
                                        {{ transaction.date | dateMoment('MMMM Do YYYY') }}
                                    </span>
                                </div>
                            </li>
                            <li class="nav-item">
                                <div class="nav-link">
                                    <span class="text-bold">Category</span>
                                    <span class="float-right badge bg-warning">
                                        {{ transaction.category.name | capitalize }}
                                    </span>
                                </div>
                            </li>
                            <li class="nav-item">
                                <div class="nav-link">
                                    <span class="text-bold">Account</span>
                                    <span class="float-right badge bg-warning">
                                        {{ transaction.account.name | capitalize }}
                                    </span>
                                </div>
                            </li>
                            <li class="nav-item">
                                <div class="nav-link">
                                    <span class="text-bold">Place</span>
                                    <span class="float-right badge bg-warning">
                                        {{ transactionPlace | capitalize }}
                                    </span>
                                </div>
                            </li>
                            <li class="nav-item">
                                <div class="nav-link">
                                    <span class="text-bold">Sum</span>
                                    <span class="float-right badge" :class="getAmountClass('bg')">
                                        {{ transaction | transactionAmount }}
                                    </span>
                                </div>
                            </li>
                            <li class="nav-item">
                                <div class="nav-link">
                                    <span class="text-bold">Balance Before</span>
                                    <span class="float-right badge" :class="getAmountClass('bg', transaction.balance_before)">
                                        {{ transaction.balance_before }}
                                    </span>
                                </div>
                            </li>
                            <li class="nav-item">
                                <div class="nav-link">
                                    <span class="text-bold">Balance After</span>
                                    <span class="float-right badge" :class="getAmountClass('bg', transaction.balance_after)">
                                        {{ transaction.balance_after }}
                                    </span>
                                </div>
                            </li>
                            <li class="nav-item">
                                <div class="nav-link">
                                    <span class="text-bold">Comment</span>
                                    <div v-if="transaction.comment">
                                        {{ transaction.comment }}
                                    </div>
                                    <div v-else>
                                        No comment
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            <forbidden-page />
        </div>
    </div>
</template>

<script>
    const FLAG_MODE_ADMIN = 'admin';
    const FLAG_MODE_USER = 'user';

    export default {
        data() {
            return {
                settings: {
                    currentUser: user,
                    viewMode: FLAG_MODE_USER,
                },
                transaction: {},
                loaded: false,
            };
        },
        methods: {
            loadTransaction() {
                axios.get(`/api/transaction/${this.$route.params.id}`).then(
                    (response) => {
                        this.transaction = response.data;
                        this.loaded = true;
                    },
                );
            },
            getAmountClass(prefix = null, amount = null) {
                let colorClass = prefix ? `${prefix}-` : '';

                if (null === amount) {
                    switch (this.transaction.type.name) {
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
        },
        created() {
            this.loadTransaction();
        },
        computed: {
            isAdminMode() {
                return this.settings.viewMode === FLAG_MODE_ADMIN;
            },
            transactionPlace() {
                return this.transaction.place ? this.transaction.place.name : 'not specified';
            }
        },
        mounted() {},
    }
</script>

<style lang="scss" scoped>
    .badge {
        font-size: 12px;
    }
</style>
