<template>
    <div class="container container-fluid">
        <div class="row mt-5" v-if="$gate.allow('viewAll', 'transactionType')">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Transaction types list</h3>
                        <div class="card-tools">
                            <button class="btn btn-success" v-if="$gate.allow('create', 'transactionType')" @click="showTransactionTypeModal()">
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
                                <tr v-for="transactionType in transactionTypes">
                                    <td class="w-25">{{ transactionType.id }}</td>
                                    <td class="w-50">{{ transactionType.name | capitalize }}</td>
                                    <td class="w-25 text-right">
                                        <button type="button" class="btn btn-link btn-as-link" v-if="$gate.allow('update', 'transactionType', transactionType)" @click="showTransactionTypeModal(transactionType); return false;">
                                            <i class="fas fa-edit text-green"></i>
                                        </button>
                                        /
                                        <button type="button" class="btn btn-link btn-as-link" v-if="$gate.allow('delete', 'transactionType', transactionType)" @click="deleteTransactionType(transactionType)">
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
        <div class="modal fade" id="transactionTypeModal" ref="transactionTypeModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
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
                                       v-model="transactionTypeForm.name"
                                       class="form-control"
                                       :class="{'is-invalid': transactionTypeForm.errors.has('name')}"
                                       placeholder="Name">
                                <has-error :form="transactionTypeForm" field="name"></has-error>
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
                transactionTypes: {},
                modal: {
                    target: this.$refs.transactionTypeModal,
                    mode: 'create',
                    title: 'Add a new transaction type',
                    buttonTitle: 'Create',
                },
                transactionTypeForm: new Form({
                    id : null,
                    name: '',
                }),
            };
        },
        methods: {
            loadTransactionTypes() {
                axios.get(`api/transactionType`).then(
                    (response) => {
                        this.transactionTypes = response.data;
                    },
                );
            },
            clearModal() {
                this.transactionTypeForm.clear();
                this.transactionTypeForm.reset();
            },
            showTransactionTypeModal(transactionType = null) {
                if (transactionType) {
                    // Set modal params
                    this.modal.mode = 'edit';
                    this.modal.title = 'Edit the transaction type';
                    this.modal.buttonTitle = 'Save';

                    // Fill form
                    this.transactionTypeForm.fill(transactionType);
                } else {
                    // Set modal params
                    this.modal.mode = 'create';
                    this.modal.title = 'Add a new transaction type';
                    this.modal.buttonTitle = 'Create';
                }

                // Show modal
                $(this.$refs.transactionTypeModal).modal('show');

                return false;
            },
            createTransactionType() {
                this.$Progress.start();

                // Add new user
                this.transactionTypeForm.post('api/transactionType')
                    .then(response => {
                        this.$Progress.finish();

                        // Refresh the table content
                        this.loadTransactionTypes();

                        // Close the modal and clean the form
                        $(this.$refs.transactionTypeModal).modal('hide');

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
            updateTransactionType() {
                this.$Progress.start();

                // Add new user
                this.transactionTypeForm.put(`api/transactionType/${this.transactionTypeForm.id}`)
                    .then(response => {
                        this.$Progress.finish();

                        // Refresh the table content
                        this.loadTransactionTypes();

                        // Close the modal and clean the form
                        $(this.$refs.transactionTypeModal).modal('hide');

                        // Show success message
                        toast({
                            type: 'success',
                            title: 'Transaction type updated successfully'
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
            deleteTransactionType(transactionType) {
                swal({
                    title: 'Are you sure?',
                    html: `You're going to delete transaction type "<span class="font-weight-bold">${transactionType.name}</span>"!`,
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        this.$Progress.start();

                        // Delete
                        axios.delete(`api/transactionType/${transactionType.id}`)
                            .then(response => {
                                // Update progress bar
                                this.$Progress.finish();

                                // Update the table
                                this.loadTransactionTypes();

                                // Show the success message
                                toast({
                                    type: 'success',
                                    title: 'Transaction type has been deleted'
                                });
                            })
                            .catch(error => {
                                this.$Progress.fail();

                                toast({
                                    type: 'error',
                                    title: 'Server error! Can`t delete the transaction type.'
                                });
                            })
                        ;
                    }
                })
            },
        },
        computed: {
            formAction() {
                return this.modal.mode == 'create' ? this.createTransactionType : this.updateTransactionType;
            },
        },
        created() {
            // Events
            $(document).on("hidden.bs.modal", this.clearModal);

            // Load transaction types to the table
            this.loadTransactionTypes();
        },
        mounted() {},
    }
</script>
