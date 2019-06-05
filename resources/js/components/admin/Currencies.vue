<template>
    <div class="container container-fluid">
        <div class="row mt-5" v-if="$gate.allow('viewAll', 'currency')">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Currencies List</h3>
                        <div class="card-tools">
                            <button class="btn btn-success" v-if="$gate.allow('create', 'currency')" @click="showCurrencyModal()">
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
                                    <th>Sign</th>
                                    <th class="text-right">Manage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="currency in currencies">
                                    <td class="w-25">{{ currency.id }}</td>
                                    <td class="w-25">{{ currency.name | capitalize }}</td>
                                    <td class="w-25">{{ currency.sign }}</td>
                                    <td class="w-25 text-right">
                                        <button type="button" class="btn btn-link btn-as-link" v-if="$gate.allow('update', 'currency', currency)" @click="showCurrencyModal(currency); return false;">
                                            <i class="fas fa-edit text-green"></i>
                                        </button>
                                        /
                                        <button type="button" class="btn btn-link btn-as-link" v-if="$gate.allow('delete', 'currency', currency)" @click="deleteCurrency(currency)">
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
        <div class="modal fade" id="currencyModal" ref="currencyModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
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
                                       v-model="currencyForm.name"
                                       class="form-control"
                                       :class="{'is-invalid': currencyForm.errors.has('name')}"
                                       placeholder="Name">
                                <has-error :form="currencyForm" field="name"></has-error>
                            </div>
                            <div class="form-group">
                                <input type="text"
                                       v-model="currencyForm.sign"
                                       class="form-control"
                                       :class="{'is-invalid': currencyForm.errors.has('sign')}"
                                       placeholder="Sign (up to 3 chars)"
                                       pattern=".{1,3}"
                                >
                                <has-error :form="currencyForm" field="sign"></has-error>
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
                currencies: {},
                modal: {
                    target: this.$refs.currencyModal,
                    mode: 'create',
                    title: 'Add a new currency',
                    buttonTitle: 'Create',
                },
                currencyForm: new Form({
                    id : null,
                    name: '',
                    sign: '',
                }),
            };
        },
        methods: {
            loadCurrencies() {
                axios.get(`api/currency`).then(
                    (response) => {
                        this.currencies = response.data;
                    },
                );
            },
            clearModal() {
                this.currencyForm.clear();
                this.currencyForm.reset();
            },
            showCurrencyModal(currency = null) {
                if (currency) {
                    // Set modal params
                    this.modal.mode = 'edit';
                    this.modal.title = 'Edit the currency';
                    this.modal.buttonTitle = 'Save';

                    console.log(currency);
                    // Fill form
                    this.currencyForm.fill(currency);
                } else {
                    // Set modal params
                    this.modal.mode = 'create';
                    this.modal.title = 'Add a new currency';
                    this.modal.buttonTitle = 'Create';
                }

                // Show modal
                $(this.$refs.currencyModal).modal('show');

                return false;
            },
            createCurrency() {
                this.$Progress.start();

                // Add new user
                this.currencyForm.post('api/currency')
                    .then(response => {
                        this.$Progress.finish();

                        // Refresh the table content
                        this.loadCurrencies();

                        // Close the modal and clean the form
                        $(this.$refs.currencyModal).modal('hide');

                        // Show success message
                        toast({
                            type: 'success',
                            title: 'Currency added successfully'
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
            updateCurrency() {
                this.$Progress.start();

                // Add new user
                this.currencyForm.put(`api/currency/${this.currencyForm.id}`)
                    .then(response => {
                        this.$Progress.finish();

                        // Refresh the table content
                        this.loadCurrencies();

                        // Close the modal and clean the form
                        $(this.$refs.currencyModal).modal('hide');

                        // Show success message
                        toast({
                            type: 'success',
                            title: 'Currency updated successfully'
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
            deleteCurrency(currency) {
                swal({
                    title: 'Are you sure?',
                    html: `You're going to delete currency "<span class="font-weight-bold">${currency.name}</span>"!`,
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        this.$Progress.start();

                        // Delete
                        axios.delete(`api/currency/${currency.id}`)
                            .then(response => {
                                // Update progress bar
                                this.$Progress.finish();

                                // Update the table
                                this.loadCurrencies();

                                // Show the success message
                                toast({
                                    type: 'success',
                                    title: 'Currency has been deleted'
                                });
                            })
                            .catch(error => {
                                this.$Progress.fail();

                                toast({
                                    type: 'error',
                                    title: 'Server error! Can`t delete the currency.'
                                });
                            })
                        ;
                    }
                })
            },
        },
        computed: {
            formAction() {
                return this.modal.mode == 'create' ? this.createCurrency : this.updateCurrency;
            },
        },
        created() {
            // Events
            $(document).on("hidden.bs.modal", this.clearModal);

            // Load currencies to the table
            this.loadCurrencies();
        },
        mounted() {},
    }
</script>
