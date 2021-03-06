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
                                    <th>Code</th>
                                    <th>Course</th>
                                    <th>Updated At</th>
                                    <th class="text-right">Manage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="currency in currencies">
                                    <td>{{ currency.id }}</td>
                                    <td class="w-25">{{ currency.name | capitalize }}</td>
                                    <td>{{ currency.sign }}</td>
                                    <td>{{ currency.code }}</td>
                                    <td>{{ currency.course }}</td>
                                    <td class="w-25">{{ currency.course_updated_at | dateMoment('MMMM Do YYYY HH:mm:ss') }}</td>
                                    <td class="text-right">
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
                                       placeholder="Sign"
                                       pattern=".{1,3}"
                                >
                                <has-error :form="currencyForm" field="sign"></has-error>
                            </div>
                            <div class="form-group">
                                <input type="text"
                                       v-model="currencyForm.code"
                                       class="form-control"
                                       :class="{'is-invalid': currencyForm.errors.has('code')}"
                                       placeholder="Code (3 chars)"
                                       pattern="\w{3}"
                                >
                                <has-error :form="currencyForm" field="code"></has-error>
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
                    code: '',
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
                    this.modal.mode = 'edit';
                    this.modal.title = 'Edit the currency';
                    this.modal.buttonTitle = 'Save';

                    this.currencyForm.fill(currency);
                } else {
                    this.modal.mode = 'create';
                    this.modal.title = 'Add a new currency';
                    this.modal.buttonTitle = 'Create';
                }

                $(this.$refs.currencyModal).modal('show');

                return false;
            },
            createCurrency() {
                this.$Progress.start();

                this.currencyForm.post('api/currency')
                    .then(response => {
                        this.$Progress.finish();

                        this.loadCurrencies();

                        $(this.$refs.currencyModal).modal('hide');

                        toast.fire({
                            icon: 'success',
                            title: 'Currency added successfully'
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
            updateCurrency() {
                this.$Progress.start();

                this.currencyForm.put(`api/currency/${this.currencyForm.id}`)
                    .then(response => {
                        this.$Progress.finish();

                        this.loadCurrencies();

                        $(this.$refs.currencyModal).modal('hide');

                        toast.fire({
                            icon: 'success',
                            title: 'Currency updated successfully'
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
            deleteCurrency(currency) {
                swal.fire({
                    title: 'Are you sure?',
                    html: `You're going to delete currency "<span class="font-weight-bold">${currency.name}</span>"!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        this.$Progress.start();

                        axios.delete(`api/currency/${currency.id}`)
                            .then(response => {
                                this.$Progress.finish();

                                this.loadCurrencies();

                                toast.fire({
                                    icon: 'success',
                                    title: 'Currency has been deleted'
                                });
                            })
                            .catch(error => {
                                this.$Progress.fail();

                                toast.fire({
                                    icon: 'error',
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
