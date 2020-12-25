<template>
    <div class="container container-fluid">
        <div class="row mt-5" v-if="$gate.allow('viewAll', 'transactionCategory')">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Transaction categories list</h3>
                        <div class="card-tools">
                            <button class="btn btn-success" v-if="$gate.allow('create', 'transactionCategory')" @click="showTransactionCategoryModal()">
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
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Transaction Type</th>
                                    <th class="text-right">Manage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="transactionCategory in transactionCategories">
                                    <td class="w-15">{{ transactionCategory.id }}</td>
                                    <td class="w-20">{{ transactionCategory.name | capitalize }}</td>
                                    <td class="w-20">{{ transactionCategory.type.name | capitalize }}</td>
                                    <td class="w-20" :class="getTransactionTypeClasses(transactionCategory.transaction_type)">
                                        {{ transactionCategory.transaction_type.name | capitalize }}
                                    </td>
                                    <td class="w-25 text-right">
                                        <button type="button" class="btn btn-link btn-as-link" v-if="$gate.allow('update', 'transactionCategory', transactionCategory)" @click="showTransactionCategoryModal(transactionCategory); return false;">
                                            <i class="fas fa-edit text-green"></i>
                                        </button>
                                        /
                                        <button type="button" class="btn btn-link btn-as-link" v-if="$gate.allow('delete', 'transactionCategory', transactionCategory)" @click="deleteTransactionCategory(transactionCategory)">
                                            <i class="fas fa-trash text-red"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer"></div>
                </div>
                <!-- /.card -->
            </div>
        </div>

        <div v-else>
            <forbidden-page />
        </div>

        <!--modal-->
        <div class="modal fade" id="transactionCategoryModal" ref="transactionCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
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
                                       v-model="transactionCategoryForm.name"
                                       class="form-control"
                                       :class="{'is-invalid': transactionCategoryForm.errors.has('name')}"
                                       placeholder="Name">
                                <has-error :form="transactionCategoryForm" field="name"></has-error>
                            </div>
                            <div class="form-group">
                                <select v-model="transactionCategoryForm.transaction_type_id"
                                        class="form-control"
                                        :class="{'is-invalid': transactionCategoryForm.errors.has('transaction_type_id')}"
                                >
                                    <option value="">Select Transaction Type</option>
                                    <option v-for="(type, id) in transactionTypes" :value="id">{{ type | capitalize }}</option>
                                </select>
                                <has-error :form="transactionCategoryForm" field="transaction_type_id"></has-error>
                            </div>
                            <div class="form-group">
                                <select v-model="transactionCategoryForm.type_id"
                                        class="form-control"
                                        :class="{'is-invalid': transactionCategoryForm.errors.has('type_id')}"
                                >
                                    <option value="">Select Category Type</option>
                                    <option v-for="(type, id) in transactionCategoryTypes" :value="id">{{ type | capitalize  }}</option>
                                </select>
                                <has-error :form="transactionCategoryForm" field="type_id"></has-error>
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
                transactionCategories: {},
                transactionTypes: [],
                transactionCategoryTypes: [],
                modal: {
                    target: this.$refs.transactionCategoryModal,
                    mode: 'create',
                    title: 'Add a new transaction category',
                    buttonTitle: 'Create',
                },
                transactionCategoryForm: new Form({
                    id : null,
                    type_id : null,
                    transaction_type_id : null,
                    name: '',
                }),
            };
        },
        methods: {
            loadTransactionCategories() {
                axios.get(`api/transactionCategory`).then(
                    (response) => {
                        this.transactionCategories = response.data;
                    },
                );
            },
            loadTransactionCategoryTypes() {
                axios.get(`api/transactionCategoryType?mode=simple`).then(
                    (response) => {
                        this.transactionCategoryTypes = response.data;
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
            clearModal() {
                this.transactionCategoryForm.clear();
                this.transactionCategoryForm.reset();
            },
            showTransactionCategoryModal(transactionCategory = null) {
                this.loadTransactionCategoryTypes();
                this.loadTransactionTypes();

                if (transactionCategory) {
                    this.modal.mode = 'edit';
                    this.modal.title = 'Edit the transaction category';
                    this.modal.buttonTitle = 'Save';

                    this.transactionCategoryForm.fill(transactionCategory);
                } else {
                    this.modal.mode = 'create';
                    this.modal.title = 'Add a new transaction category';
                    this.modal.buttonTitle = 'Create';
                }

                $(this.$refs.transactionCategoryModal).modal('show');

                return false;
            },
            getTransactionTypeClasses(transactionType) {
                let colorClass = 'text-';
                let additionalClasses = 'text-bold';

                switch (transactionType.name) {
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
            createTransactionCategory() {
                this.$Progress.start();

                this.transactionCategoryForm.post('api/transactionCategory')
                    .then(response => {
                        this.$Progress.finish();

                        this.loadTransactionCategories();

                        $(this.$refs.transactionCategoryModal).modal('hide');

                        toast.fire({
                            icon: 'success',
                            title: 'Transaction category added successfully'
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
            updateTransactionCategory() {
                this.$Progress.start();

                this.transactionCategoryForm.put(`api/transactionCategory/${this.transactionCategoryForm.id}`)
                    .then(response => {
                        this.$Progress.finish();

                        this.loadTransactionCategories();

                        $(this.$refs.transactionCategoryModal).modal('hide');

                        toast.fire({
                            icon: 'success',
                            title: 'Transaction category updated successfully'
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
            deleteTransactionCategory(transactionCategory) {
                swal.fire({
                    title: 'Are you sure?',
                    html: `You're going to delete transaction category "<span class="font-weight-bold">${transactionCategory.name}</span>"!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        this.$Progress.start();

                        axios.delete(`api/transactionCategory/${transactionCategory.id}`)
                            .then(response => {
                                this.$Progress.finish();

                                this.loadTransactionCategories();

                                toast.fire({
                                    icon: 'success',
                                    title: 'Transaction category has been deleted'
                                });
                            })
                            .catch(error => {
                                this.$Progress.fail();

                                toast.fire({
                                    icon: 'error',
                                    title: 'Server error! Can`t delete the transaction category.'
                                });
                            })
                        ;
                    }
                })
            },
        },
        computed: {
            formAction() {
                return this.modal.mode == 'create' ? this.createTransactionCategory : this.updateTransactionCategory;
            },
        },
        created() {
            // Events
            $(document).on("hidden.bs.modal", this.clearModal);

            // Load transaction categories to the table
            this.loadTransactionCategories();
            this.loadTransactionCategoryTypes();
            this.loadTransactionTypes();
        },
        mounted() {},
    }
</script>
