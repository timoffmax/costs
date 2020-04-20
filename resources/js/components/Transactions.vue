<template>
    <div class="container-fluid">
        <div class="row control mt-5">
            <div class="col-lg-12">
                <v-md-date-range-picker @change="onDateRangeChange"
                                        :startDate="dateFrom"
                                        :endDate="dateTo"
                >

                </v-md-date-range-picker>
            </div>
        </div>
        <div class="row search-filters mt-5">
            <div class="col-lg-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Active filters</h3>
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
                        <button type="button" class="btn btn-primary mr-2" v-for="filter in searchFiltersToDisplay" v-if="filter.value">
                            <b>{{ filter.label }}:</b> {{ filter.value }}
                            <a href="#" class="badge badge-danger ml-1" @click.prevent="removeFilter(filter.name)">
                                <i class="fas fa-times"></i>
                            </a>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-5" v-if="$gate.allow('viewAll', 'transaction')">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Transactions List</h3>
                        <div class="card-tools">
                            <button class="btn btn-success" v-if="$gate.allow('create', 'transaction')" @click="showTransactionModal()">
                                Add
                                <i class="fas fa-plus fa-fw"></i>
                            </button>
                            <button class="btn btn-primary font-size-4" @click="showColumnsSettingsModal()">
                                <i class="fas fa-cog"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <div>
                            <vue-good-table v-if="transactions"
                                            ref="transactionTable"
                                            @on-page-change="onPageChange"
                                            @on-sort-change="onSortChange"
                                            @on-column-filter="onColumnFilter"
                                            @on-per-page-change="onPerPageChange"
                                            mode="remote"
                                            :totalRows="transactions.total"
                                            :pagination-options="{
                                                enabled: true,
                                                perPage: serverParams.perPage,
                                                perPageDropdown: [10, 20, 50, 100, 200],
                                            }"
                                            :columns="dynamicColumns"
                                            :rows="transactions.data"
                                            styleClass="table table-hover"
                            >
                                <template slot="table-row" slot-scope="props">
                                    <span v-if="props.column.field === 'actions'">
                                        <button type="button" class="btn btn-link btn-as-link" v-if="$gate.allow('view', 'transaction', props.row)">
                                            <router-link :to="`/transaction/${props.row.id}`">
                                                <i class="fas fa-eye text-blue"></i>
                                            </router-link>
                                        </button>
                                        /
                                        <button type="button" class="btn btn-link btn-as-link" v-if="$gate.allow('update', 'transaction', props.row)" @click="showTransactionModal(props.row)">
                                            <i class="fas fa-edit text-green"></i>
                                        </button>
                                        /
                                        <button type="button" class="btn btn-link btn-as-link" v-if="$gate.allow('update', 'transaction', props.row)" @click="copyTransactionModal(props.row)">
                                            <i class="fas fa-copy text-blue"></i>
                                        </button>
                                        /
                                        <button type="button" class="btn btn-link btn-as-link" v-if="$gate.allow('delete', 'transaction', props.row)" @click="deleteTransaction(props.row)">
                                            <i class="fas fa-trash text-red"></i>
                                        </button>
                                    </span>
                                    <router-link v-if="props.column.field === 'user.name'" :to="`/user/${props.row.user.id}`">
                                        {{ props.row.user.name }}
                                    </router-link>
                                    <span v-else-if="props.column.field === 'sum'" :class="getAmountClasses(props.row)" >
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
        <div class="modal fade" id="columnsSettingsModal" ref="columnsSettingsModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Displayed Columns</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div v-for="(column, key) in columns" class="col-12">
                            <label class="toggle">
                                <input @change="switchColumnVisibility(key)" class="toggle__input" type="checkbox" :checked="columnIsVisible(key)">
                                <span class="toggle__label">
                              <span class="toggle__text">{{ column.label }}</span>
                            </span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                            <div class="form-group">
                                <input type="date"
                                       v-model="transactionForm.date"
                                       class="form-control"
                                       :class="{'is-invalid': transactionForm.errors.has('date')}">
                                <has-error :form="transactionForm" field="date"></has-error>
                            </div>
                            <div v-if="isAdminMode" class="form-group">
                                <select v-model="transactionForm.user_id"
                                       class="form-control"
                                       :class="{'is-invalid': transactionForm.errors.has('user_id')}"
                                >
                                    <option value="">Select User</option>
                                    <option v-for="(user, id) in users" :value="id">{{ user | capitalize }}</option>
                                </select>
                                <has-error :form="transactionForm" field="user_id"></has-error>
                            </div>
                            <div class="form-group">
                                <select v-model="transactionForm.type_id"
                                       class="form-control"
                                       :class="{'is-invalid': transactionForm.errors.has('type_id')}"
                                >
                                    <option value="">Select Transaction Type</option>
                                    <option v-for="type in transactionTypes" :value="type.id">
                                        {{ type.label | capitalize }}
                                    </option>
                                </select>
                                <has-error :form="transactionForm" field="type_id"></has-error>
                            </div>
                            <template v-if="transactionTypeIfTransferable">
                                <div class="form-group">
                                    <select v-model="transactionForm.account_from_id"
                                            class="form-control"
                                            :class="{'is-invalid': transactionForm.errors.has('account_from_id')}"
                                    >
                                        <option value="">From Account</option>
                                        <option v-for="account in getAccountDropdownOptions('from')"
                                                :value="account.id"
                                                v-if="account.id !== transactionForm.account_to_id"
                                        >
                                            {{ account.name | capitalize }}
                                        </option>
                                    </select>
                                    <has-error :form="transactionForm" field="account_from_id"></has-error>
                                </div>
                                <div class="form-group">
                                    <select v-model="transactionForm.account_to_id"
                                            class="form-control"
                                            :class="{'is-invalid': transactionForm.errors.has('account_to_id')}"
                                    >
                                        <option value="">To Account</option>
                                        <option v-for="account in getAccountDropdownOptions('to')"
                                                :value="account.id"
                                                v-if="account.id !== transactionForm.account_from_id"
                                        >
                                            {{ account.name | capitalize }}
                                        </option>
                                    </select>
                                    <has-error :form="transactionForm" field="account_to_id"></has-error>
                                </div>
                            </template>
                            <template v-else>
                                <div class="form-group">
                                    <select v-model="transactionForm.account_id"
                                            class="form-control"
                                            :class="{'is-invalid': transactionForm.errors.has('account_id')}"
                                    >
                                        <option value="">Select Account</option>
                                        <option v-for="account in getAccountDropdownOptions()"
                                                :value="account.id">{{ account.name | capitalize }}</option>
                                    </select>
                                    <has-error :form="transactionForm" field="account_id"></has-error>
                                </div>
                            </template>
                            <div v-if="!transactionTypeIfTransferable && !transactionTypeIsIncome" class="form-group">
                                <select v-model="transactionForm.place_id"
                                        class="form-control"
                                        :class="{'is-invalid': transactionForm.errors.has('place_id')}"
                                >
                                    <option value="">No Place</option>
                                    <option v-for="place in settings.currentUser.places" :value="place.id">{{ place.name | capitalize }}</option>
                                </select>
                                <has-error :form="transactionForm" field="place_id"></has-error>
                            </div>
                            <div v-if="!transactionTypeIfTransferable" class="form-group">
                                <select v-model="transactionForm.category_id"
                                        class="form-control"
                                        :class="{'is-invalid': transactionForm.errors.has('category_id')}"
                                >
                                    <option value="">Select Category</option>
                                    <template v-for="category in transactionCategories">
                                        <option v-if="numbersAreEqual(category.transaction_type_id, transactionForm.type_id)" :value="category.id">
                                            {{ category.name | capitalize }}
                                        </option>
                                    </template>
                                </select>
                                <has-error :form="transactionForm" field="category_id"></has-error>
                            </div>
                            <div class="form-group">
                                <input type="text"
                                       v-model="transactionForm.sum"
                                       class="form-control"
                                       :class="{'is-invalid': transactionForm.errors.has('sum')}"
                                       placeholder="Sum"
                                       pattern="\d+(\.\d{1,2})?"
                                >
                                <small class="form-text text-muted">
                                    Use format 123 or 123.45
                                </small>
                                <has-error :form="transactionForm" field="sum"></has-error>
                            </div>
                            <div v-if="transferWithExchange" class="form-group">
                                <input type="text"
                                       v-model="transactionForm.exchange_course"
                                       class="form-control"
                                       :class="{'is-invalid': transactionForm.errors.has('exchange_course')}"
                                       placeholder="Exchange course"
                                       pattern="\d+(\.\d{1,2})?"
                                >
                                <small class="form-text text-muted">
                                    Use format 123 or 123.45
                                </small>
                                <has-error :form="transactionForm" field="exchange_course"></has-error>
                            </div>
                            <div v-if="transactionTypeIfTransferable" class="form-group">
                                <input type="text"
                                       v-model="transactionForm.fee"
                                       class="form-control"
                                       :class="{'is-invalid': transactionForm.errors.has('fee')}"
                                       placeholder="Fee"
                                       pattern="\d+(\.\d{1,2})?"
                                >
                                <small class="form-text text-muted">
                                    Use format 123 or 123.45
                                </small>
                                <has-error :form="transactionForm" field="fee"></has-error>
                            </div>
                            <div v-if="!transactionTypeIfTransferable" class="form-group">
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
                            <button type="button" @click="createTransaction(true)" class="btn btn-outline-dark">Save And Continue</button>
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

    const LS_KEY_COLUMNS = 'transactionsTable.columns';

    export default {
        components: {
            VueGoodTable,
        },
        props: ['filters'],
        data() {
            return {
                dateFrom: null,
                dateTo: null,

                settings: {
                    currentUser: user,
                    viewMode: FLAG_MODE_USER,
                    columnNameAliases: {
                        account_id: 'account.name',
                        category_id: 'category.name',
                        place_id: 'place.name',
                    },
                    activeColumns: {},
                },

                transactions: {},
                transactionTypes: [],
                transactionCategories: [],
                userAccounts: [],
                users: [],
                idNameMap: {
                    category_id: [],
                    account_id: [],
                    place_id: [],
                },

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
                    category_id: null,
                    account_id: null,
                    account_from_id: null,
                    exchange_course: null,
                    account_to_id: null,
                    place_id: null,
                    sum: null,
                    fee: null,
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
                        dateInputFormat: 'yyyy-MM-dd HH:mm:ss',
                        dateOutputFormat: 'MMMM do yyyy',
                        thClass: 'text-center',
                        tdClass: 'text-center text-nowrap',
                        filterOptions: {
                            enabled: false,
                            trigger: 'enter',
                        },
                    },
                    {
                        label: 'Category',
                        field: 'category.name',
                        thClass: 'text-center',
                        tdClass: 'text-left text-nowrap text-capitalize',
                    },
                    {
                        label: 'Account',
                        field: 'account.name',
                        thClass: 'text-center',
                        tdClass: 'text-center text-nowrap',
                    },
                    {
                        label: 'Place',
                        field: 'place.name',
                        thClass: 'text-center',
                        tdClass: 'text-left text-nowrap',
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
                        hidden: true,
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
                        hidden: true,
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
                        tdClass: 'text-right text-nowrap',
                    },
                ],
                totalRecords: 0,
                serverParams: {
                    columnFilters: {},
                    sortType: '',
                    sortField: '',
                    sort: 'dateIdDesc',
                    page: 1,
                    perPage: 50,
                }
            };
        },
        methods: {
            loadUserData() {
                // Update current user data
                axios.get(`api/user/${window.user.id}`).then(
                    (response) => {
                        this.settings.currentUser = response.data;
                        this.updateIdNameMap(this.settings.currentUser.accounts, 'account_id');
                        this.updateIdNameMap(this.settings.currentUser.places, 'place_id');
                    },
                );
            },
            loadTransactions() {
                let queryString = this.prepareQueryString();

                // Send request
                axios.get(`api/transaction?${queryString}`).then(
                    (response) => {
                        this.transactions = response.data;
                        this.totalRecords = response.data.total;
                    },
                );
            },
            prepareQueryString() {
                let queryParams = Object.assign({}, this.serverParams, this.searchFilters);

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

                        queryParams[paramName] = JSON.stringify(parameter);
                    }
                }

                if (!this.isAdminMode) {
                    queryParams.userId = this.settings.currentUser.id;
                }

                let result = Object.keys(queryParams)
                    .map(k => encodeURIComponent(k) + '=' + encodeURIComponent(queryParams[k]))
                    .join('&')
                ;

                return result;
            },
            loadTransactionTypes() {
                axios.get(`api/transactionType`).then(
                    (response) => {
                        let types = {};

                        for (let type of response.data) {
                            types[type.id] = type;
                        }

                        this.transactionTypes = types;
                    },
                );
            },
            loadCategories() {
                axios.get(`api/transactionCategory`).then(
                    (response) => {
                        this.transactionCategories = response.data;
                        this.updateIdNameMap(this.transactionCategories, 'category_id');
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
            updateIdNameMap(objectsArray, key) {
                for (let item of objectsArray) {
                    this.idNameMap[key][item.id] = item.name;
                }
            },
            getMappedValueById(key, id) {
                let result = null;
                let mapValues = this.idNameMap[key];

                if (typeof mapValues !== 'undefined') {
                    result = mapValues[id] ? mapValues[id] : null;
                }

                return result;
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
                    this.transactionForm.sum = null;
                    this.transactionForm.date = this.currentDate;
                }

                // Show modal
                $(this.$refs.transactionModal).modal('show');
            },
            copyTransactionModal(transaction) {
                // Set modal params
                this.modal.mode = 'create';
                this.modal.title = 'Copy the transaction';
                this.modal.buttonTitle = 'Copy';

                // Fill form
                this.transactionForm.fill(transaction);
                this.transactionForm.user_id = this.settings.currentUser.id;
                this.transactionForm.date = this.transactionForm.date.substr(0, 10);

                // Show modal
                $(this.$refs.transactionModal).modal('show');
            },
            showColumnsSettingsModal() {
                // Show modal
                $(this.$refs.columnsSettingsModal).modal('show');
            },
            createTransaction(andContinue = false) {
                this.$Progress.start();

                // Add new user
                this.transactionForm.post('api/transaction')
                    .then(response => {
                        this.$Progress.finish();

                        this.loadTransactions();

                        if (true === andContinue) {
                            let formData = this.transactionForm;
                            this.showModalWithData(formData, 1000);
                        }

                        $(this.$refs.transactionModal).modal('hide');

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
            showModalWithData(formData, delay = 500) {
                let date = formData.date;
                let typeId = formData.type_id;
                let accountId = formData.account_id;

                setTimeout(() => {
                    this.showTransactionModal();

                    this.transactionForm.date = date;
                    this.transactionForm.type_id = typeId;
                    this.transactionForm.account_id = accountId;
                }, delay);
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
            removeFilter(filterName) {
                var field = this.getColumnKey(filterName);
                let foundIndex = this.columns.findIndex((column) => {
                    return column.field === field;
                });

                this.$delete(this.serverParams.columnFilters, filterName);

                if (foundIndex > 0) {
                    this.$set(this.columns[foundIndex].filterOptions, 'filterValue', 0);
                }

                if (typeof this.filters !== 'undefined') {
                    this.$delete(this.filters, filterName);
                }

                this.$refs.transactionTable.$emit('on-column-filter', {
                    columnFilters: this.serverParams.columnFilters
                });
            },
            getAmountClasses(transaction) {
                let colorClass = 'text-';
                let additionalClasses = 'text-bold';

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

                return [colorClass, additionalClasses].join(' ');
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
                if (params.currentPerPage === this.serverParams.perPage) {
                    return;
                }

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

                let columnFilters = {};

                for (let columnName in params.columnFilters) {
                    let key = columnName;

                    // Use real column name IDs instead of aliases (such as 'account.name' etc.)
                    for (let realColumnName in this.settings.columnNameAliases) {
                        if (this.settings.columnNameAliases[realColumnName] === key) {
                            key = realColumnName;
                        }
                    }

                    columnFilters[key] = params.columnFilters[columnName];
                }

                this.updateParams({columnFilters: columnFilters});
                this.loadTransactions();
            },
            getColumnName(key) {
                let result = key;

                for (let realColumnName in this.settings.columnNameAliases) {
                    if (this.settings.columnNameAliases[realColumnName] === key) {
                        result = realColumnName;
                    }
                }

                return result;
            },
            getColumnKey(columnName) {
                let result = columnName;

                for (let realColumnName in this.settings.columnNameAliases) {
                    if (realColumnName === columnName) {
                        result = this.settings.columnNameAliases[realColumnName];
                    }
                }

                return result;
            },
            getColumnLabel(columnName) {
                let columnKey = this.getColumnKey(columnName);
                let result = columnKey;

                for (let column of this.columns) {
                    if (column.field === columnKey) {
                        result = column.label;
                    }
                }

                return result;
            },
            onDateRangeChange(momentObjects, datesArray) {
                if (Array.isArray(datesArray)) {
                    this.dateFrom = datesArray[0];
                    this.dateTo = datesArray[1];
                }

                this.loadTransactions();
            },
            switchColumnVisibility(columnKey, value = null) {
                let column = this.columns[columnKey];
                let newValue = null !== value ? !value : !column.hidden;

                this.$set(this.columns[columnKey], 'hidden', newValue);
                this.setColumnVisibilitySetting(column.field, !newValue);
            },
            setColumnVisibilitySetting(columnName, value) {
                let activeColumns = this.settings.activeColumns;
                activeColumns[columnName] = value;
                localStorage.setItem(LS_KEY_COLUMNS, JSON.stringify(activeColumns));
            },
            columnIsVisible(columnKey) {
                let column = this.columns[columnKey];
                return !column.hidden;
            },
            // Helpers
            numbersAreEqual(value1, value2) {
                return Number(value1) === Number (value2);
            },
            applyLocalStorageSettings() {
                // Get columns settings or fill them
                let activeColumns = this.settings.activeColumns;
                let storageSettings = localStorage.getItem(LS_KEY_COLUMNS);

                try {
                    storageSettings = JSON.parse(storageSettings);
                } catch (e) {
                    storageSettings = null;
                }

                if (typeof storageSettings !== 'object') {
                    localStorage.removeItem(LS_KEY_COLUMNS);
                }

                if (storageSettings) {
                    activeColumns = Object.assign(storageSettings);
                    this.settings.activeColumns = activeColumns;
                } else {
                    for (let columnId in this.columns) {
                        let column = this.columns[columnId];
                        this.setColumnVisibilitySetting(column.field, !column.hidden)
                    }
                }

                // Apply settings
                for (let columnId in this.columns) {
                    let column = this.columns[columnId];
                    this.$set(column, 'hidden', !activeColumns[column.field]);
                }
            },
            getAccountById(accountId) {
                let accounts = this.settings.currentUser.accounts;

                for (let account of accounts) {
                    if (account.id === accountId) {
                        return account;
                    }
                }

                return null;
            },
            getAccountDropdownOptions(type = 'from') {
                let result = [];

                if (!this.transactionForm.type_id) {
                    return result;
                }

                let selectedTypeId = this.transactionForm.type_id;
                let selectedTypeName = this.transactionTypes[selectedTypeId].name;

                for (let account of this.settings.currentUser.accounts) {
                    let isCasualAccount = account.type.name === 'cash' || account.type.name === 'card';
                    let isDepositAccount = account.type.name === 'deposit';

                    switch (selectedTypeName) {
                        case 'cost':
                            if (isCasualAccount) {
                                result.push(account);
                            }
                            break;

                        case 'income':
                            if (isCasualAccount || isDepositAccount) {
                                result.push(account);
                            }
                            break;

                        case 'transfer':
                            result.push(account);
                            break;

                        default:
                            if ('from' === type && isCasualAccount) {
                                result.push(account);
                            } else if ('to' === type && account.type.name === selectedTypeName) {
                                result.push(account);
                            }
                            break;
                    }
                }

                return result;
            },
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

                if (accounts) {
                    return accounts.map((account, index, array) => {
                        return {
                            value: account.id,
                            text: this.$options.filters.capitalize(account.name),
                        };
                    });
                }
            },
            // Returns simple list of current user places
            getPlacesList() {
                let places = this.settings.currentUser.places;
                if (places) {
                    return places.map((place, index, array) => {
                        return {
                            value: place.id,
                            text: this.$options.filters.capitalize(place.name),
                        };
                    });
                }
            },
            // Returns simple list of transaction categories
            getCategoriesList() {
                return this.transactionCategories.map((category, index, array) => {
                    return {
                        value: category.id,
                        text: this.$options.filters.capitalize(category.name),
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
                                    placeholder: 'All',
                                    filterDropdownItems: this.getUsersList,
                                },
                                html: true,
                            });
                            break;

                        case 'account.name':
                            result = Object.assign(column, {
                                filterOptions: {
                                    enabled: true,
                                    placeholder: 'All',
                                    filterDropdownItems: this.isAdminMode ? null : this.getAccountsList,
                                },
                            });
                            break;

                        case 'place.name':
                            result = Object.assign(column, {
                                filterOptions: {
                                    enabled: true,
                                    placeholder: 'All',
                                    filterDropdownItems: this.isAdminMode ? null : this.getPlacesList,
                                },
                            });
                            break;

                        case 'category.name':
                            result = Object.assign(column, {
                                filterOptions: {
                                    enabled: true,
                                    placeholder: 'All',
                                    filterDropdownItems: this.getCategoriesList,
                                },
                            });
                            break;

                        default:
                            result = column;
                    }

                    return result;
                })
            },
            transactionTypeIfTransferable() {
                let result = false;

                result = result || this.transactionTypeIsTransfer;
                result = result || this.transactionTypeIsDeposit;
                result = result || this.transactionTypeIsMoneybox;
                result = result || this.transactionTypeIsSaving;

                return result;
            },
            transactionTypeIsTransfer() {
                for (let type in this.transactionTypes) {
                    if (this.transactionTypes[type].id === this.transactionForm.type_id) {
                        return this.transactionTypes[type].name === 'transfer';
                    }
                }

                return false;
            },
            transactionTypeIsIncome() {
                for (let type in this.transactionTypes) {
                    if (this.transactionTypes[type].id === this.transactionForm.type_id) {
                        return this.transactionTypes[type].name === 'income';
                    }
                }

                return false;
            },
            transactionTypeIsDeposit() {
                for (let type in this.transactionTypes) {
                    if (this.transactionTypes[type].id === this.transactionForm.type_id) {
                        return this.transactionTypes[type].name === 'deposit';
                    }
                }

                return false;
            },
            transactionTypeIsMoneybox() {
                for (let type in this.transactionTypes) {
                    if (this.transactionTypes[type].id === this.transactionForm.type_id) {
                        return this.transactionTypes[type].name === 'moneybox';
                    }
                }

                return false;
            },
            transactionTypeIsSaving() {
                for (let type in this.transactionTypes) {
                    if (this.transactionTypes[type].id === this.transactionForm.type_id) {
                        return this.transactionTypes[type].name === 'saving';
                    }
                }

                return false;
            },
            transferWithExchange() {
                let isTransfer = this.transactionTypeIsTransfer;
                let accountFrom = this.getAccountById(this.transactionForm.account_from_id);
                let accountTo = this.getAccountById(this.transactionForm.account_to_id);
                let result = false;

                if (!isTransfer || !accountFrom || !accountTo) {
                    return false;
                }

                if (accountFrom.currency_id !== accountTo.currency_id) {
                    result = true;
                }

                return result;
            },
            searchFilters() {
                let result = Object.assign({}, this.serverParams.columnFilters);

                if (typeof this.filters !== 'undefined') {
                    result = Object.assign(result, this.filters);
                }

                if (!result.date) {
                    if (null === this.dateFrom && null === this.dateTo) {
                        this.dateFrom = moment().startOf('month').format('YYYY-MM-DD');
                        this.dateTo = moment().endOf('month').format('YYYY-MM-DD');
                    }

                    result.date = [this.dateFrom, this.dateTo];
                } else if (2 === queryParams.date.length) {
                    this.dateFrom = queryParams.date[0];
                    this.dateTo = queryParams.date[1];
                } else if (queryParams.date.length) {
                    this.dateFrom = queryParams.date;
                    this.dateTo = queryParams.date;
                }

                return result;
            },
            searchFiltersToDisplay() {
                let result = [];

                if (!this.searchFilters) {
                    return;
                }

                for (let columnName in this.searchFilters) {
                    let displayName = this.getColumnLabel(columnName);
                    let filterValue = this.searchFilters[columnName];

                    let filter = {};
                    filter.label = displayName;
                    filter.name = columnName;

                    if (Array.isArray(filterValue) && filterValue.length === 2) {
                        filter.value = `from ${filterValue[0]} to ${filterValue[1]}`;
                    } else {
                        filterValue = this.searchFilters[columnName];
                        let mappedValue = this.getMappedValueById(columnName, filterValue)
                        filter.value = mappedValue ? mappedValue : filterValue;
                    }

                    result.push(filter);
                }

                return result;
            },
        },
        created() {
            // Events
            $(document).on("hidden.bs.modal", this.clearModal);

            // Load transactions to the table
            this.loadUserData();
            this.loadTransactions();
            this.loadTransactionTypes();
            this.loadCategories();

            if (this.isAdminMode) {
                this.loadUsers();
            }
        },
        mounted() {
            this.applyLocalStorageSettings();
        },
    }
</script>

<style scoped>
    /*
    =====
    LEVEL 1. CORE STYLES
    =====
    */

    .toggle{
        --uiToggleSize: var(--toggleSize, 20px);
        --uiToggleIndent: var(--toggleIndent, .4em);
        --uiToggleBorderWidth: var(--toggleBorderWidth, 2px);
        --uiToggleColor: var(--toggleColor, #000);
        --uiToggleDisabledColor: var(--toggleDisabledColor, #868e96);
        --uiToggleBgColor: var(--toggleBgColor, #fff);
        --uiToggleArrowWidth: var(--toggleArrowWidth, 2px);
        --uiToggleArrowColor: var(--toggleArrowColor, #fff);

        display: inline-block;
        position: relative;
    }

    .toggle__input{
        position: absolute;
        left: -99999px;
    }

    .toggle__label{
        display: inline-flex;
        cursor: pointer;
        min-height: var(--uiToggleSize);
        padding-left: calc(var(--uiToggleSize) + var(--uiToggleIndent));
    }

    .toggle__label:before, .toggle__label:after{
        content: "";
        box-sizing: border-box;
        width: 1em;
        height: 1em;
        font-size: var(--uiToggleSize);

        position: absolute;
        left: 0;
        top: 0;
    }

    .toggle__label:before{
        border: var(--uiToggleBorderWidth) solid var(--uiToggleColor);
        z-index: 2;
    }

    .toggle__input:disabled ~ .toggle__label:before{
        border-color: var(--uiToggleDisabledColor);
    }

    .toggle__input:focus ~ .toggle__label:before{
        box-shadow: 0 0 0 2px var(--uiToggleBgColor), 0 0 0px 4px var(--uiToggleColor);
    }

    .toggle__input:not(:disabled):checked:focus ~ .toggle__label:after{
        box-shadow: 0 0 0 2px var(--uiToggleBgColor), 0 0 0px 4px var(--uiToggleColor);
    }

    .toggle__input:not(:disabled) ~ .toggle__label:after{
        background-color: var(--uiToggleColor);
        opacity: 0;
    }

    .toggle__input:not(:disabled):checked ~ .toggle__label:after{
        opacity: 1;
    }

    .toggle__text{
        margin-top: auto;
        margin-bottom: auto;
    }

    /*
    The arrow size and position depends from sizes of square because I needed an arrow correct positioning from the top left corner of the element toggle
    */

    .toggle__text:before{
        content: "";
        box-sizing: border-box;
        width: 0;
        height: 0;
        font-size: var(--uiToggleSize);

        border-left-width: 0;
        border-bottom-width: 0;
        border-left-style: solid;
        border-bottom-style: solid;
        border-color: var(--uiToggleArrowColor);

        position: absolute;
        top: .5428em;
        left: .2em;
        z-index: 3;

        transform-origin: left top;
        transform: rotate(-40deg) skew(10deg);
    }

    .toggle__input:not(:disabled):checked ~ .toggle__label .toggle__text:before{
        width: .5em;
        height: .25em;
        border-left-width: var(--uiToggleArrowWidth);
        border-bottom-width: var(--uiToggleArrowWidth);
        will-change: width, height;
        transition: width .1s ease-out .2s, height .2s ease-out;
    }

    /*
    =====
    LEVEL 2. PRESENTATION STYLES
    =====
    */

    /*
    The demo skin
    */

    .toggle__label:before, .toggle__label:after{
        border-radius: 2px;
    }

    /*
    The animation of switching states
    */

    .toggle__input:not(:disabled) ~ .toggle__label:before,
    .toggle__input:not(:disabled) ~ .toggle__label:after{
        opacity: 1;
        transform-origin: center center;
        will-change: transform;
        transition: transform .2s ease-out;
    }

    .toggle__input:not(:disabled) ~ .toggle__label:before{
        transform: rotateY(0deg);
        transition-delay: .2s;
    }

    .toggle__input:not(:disabled) ~ .toggle__label:after{
        transform: rotateY(90deg);
    }

    .toggle__input:not(:disabled):checked ~ .toggle__label:before{
        transform: rotateY(-90deg);
        transition-delay: 0s;
    }

    .toggle__input:not(:disabled):checked ~ .toggle__label:after{
        transform: rotateY(0deg);
        transition-delay: .2s;
    }

    .toggle__text:before{
        opacity: 0;
    }

    .toggle__input:not(:disabled):checked ~ .toggle__label .toggle__text:before{
        opacity: 1;
        transition: opacity .1s ease-out .3s, width .1s ease-out .5s, height .2s ease-out .3s;
    }

    /*
    =====
    LEVEL 3. SETTINGS
    =====
    */

    .toggle{
        --toggleColor: #164690;
        --toggleBgColor: #5866b6;
        /*--toggleSize: 50px;*/
    }
</style>
