<template>
    <div class="container container-fluid">
        <div class="row mt-5" v-if="$gate.allow('view', 'user')">
            <div class="col-12">
                <div class="card card-widget widget-user">
                    <!-- Add the bg color to the header using any of the bg-* classes -->
                    <div class="widget-user-header bg-primary">
                        <h3 class="widget-user-username">{{ user.name }}</h3>
                        <h5 v-if="user.role" class="widget-user-desc">{{ user.role.name | capitalize }}</h5>
                    </div>
                    <div class="widget-user-image">
                        <div v-if="!user.photo" v-html="svg('user', 'img-circle elevation-2 user-profile-image-main')" />
                        <img v-else class="img-circle elevation-2" :src="user.photo" :alt="user.name">
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-sm-4 border-right">
                                <div class="description-block">
                                    <h5 v-if="user.accounts" class="description-header">{{ Object.keys(user.accounts).length }}</h5>
                                    <span class="description-text">ACCOUNTS</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4 border-right">
                                <div class="description-block">
                                    <h5 v-if="user.accounts" class="description-header">{{ allAccountsAmount }}</h5>
                                    <span class="description-text">MONEY</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4">
                                <div class="description-block">
                                    <h5 v-if="user.accounts" class="description-header">{{ Object.keys(user.transactions).length }}</h5>
                                    <span class="description-text">TRANSACTIONS</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
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
    export default {
        data() {
            return {
                user: {},
            };
        },
        methods: {
            loadUser() {
                axios.get(`/api/user/${this.$route.params.id}`).then(
                    (response) => {
                        this.user = response.data;
                    },
                );
            },
        },
        created() {
            this.loadUser();
        },
        computed: {
            allAccountsAmount() {
                let sum = 0;

                for (let account of this.user.accounts) {
                    sum += parseFloat(account.balance);
                }

                return sum.toFixed(2);
            }
        },
        mounted() {},
    }
</script>
