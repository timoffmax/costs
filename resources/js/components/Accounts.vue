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
                    <div class="card card-primary card-outline card-outline-tabs">
                        <div class="card-header p-0 border-bottom-0">
                            <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active"
                                       id="all-accounts-tab"
                                       data-toggle="pill"
                                       href="#all-accounts"
                                       role="tab"
                                       aria-controls="account-tabs-all"
                                       aria-selected="true"
                                       @click="selectedType = null"
                                    >
                                        All
                                    </a>
                                </li>
                                <li v-for="accountType in accountTypes" class="nav-item">
                                    <a class="nav-link"
                                       :id="`${accountType.name}-accounts-tab`"
                                       data-toggle="pill"
                                       :href="`#${accountType.name}-accounts`"
                                       role="tab"
                                       :aria-controls="`account-tabs-${accountType.name}`"
                                       aria-selected="false"
                                       @click="selectedType = accountType.id"
                                    >
                                        {{ accountType.label }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="account-tabs-all-tabContent">
                                <div class="tab-pane fade active show" id="all-accounts" role="tabpanel" aria-labelledby="all-accounts-tab">
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
                                            <tr v-for="account in accounts.data" :class="isArchived(account) ? 'text-muted' : ''">
                                                <td>{{ account.id }}</td>
                                                <td v-if="isAdminMode"><router-link :to="`/user/${account.user.id}`">{{ account.user.name }}</router-link></td>
                                                <td>
                                                    <span v-show="isArchived(account)" class="badge badge-danger">ARCHIVED</span>
                                                    {{ account.name }}
                                                </td>
                                                <td>{{ account.type.label | capitalize }}</td>
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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                                    <option v-for="type in accountTypes" :value="type.id">{{ type.label | capitalize }}</option>
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
                                       pattern="\d+(\.\d{1,2})?"
                                >
                                <small class="form-text text-muted">
                                    Use format 123 or 123.45
                                </small>
                                <has-error :form="accountForm" field="balance"></has-error>
                            </div>
                            <div class="form-group">
                                <label class="toggle">
                                    <input @change="toggleCalculateCosts()" class="toggle__input" type="checkbox" :checked="!accountForm.calculate_costs">
                                    <span class="toggle__label">
                                        <span class="toggle__text">Don't calculate costs for this account</span>
                                    </span>
                                </label>
                                <has-error :form="accountForm" field="calculate_costs"></has-error>
                            </div>
                            <div class="form-group">
                                <label class="toggle">
                                    <input @change="toggleIsArchived()" class="toggle__input" type="checkbox" :checked="accountForm.is_archived">
                                    <span class="toggle__label">
                                        <span class="toggle__text">Archived</span>
                                    </span>
                                </label>
                                <has-error :form="accountForm" field="is_archived"></has-error>
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
                selectedType: null,
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
                    balance: null,
                    currency_id: null,
                    calculate_costs: 1,
                    is_archived: 0,
                }),
            };
        },
        methods: {
            loadAccounts(page = 1) {
                // Prepare query params
                let queryParams = {
                    page: page,
                    pageSize: this.pageSize,
                    type_id: this.selectedType,
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
                axios.get(`api/accountType`).then(
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
            selectAccountType() {

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

                        this.loadAccounts();
                        $(this.$refs.accountModal).modal('hide');

                        toast.fire({
                            icon: 'success',
                            title: 'Account added successfully'
                        });
                    })
                    .catch(error => {
                        this.$Progress.fail();

                        let responseData = error.response.data;

                        toast.fire({
                            icon: 'error',
                            title: responseData.message
                        });
                    })
                ;
            },
            updateAccount() {
                this.$Progress.start();

                this.accountForm.put(`api/account/${this.accountForm.id}`)
                    .then(response => {
                        this.$Progress.finish();

                        this.loadAccounts();
                        $(this.$refs.accountModal).modal('hide');

                        toast.fire({
                            icon: 'success',
                            title: 'Account updated successfully'
                        });
                    })
                    .catch(error => {
                        this.$Progress.fail();

                        let responseData = error.response.data;

                        toast.fire({
                            icon: 'error',
                            title: responseData.message
                        });
                    })
                ;
            },
            deleteAccount(account) {
                swal.fire({
                    title: 'Are you sure?',
                    html: `You're going to delete account "<span class="font-weight-bold">${account.name}</span>"!`,
                    icon: 'warning',
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
                                this.$Progress.finish();

                                this.loadAccounts()

                                toast.fire({
                                    icon: 'success',
                                    title: 'Account has been deleted'
                                });
                            })
                            .catch(error => {
                                this.$Progress.fail();

                                toast.fire({
                                    icon: 'error',
                                    title: 'Server error! Can`t delete the account.'
                                });
                            })
                        ;
                    }
                })
            },
            toggleCalculateCosts() {
                this.accountForm.calculate_costs = !this.accountForm.calculate_costs;
            },
            isArchived(account) {
                return account.is_archived;
            },
            toggleIsArchived() {
                this.accountForm.is_archived = !this.accountForm.is_archived;
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
        watch: {
            selectedType: {
                handler: function(newValue, oldValue) {
                    this.loadAccounts();
                },
            }
        },
    }
</script>
