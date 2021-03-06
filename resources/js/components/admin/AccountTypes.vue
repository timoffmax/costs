<template>
    <div class="container container-fluid">
        <div class="row mt-5" v-if="$gate.allow('viewAll', 'accountType')">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Account types List</h3>
                        <div class="card-tools">
                            <button class="btn btn-success" v-if="$gate.allow('create', 'accountType')" @click="showAccountTypeModal()">
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
                                <tr v-for="accountType in accountTypes">
                                    <td class="w-25">{{ accountType.id }}</td>
                                    <td class="w-50">{{ accountType.name | capitalize }}</td>
                                    <td class="w-25 text-right">
                                        <button type="button" class="btn btn-link btn-as-link" v-if="$gate.allow('update', 'accountType', accountType)" @click="showAccountTypeModal(accountType); return false;">
                                            <i class="fas fa-edit text-green"></i>
                                        </button>
                                        /
                                        <button type="button" class="btn btn-link btn-as-link" v-if="$gate.allow('delete', 'accountType', accountType)" @click="deleteAccountType(accountType)">
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
        <div class="modal fade" id="accountTypeModal" ref="accountTypeModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
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
                                       v-model="accountTypeForm.name"
                                       class="form-control"
                                       :class="{'is-invalid': accountTypeForm.errors.has('name')}"
                                       placeholder="Name">
                                <has-error :form="accountTypeForm" field="name"></has-error>
                            </div>
                            <div class="form-group">
                                <input type="text"
                                       v-model="accountTypeForm.label"
                                       class="form-control"
                                       :class="{'is-invalid': accountTypeForm.errors.has('label')}"
                                       placeholder="Label">
                                <has-error :form="accountTypeForm" field="label"></has-error>
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
    import swal from "sweetalert2";

    export default {
        data() {
            return {
                accountTypes: {},
                modal: {
                    target: this.$refs.accountTypeModal,
                    mode: 'create',
                    title: 'Add a new account type',
                    buttonTitle: 'Create',
                },
                accountTypeForm: new Form({
                    id : null,
                    name: '',
                    label: '',
                }),
            };
        },
        methods: {
            loadAccountTypes() {
                axios.get(`api/accountType`).then(
                    (response) => {
                        this.accountTypes = response.data;
                    },
                );
            },
            clearModal() {
                this.accountTypeForm.clear();
                this.accountTypeForm.reset();
            },
            showAccountTypeModal(accountType = null) {
                if (accountType) {
                    this.modal.mode = 'edit';
                    this.modal.title = 'Edit the account type';
                    this.modal.buttonTitle = 'Save';

                    this.accountTypeForm.fill(accountType);
                } else {
                    this.modal.mode = 'create';
                    this.modal.title = 'Add a new account type';
                    this.modal.buttonTitle = 'Create';
                }

                $(this.$refs.accountTypeModal).modal('show');

                return false;
            },
            createAccountType() {
                this.$Progress.start();

                this.accountTypeForm.post('api/accountType')
                    .then(response => {
                        this.$Progress.finish();

                        this.loadAccountTypes();
                        $(this.$refs.accountTypeModal).modal('hide');

                        toast.fire({
                            icon: 'success',
                            title: 'Account type added successfully'
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
            updateAccountType() {
                this.$Progress.start();

                this.accountTypeForm.put(`api/accountType/${this.accountTypeForm.id}`)
                    .then(response => {
                        this.$Progress.finish();

                        this.loadAccountTypes();
                        $(this.$refs.accountTypeModal).modal('hide');

                        toast.fire({
                            icon: 'success',
                            title: 'Account type updated successfully'
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
            deleteAccountType(accountType) {
                swal.fire({
                    title: 'Are you sure?',
                    html: `You're going to delete account type "<span class="font-weight-bold">${accountType.name}</span>"!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!',
                }).then((result) => {
                    if (result.value) {
                        this.$Progress.start();

                        axios.delete(`api/accountType/${accountType.id}`)
                            .then(response => {
                                this.$Progress.finish();

                                this.loadAccountTypes();

                                toast.fire({
                                    icon: 'success',
                                    title: 'Account type has been deleted'
                                });
                            })
                            .catch(error => {
                                this.$Progress.fail();

                                toast.fire({
                                    icon: 'error',
                                    title: 'Server error! Can`t delete the account type.'
                                });
                            })
                        ;
                    }
                })
            },
        },
        computed: {
            formAction() {
                return this.modal.mode == 'create' ? this.createAccountType : this.updateAccountType;
            },
        },
        created() {
            // Events
            $(document).on("hidden.bs.modal", this.clearModal);

            // Load account types to the table
            this.loadAccountTypes();
        },
        mounted() {},
    }
</script>
