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
                                <i class="fas fa-plus fa-fw"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <div>
                            <vue-good-table v-if="transactions"
                                            @on-page-change="onPageChange"
                                            @on-sort-change="onSortChange"
                                            @on-column-filter="onColumnFilter"
                                            @on-per-page-change="onPerPageChange"
                                            mode="remote"
                                            :totalRows="transactions.total"
                                            :pagination-options="{
                                                enabled: true,
                                                perPage: serverParams.perPage,
                                                perPageDropdown: [1, 50, 100, 200],
                                            }"
                                            :columns="dynamicColumns"
                                            :rows="transactions.data"
                                            styleClass="table table-hover"
                            >
                                <template slot="table-row" slot-scope="props">
                                    <span v-if="props.column.field === 'actions'">
                                      <button type="button" class="btn btn-link btn-as-link" v-if="$gate.allow('update', 'transaction', props.row)" @click="showTransactionModal(props.row)">
                                            <i class="fas fa-edit text-green"></i>
                                        </button>
                                        /
                                        <button type="button" class="btn btn-link btn-as-link" v-if="$gate.allow('delete', 'transaction', props.row)" @click="deleteTransaction(props.row)">
                                            <i class="fas fa-trash text-red"></i>
                                        </button>
                                    </span>
                                    <router-link v-if="props.column.field === 'user.name'" :to="`/user/${props.row.user.id}`">
                                        {{ props.row.user.name }}
                                    </router-link>
                                    <span v-else-if="props.column.field === 'sum'" :class="getAmountColorClass(props.row)" >
                                        {{ props.row | transactionAmount }}
                                    </span>
                                    <span v-else>
                                        {{ props.formattedRow[props.column.field] }}
                                    </span>
                                </template>
                                <div slot="emptystate">
                                    No transactions to display
                                </div>
                            </vue-good-table>
                        </div>
                    </div>
                    <!-- /.card-body -->
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
                                    <option v-for="account in settings.currentUser.accounts" :value="account.id">{{ account.name }}</option>
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
    import { VueGoodTable } from 'vue-good-table';

    const FLAG_MODE_ADMIN = 'admin';
    const FLAG_MODE_USER = 'user';

    export default {
        components: {
            VueGoodTable,
        },
        data() {
            return {
                settings: {
                    currentUser: user,
                    viewMode: FLAG_MODE_USER,
                },

                transactions: {},
                transactionTypes: [],
                userAccounts: [],
                users: [],

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

                columns: [
                    {
                        label: 'ID',
                        field: 'id',
                        thClass: 'text-center',
                        tdClass: 'text-center',
                        filterOptions: {
                            enabled: true,
                            filterDropdownItems: [],
                            trigger: 'enter',
                        },
                        width: '10%',
                    },
                    {
                        label: 'User',
                        thClass: 'text-center',
                        field: 'user.name',
                    },
                    {
                        label: 'Date',
                        field: 'date',
                        type: 'date',
                        dateInputFormat: 'YYYY-MM-DD',
                        dateOutputFormat: 'MMMM Do YYYY',
                        thClass: 'text-center',
                        tdClass: 'text-left text-nowrap',
                        filterOptions: {
                            enabled: true,
                            trigger: 'enter',
                        },
                    },
                    {
                        label: 'Account',
                        field: 'account.name',
                        filterOptions: {
                            enabled: true,
                            placeholder: 'Select Account',
                            trigger: 'enter',
                            filterDropdownItems: [],
                        },
                    },
                    {
                        label: 'Sum',
                        field: 'sum',
                        thClass: 'text-center',
                        type: 'decimal',
                        filterOptions: {
                            enabled: true,
                            trigger: 'enter',
                        },
                    },
                    {
                        label: 'Balance Before',
                        field: 'balance_before',
                        type: 'decimal',
                        thClass: 'text-center text-nowrap',
                        filterOptions: {
                            enabled: true,
                            trigger: 'enter',
                        },
                    },
                    {
                        label: 'Balance After',
                        field: 'balance_after',
                        type: 'decimal',
                        thClass: 'text-center text-nowrap',
                        filterOptions: {
                            enabled: true,
                            trigger: 'enter',
                        },
                    },
                    {
                        label: 'Comment',
                        field: 'comment',
                        thClass: 'text-center',
                        filterOptions: {
                            enabled: true,
                            trigger: 'enter',
                        },
                    },
                    {
                        label: 'Actions',
                        field: 'actions',
                        thClass: 'text-center',
                        tdClass: 'text-right',
                    },
                ],
                totalRecords: 0,
                serverParams: {
                    columnFilters: {},
                    sortType: '',
                    sortField: '',
                    page: 1,
                    perPage: 1,
                }
            };
        },
        methods: {
            loadTransactions(page = 1) {
                // Prepare query params
                let queryParams = Object.assign({}, this.serverParams);

                for (let paramName in queryParams) {
                    if (typeof queryParams[paramName] === 'object' && Object.keys(queryParams[paramName]).length > 0) {
                        let parameter = queryParams[paramName];

                        // Remove empty properties rebuild some ones
                        for (var propName in parameter) {
                            let property = parameter[propName];

                            if (property === null || property === undefined || property === '') {
                                delete parameter[propName];
                            }

                            if ('comment' === propName && '' !== property) {
                                parameter[propName] = {
                                    operatorType: 'like',
                                    value: `%${property}%`,
                                };
                            }
                        }

                        // Convert to JSON
                        queryParams[paramName] = JSON.stringify(parameter);
                    }
                }

                if (!this.isAdminMode) {
                    queryParams.userId = this.settings.currentUser.id;
                }

                queryParams = Object.keys(queryParams)
                    .map(k => encodeURIComponent(k) + '=' + encodeURIComponent(queryParams[k]))
                    .join('&')
                ;

                // Send request
                axios.get(`api/transaction?${queryParams}`).then(
                    (response) => {
                        this.transactions = response.data;
                        this.totalRecords = response.data.total;
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
                    this.transactionForm.user_id = this.settings.currentUser.id;
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
                                this.loadTransactions();

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
            getAmountColorClass(transaction) {
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
            },

            // good-table functions
            updateParams(newProps) {
                this.serverParams = Object.assign({}, this.serverParams, newProps);
            },
            onPageChange(params) {
                this.updateParams({page: params.currentPage});
                this.loadTransactions();
            },
            onPerPageChange(params) {
                this.updateParams({perPage: params.currentPerPage});
                this.loadTransactions();
            },
            onSortChange(params) {
                this.updateParams({
                    sortType: params[0].type,
                    sortField: params[0].field,
                });
                this.loadTransactions();
            },
            onColumnFilter(params) {
                if (Object.keys(params.columnFilters).length === 0) {
                    return;
                }

                for (var columnName in params.columnFilters) {
                    let key = columnName;

                    if (columnName === 'account.name') {
                        key = 'account_id';
                    }

                    this.serverParams.columnFilters[key] = params.columnFilters[columnName];
                }

                this.loadTransactions();
            }
        },
        computed: {
            formAction() {
                return this.modal.mode === 'create' ? this.createTransaction : this.updateTransaction;
            },
            isAdminMode() {
                return this.settings.viewMode === FLAG_MODE_ADMIN;
            },
            currentDate() {
                return (new Date()).toISOString().substring(0, 10);
            },
            // Returns simple list of users (object to array)
            getUsersList() {
                return Object.values(this.users);
            },
            // Returns simple list of current user accounts (object to array)
            getAccountsList() {
                let accounts = this.settings.currentUser.accounts;

                return accounts.map((account, index, array) => {
                    return {
                        value: account.id,
                        text: account.name,
                    };
                });
            },
            /**
             * Duct tape to make Vue Good Table columns dynamic. By default it doesn't see changes in column properties
             */
            dynamicColumns() {
                return this.columns.map(column => {
                    let result;

                    switch (column.field) {
                        case 'user.name':
                            result = Object.assign(column, {
                                hidden: !this.isAdminMode,
                                filterOptions: {
                                    enabled: this.isAdminMode,
                                    filterDropdownItems: this.getUsersList,
                                },
                                html: true,
                            });
                            break;

                        case 'account.name':
                            result = Object.assign(column, {
                                filterOptions: {
                                    enabled: true,
                                    filterDropdownItems: this.isAdminMode ? null : this.getAccountsList,
                                },
                            });
                            break;

                        default:
                            result = column;
                    }

                    return result;
                })
            },
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
