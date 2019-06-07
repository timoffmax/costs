<template>
    <div class="container container-fluid">
        <div class="row mt-5" v-if="$gate.allow('viewAll', 'account')">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Accounts List</h3>
                        <div class="card-tools">
                            <button class="btn btn-success" v-if="$gate.allow('create', 'account')" @click="showAccountModal()">
                                Add
                                <i class="fas fa-plus fa-fw"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th v-if="isAdminMode">User</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th class="text-right">Balance</th>
                                    <th class="text-right">Manage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="account in accounts.data">
                                    <td>{{ account.id }}</td>
                                    <td v-if="isAdminMode"><router-link :to="`/user/${account.user.id}`">{{ account.user.name }}</router-link></td>
                                    <td>{{ account.name }}</td>
                                    <td>{{ account.type.name | capitalize }}</td>
                                    <td class="text-right">{{ account.balance | price(account.currency) }}</td>
                                    <td class="text-right">
                                        <button type="button" class="btn btn-link btn-as-link" v-if="$gate.allow('update', 'account', account)" @click="showAccountModal(account)">
                                            <i class="fas fa-edit text-green"></i>
                                        </button>
                                        /
                                        <button type="button" class="btn btn-link btn-as-link" v-if="$gate.allow('delete', 'account', account)" @click="deleteAccount(account)">
                                            <i class="fas fa-trash text-red"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <pagination :data="accounts" @pagination-change-page="loadAccounts"/>
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>

        <div v-else>
            <forbidden-page />
        </div>

        <!--modal-->
        <div class="modal fade" id="accountModal" ref="accountModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ modal.title }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="formAction">
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="text"
                                       v-model="accountForm.name"
                                       class="form-control"
                                       :class="{'is-invalid': accountForm.errors.has('name')}"
                                       placeholder="Name">
                                <has-error :form="accountForm" field="name"></has-error>
                            </div>
                            <div class="form-group" v-if="isAdminMode">
                                <select v-model="accountForm.user_id"
                                       class="form-control"
                                       :class="{'is-invalid': accountForm.errors.has('user_id')}"
                                >
                                    <option value="">Select User</option>
                                    <option v-for="(user, id) in users" :value="id">{{ user | capitalize }}</option>
                                </select>
                                <has-error :form="accountForm" field="user_id"></has-error>
                            </div>
                            <div class="form-group">
                                <select v-model="accountForm.type_id"
                                       class="form-control"
                                       :class="{'is-invalid': accountForm.errors.has('type_id')}"
                                >
                                    <option value="">Select Account Type</option>
                                    <option v-for="(type, id) in accountTypes" :value="id">{{ type | capitalize }}</option>
                                </select>
                                <has-error :form="accountForm" field="type_id"></has-error>
                            </div>
                            <div class="form-group">
                                <select v-model="accountForm.currency_id"
                                        class="form-control"
                                        :class="{'is-invalid': accountForm.errors.has('currency_id')}"
                                >
                                    <option value="">No currency</option>
                                    <option v-for="currency in currencies" :value="currency.id">{{ currency.sign }}</option>
                                </select>
                                <has-error :form="accountForm" field="currency_id"></has-error>
                            </div>
                            <div class="form-group">
                                <input type="text"
                                       v-model="accountForm.balance"
                                       class="form-control"
                                       :class="{'is-invalid': accountForm.errors.has('balance')}"
                                       placeholder="Balance"
                                       pattern="\d+(\.\d{2})?"
                                >
                                <has-error :form="accountForm" field="balance"></has-error>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">{{ modal.buttonTitle }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                accounts: {},
                accountTypes: [],
                users: [],
                currencies: {},
                pageSize: 10,
                viewMode: 'user',
                currentUser: user,
                modal: {
                    target: this.$refs.accountModal,
                    mode: 'create',
                    title: 'Add new account',
                    buttonTitle: 'Create',
                },
                accountForm: new Form({
                    id : null,
                    user_id: null,
                    type_id: null,
                    name: '',
                    balance: 0.00,
                    currency_id: null,
                }),
            };
        },
        methods: {
            loadAccounts(page = 1) {
                // Prepare query params
                let queryParams = {
                    page: page,
                    pageSize: this.pageSize,
                };

                if (!this.isAdminMode) {
                    queryParams.userId = this.currentUser.id;
                }

                queryParams = Object.keys(queryParams)
                    .map(k => encodeURIComponent(k) + '=' + encodeURIComponent(queryParams[k]))
                    .join('&')
                ;

                // Send request
                axios.get(`api/account?${queryParams}`).then(
                    (response) => {
                        this.accounts = response.data;
                    },
                );
            },
            loadAccountTypes() {
                axios.get(`api/accountType?mode=simple`).then(
                    (response) => {
                        this.accountTypes = response.data;
                    },
                );
            },
            loadUsers() {
                axios.get(`api/user?mode=simple`).then(
                    (response) => {
                        this.users = response.data;
                    },
                );
            },
            loadCurrencies() {
                axios.get(`api/currency`).then(
                    (response) => {
                        this.currencies = response.data;
                    },
                );
            },
            clearModal() {
                this.accountForm.clear();
                this.accountForm.reset();
            },
            showAccountModal(account = null) {
                if (account) {
                    // Set modal params
                    this.modal.mode = 'edit';
                    this.modal.title = 'Edit the account';
                    this.modal.buttonTitle = 'Save';

                    // Fill form
                    this.accountForm.fill(account);
                } else {
                    // Set modal params
                    this.modal.mode = 'create';
                    this.modal.title = 'Add a new account';
                    this.modal.buttonTitle = 'Create';
                    this.accountForm.user_id = this.currentUser.id;
                    this.accountForm.balance = '0.00';
                }

                // Show modal
                $(this.$refs.accountModal).modal('show');
            },
            createAccount() {
                this.$Progress.start();

                // Add new user
                this.accountForm.post('api/account')
                    .then(response => {
                        this.$Progress.finish();

                        // Refresh the table content
                        this.loadAccounts();

                        // Close the modal and clean the form
                        $(this.$refs.accountModal).modal('hide');

                        // Show success message
                        toast({
                            type: 'success',
                            title: 'Account added successfully'
                        });
                    })
                    .catch(error => {
                        this.$Progress.fail();

                        // Show error message
                        let responseData = error.response.data;

                        toast({
                            type: 'error',
                            title: responseData.message
                        });
                    })
                ;
            },
            updateAccount() {
                this.$Progress.start();

                // Add new user
                this.accountForm.put(`api/account/${this.accountForm.id}`)
                    .then(response => {
                        this.$Progress.finish();

                        // Refresh the table content
                        this.loadAccounts();

                        // Close the modal and clean the form
                        $(this.$refs.accountModal).modal('hide');

                        // Show success message
                        toast({
                            type: 'success',
                            title: 'Account updated successfully'
                        });
                    })
                    .catch(error => {
                        this.$Progress.fail();

                        // Show error message
                        let responseData = error.response.data;

                        toast({
                            type: 'error',
                            title: responseData.message
                        });
                    })
                ;
            },
            deleteAccount(account) {
                swal({
                    title: 'Are you sure?',
                    html: `You're going to delete account "<span class="font-weight-bold">${account.name}</span>"!`,
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        this.$Progress.start();

                        // Delete
                        axios.delete(`api/account/${account.id}`)
                            .then(response => {
                                // Update progress bar
                                this.$Progress.finish();

                                // Update the table
                                this.loadAccounts()

                                // Show the success message
                                toast({
                                    type: 'success',
                                    title: 'Account has been deleted'
                                });
                            })
                            .catch(error => {
                                this.$Progress.fail();

                                toast({
                                    type: 'error',
                                    title: 'Server error! Can`t delete the account.'
                                });
                            })
                        ;
                    }
                })
            },
        },
        computed: {
            formAction() {
                return this.modal.mode === 'create' ? this.createAccount : this.updateAccount;
            },
            isAdminMode() {
                return this.viewMode === 'admin';
            },
        },
        created() {
            // Events
            $(document).on("hidden.bs.modal", this.clearModal);

            // Load accounts to the table
            this.loadAccountTypes();
            this.loadAccounts();
            this.loadCurrencies();

            if (this.isAdminMode) {
                this.loadUsers();
            }
        },
        mounted() {},
    }
</script>
