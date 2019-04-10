<template>
    <div class="container container-fluid">
        <div class="row mt-5" v-if="$gate.allow('viewAll', 'transactionCategoryType')">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Transaction category types list</h3>
                        <div class="card-tools">
                            <button class="btn btn-success" v-if="$gate.allow('create', 'transactionCategoryType')" @click="showTransactionCategoryTypeModal()">
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
                                    <th class="text-right">Manage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="transactionCategoryType in transactionCategoryTypes">
                                    <td class="w-25">{{ transactionCategoryType.id }}</td>
                                    <td class="w-50">{{ transactionCategoryType.name | capitalize }}</td>
                                    <td class="w-25 text-right">
                                        <button type="button" class="btn btn-link btn-as-link" v-if="$gate.allow('update', 'transactionCategoryType', transactionCategoryType)" @click="showTransactionCategoryTypeModal(transactionCategoryType); return false;">
                                            <i class="fas fa-edit text-green"></i>
                                        </button>
                                        /
                                        <button type="button" class="btn btn-link btn-as-link" v-if="$gate.allow('delete', 'transactionCategoryType', transactionCategoryType)" @click="deleteTransactionCategoryType(transactionCategoryType)">
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
        <div class="modal fade" id="transactionCategoryTypeModal" ref="transactionCategoryTypeModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
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
                                       v-model="transactionCategoryTypeForm.name"
                                       class="form-control"
                                       :class="{'is-invalid': transactionCategoryTypeForm.errors.has('name')}"
                                       placeholder="Name">
                                <has-error :form="transactionCategoryTypeForm" field="name"></has-error>
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
                transactionCategoryTypes: {},
                modal: {
                    target: this.$refs.transactionCategoryTypeModal,
                    mode: 'create',
                    title: 'Add a new transaction category type',
                    buttonTitle: 'Create',
                },
                transactionCategoryTypeForm: new Form({
                    id : null,
                    name: '',
                }),
            };
        },
        methods: {
            loadTransactionCategoryTypes() {
                axios.get(`api/transactionCategoryType`).then(
                    (response) => {
                        this.transactionCategoryTypes = response.data;
                    },
                );
            },
            clearModal() {
                this.transactionCategoryTypeForm.clear();
                this.transactionCategoryTypeForm.reset();
            },
            showTransactionCategoryTypeModal(transactionCategoryType = null) {
                if (transactionCategoryType) {
                    // Set modal params
                    this.modal.mode = 'edit';
                    this.modal.title = 'Edit the transaction category type';
                    this.modal.buttonTitle = 'Save';

                    // Fill form
                    this.transactionCategoryTypeForm.fill(transactionCategoryType);
                } else {
                    // Set modal params
                    this.modal.mode = 'create';
                    this.modal.title = 'Add a new transaction category type';
                    this.modal.buttonTitle = 'Create';
                }

                // Show modal
                $(this.$refs.transactionCategoryTypeModal).modal('show');

                return false;
            },
            createTransactionCategoryType() {
                this.$Progress.start();

                // Add new user
                this.transactionCategoryTypeForm.post('api/transactionCategoryType')
                    .then(response => {
                        this.$Progress.finish();

                        // Refresh the table content
                        this.loadTransactionCategoryTypes();

                        // Close the modal and clean the form
                        $(this.$refs.transactionCategoryTypeModal).modal('hide');

                        // Show success message
                        toast({
                            type: 'success',
                            title: 'Transaction type added successfully'
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
            updateTransactionCategoryType() {
                this.$Progress.start();

                // Add new user
                this.transactionCategoryTypeForm.put(`api/transactionCategoryType/${this.transactionCategoryTypeForm.id}`)
                    .then(response => {
                        this.$Progress.finish();

                        // Refresh the table content
                        this.loadTransactionCategoryTypes();

                        // Close the modal and clean the form
                        $(this.$refs.transactionCategoryTypeModal).modal('hide');

                        // Show success message
                        toast({
                            type: 'success',
                            title: 'Transaction category type updated successfully'
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
            deleteTransactionCategoryType(transactionCategoryType) {
                swal({
                    title: 'Are you sure?',
                    html: `You're going to delete transaction category type "<span class="font-weight-bold">${transactionCategoryType.name}</span>"!`,
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        this.$Progress.start();

                        // Delete
                        axios.delete(`api/transactionCategoryType/${transactionCategoryType.id}`)
                            .then(response => {
                                // Update progress bar
                                this.$Progress.finish();

                                // Update the table
                                this.loadTransactionCategoryTypes();

                                // Show the success message
                                toast({
                                    type: 'success',
                                    title: 'Transaction category type has been deleted'
                                });
                            })
                            .catch(error => {
                                this.$Progress.fail();

                                toast({
                                    type: 'error',
                                    title: 'Server error! Can`t delete the transaction category type.'
                                });
                            })
                        ;
                    }
                })
            },
        },
        computed: {
            formAction() {
                return this.modal.mode == 'create' ? this.createTransactionCategoryType : this.updateTransactionCategoryType;
            },
        },
        created() {
            // Events
            $(document).on("hidden.bs.modal", this.clearModal);

            // Load transaction category types to the table
            this.loadTransactionCategoryTypes();
        },
        mounted() {},
    }
</script>
