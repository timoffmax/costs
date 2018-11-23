<template>
    <div class="container container-fluid">
        <div class="row mt-5" v-if="$gate.allow('viewAll', 'transaction')">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Transactions List</h3>
                        <div class="card-tools">
                            <button class="btn btn-success" v-if="$gate.allow('create', 'transaction')" @click="showTransactionModal()">
                                Add
                                <i class="fas fa-user-plus fa-fw"></i>
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
                                    <th>Date</th>
                                    <th>Account</th>
                                    <th class="text-right">Sum</th>
                                    <th class="text-right">Balance Before</th>
                                    <th class="text-right">Balance After</th>
                                    <th>Comment</th>
                                    <th class="text-right">Manage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="transaction in transactions.data">
                                    <td>{{ transaction.id }}</td>
                                    <td v-if="isAdminMode"><router-link :to="`/user/${transaction.user.id}`">{{ transaction.user.name }}</router-link></td>
                                    <td>{{ transaction.date | dateMoment('MMMM Do YYYY') }}</td>
                                    <td>{{ transaction.account.name }}</td>
                                    <td :class="getTextClass(transaction)" class="text-right">{{ transaction | transactionAmount }}</td>
                                    <td class="text-right">{{ transaction.balance_before }}</td>
                                    <td class="text-right">{{ transaction.balance_after }}</td>
                                    <td>{{ transaction.comment }}</td>
                                    <td class="text-right">
                                        <button type="button" class="btn btn-link btn-as-link" v-if="$gate.allow('update', 'transaction', transaction)" @click="showTransactionModal(transaction)">
                                            <i class="fas fa-edit text-green"></i>
                                        </button>
                                        /
                                        <button type="button" class="btn btn-link btn-as-link" v-if="$gate.allow('delete', 'transaction', transaction)" @click="deleteTransaction(transaction)">
                                            <i class="fas fa-trash text-red"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <pagination  :data="transactions" @pagination-change-page="loadTransactions"/>
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>

        <div v-else>
            <forbidden-page />
        </div>

        <!--modal-->
        <div class="modal fade" id="transactionModal" ref="transactionModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
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
                            <div v-if="isAdminMode" class="form-group">
                                <select v-model="transactionForm.user_id"
                                       class="form-control"
                                       :class="{'is-invalid': transactionForm.errors.has('user_id')}"
                                >
                                    <option value="">Select User</option>
                                    <option v-for="(id, user) in users" :value="user">{{ id }}</option>
                                </select>
                                <has-error :form="transactionForm" field="user_id"></has-error>
                            </div>
                            <div class="form-group">
                                <select v-model="transactionForm.type_id"
                                       class="form-control"
                                       :class="{'is-invalid': transactionForm.errors.has('type_id')}"
                                >
                                    <option value="">Select Transaction Type</option>
                                    <option v-for="(id, type) in transactionTypes" :value="type">{{ id }}</option>
                                </select>
                                <has-error :form="transactionForm" field="type_id"></has-error>
                            </div>
                            <div class="form-group">
                                <select v-model="transactionForm.account_id"
                                        class="form-control"
                                        :class="{'is-invalid': transactionForm.errors.has('account_id')}"
                                >
                                    <option value="">Select Account</option>
                                    <option v-for="account in currentUser.accounts" :value="account.id">{{ account.name }}</option>
                                </select>
                                <has-error :form="transactionForm" field="account_id"></has-error>
                            </div>
                            <div class="form-group">
                                <input type="date"
                                       v-model="transactionForm.date"
                                       class="form-control"
                                       :class="{'is-invalid': transactionForm.errors.has('date')}">
                                <has-error :form="transactionForm" field="date"></has-error>
                            </div>
                            <div class="form-group">
                                <input type="text"
                                       v-model="transactionForm.sum"
                                       class="form-control"
                                       :class="{'is-invalid': transactionForm.errors.has('sum')}"
                                       placeholder="Sum"
                                       pattern="\d+\.\d{2}"
                                >
                                <has-error :form="transactionForm" field="sum"></has-error>
                            </div>
                            <div class="form-group">
                                <textarea v-model="transactionForm.comment"
                                       class="form-control"
                                       :class="{'is-invalid': transactionForm.errors.has('comment')}"
                                       placeholder="Comment">
                                </textarea>
                                <has-error :form="transactionForm" field="comment"></has-error>
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
                transactions: {},
                transactionTypes: [],
                userAccounts: [],
                currentUser: user,
                users: [],
                pageSize: 50,
                viewMode: 'user',
                modal: {
                    target: this.$refs.transactionModal,
                    mode: 'create',
                    title: 'Add new transaction',
                    buttonTitle: 'Create',
                },
                transactionForm: new Form({
                    id : null,
                    user_id: null,
                    type_id: null,
                    account_id: null,
                    sum: 0.00,
                    date: '',
                    comment: '',
                }),
            };
        },
        methods: {
            loadTransactions(page = 1) {
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
                axios.get(`api/transaction?${queryParams}`).then(
                    (response) => {
                        this.transactions = response.data;
                    },
                );
            },
            loadTransactionTypes() {
                axios.get(`api/transactionType?mode=simple`).then(
                    (response) => {
                        this.transactionTypes = response.data;
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
            clearModal() {
                this.transactionForm.clear();
                this.transactionForm.reset();
            },
            showTransactionModal(transaction = null) {
                if (transaction) {
                    // Set modal params
                    this.modal.mode = 'edit';
                    this.modal.title = 'Edit the transaction';
                    this.modal.buttonTitle = 'Save';

                    // Fill form
                    this.transactionForm.fill(transaction);
                    this.transactionForm.date = this.transactionForm.date.substr(0, 10);

                } else {
                    // Set modal params
                    this.modal.mode = 'create';
                    this.modal.title = 'Add a new transaction';
                    this.modal.buttonTitle = 'Create';
                    this.transactionForm.user_id = this.currentUser.id;
                    this.transactionForm.sum = '0.00';
                    this.transactionForm.date = this.currentDate;
                }

                // Show modal
                $(this.$refs.transactionModal).modal('show');
            },
            createTransaction() {
                this.$Progress.start();

                // Add new user
                this.transactionForm.post('api/transaction')
                    .then(response => {
                        this.$Progress.finish();

                        // Refresh the table content
                        this.loadTransactions();

                        // Close the modal and clean the form
                        $(this.$refs.transactionModal).modal('hide');

                        // Show success message
                        toast({
                            type: 'success',
                            title: 'Transaction added successfully'
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
            updateTransaction() {
                this.$Progress.start();

                // Add new user
                this.transactionForm.put(`api/transaction/${this.transactionForm.id}`)
                    .then(response => {
                        this.$Progress.finish();

                        // Refresh the table content
                        this.loadTransactions();

                        // Close the modal and clean the form
                        $(this.$refs.transactionModal).modal('hide');

                        // Show success message
                        toast({
                            type: 'success',
                            title: 'Transaction updated successfully'
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
            deleteTransaction(transaction) {
                swal({
                    title: 'Are you sure?',
                    html: `You're going to delete transaction "<span class="font-weight-bold">${transaction.id}</span>"!`,
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        this.$Progress.start();

                        // Delete
                        axios.delete(`api/transaction/${transaction.id}`)
                            .then(response => {
                                // Update progress bar
                                this.$Progress.finish();

                                // Update the table
                                this.loadTransactions()

                                // Show the success message
                                toast({
                                    type: 'success',
                                    title: 'Transaction has been deleted'
                                });
                            })
                            .catch(error => {
                                this.$Progress.fail();

                                toast({
                                    type: 'error',
                                    title: 'Server error! Can`t delete the transaction.'
                                });
                            })
                        ;
                    }
                })
            },
            getTextClass(transaction) {
                let textClass = 'text-';

                switch (transaction.type.name) {
                    case 'income':
                        textClass += 'success';
                        break;

                    case 'cost':
                        textClass += 'danger';
                        break;

                    default:
                        textClass += 'info';
                }

                return textClass;
            }
        },
        computed: {
            formAction() {
                return this.modal.mode === 'create' ? this.createTransaction : this.updateTransaction;
            },
            isAdminMode() {
                return this.viewMode === 'admin';
            },
            currentDate() {
                return (new Date()).toISOString().substring(0, 10);
            }
        },
        created() {
            // Events
            $(document).on("hidden.bs.modal", this.clearModal);

            // Load transactions to the table
            this.loadTransactions();
            this.loadTransactionTypes();

            if (this.isAdminMode) {
                this.loadUsers();
            }
        },
        mounted() {},
    }
</script>
