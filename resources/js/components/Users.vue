<template>
    <div class="container container-fluid">
        <div class="row mt-5">
            <div class="col-12" v-if="$gate.allow('viewAll', 'user')">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Users List</h3>
                        <div class="card-tools">
                            <button class="btn btn-success" v-if="$gate.allow('create', 'user')" @click="showUserModal()">
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
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Registered At</th>
                                    <th>Manage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in users">
                                    <td>{{ user.id }}</td>
                                    <td>{{ user.name }}</td>
                                    <td>{{ user.email }}</td>
                                    <td><span class="tag tag-success">{{ userRoles[user.role_id] | capitalize }}</span></td>
                                    <td>{{ user.created_at | dateMoment('MMMM Do YYYY') }}</td>
                                    <td>
                                        <a href="#" v-if="$gate.allow('update', 'user', user)" @click="showUserModal(user)"><i class="fas fa-edit text-green"></i></a>
                                        /
                                        <a href="#" v-if="$gate.allow('delete', 'user', user)" @click="deleteUser(user)"><i class="fas fa-trash text-red"></i></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <div class="col-12" v-else>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title text-center text-red">You have no permissions</h3>
                    </div>
                </div>
            </div>
        </div>

        <!--modal-->
        <div class="modal fade" id="userModal" ref="userModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
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
                                       v-model="userForm.name"
                                       class="form-control"
                                       :class="{'is-invalid': userForm.errors.has('name')}"
                                       placeholder="Name">
                                <has-error :form="userForm" field="name"></has-error>
                            </div>
                            <div class="form-group">
                                <input type="email"
                                       v-model="userForm.email"
                                       class="form-control"
                                       :class="{'is-invalid': userForm.errors.has('email')}"
                                       placeholder="E-mail">
                                <has-error :form="userForm" field="email"></has-error>
                            </div>
                            <div class="form-group">
                                <select name="role"
                                       v-model="userForm.role_id"
                                       class="form-control"
                                       :class="{'is-invalid': userForm.errors.has('role_id')}"
                                >
                                    <option value="">Select Role</option>
                                    <option v-for="(id, role) in userRoles" :value="role">{{ id }}</option>
                                </select>
                                <has-error :form="userForm" field="role_id"></has-error>
                            </div>

                            <div v-if="modal.showPasswordFields">
                                <div class="form-group">
                                    <input type="password"
                                           v-model="userForm.password"
                                           class="form-control"
                                           :class="{'is-invalid': userForm.errors.has('password')}"
                                           placeholder="Password">
                                    <has-error :form="userForm" field="password"></has-error>
                                </div>
                                <div class="form-group">
                                    <input type="password"
                                           v-model="userForm.passwordConfirmation"
                                           class="form-control"
                                           :class="{'is-invalid': userForm.errors.has('passwordConfirmation')}"
                                           placeholder="Password Confirmation">
                                    <has-error :form="userForm" field="passwordConfirmation"></has-error>
                                </div>
                            </div>
                            <a href="#"
                               class="link-dashed"
                               v-show="modal.mode === 'edit'"
                               @click="togglePasswordFields"
                            >
                                <span v-show="!modal.showPasswordFields">Change user password</span>
                                <span v-show="modal.showPasswordFields">Don't change the password</span>
                            </a>
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
                users: {},
                userRoles: {},
                modal: {
                    target: this.$refs.userModal,
                    mode: 'create',
                    showPasswordFields: true,
                    title: 'Add new user',
                    buttonTitle: 'Create',
                },
                userForm: new Form({
                    id : null,
                    name: '',
                    email: '',
                    password: '',
                    passwordConfirmation: '',
                    role_id: '',
                    photo: '',
                }),
            };
        },
        methods: {
            loadUsers() {
                axios.get('api/user').then(
                    (response) => {
                        this.users = response.data.data;
                    },
                );
            },
            loadRoles() {
                axios.get('api/userRole').then(
                    (response) => {
                        this.userRoles = response.data;
                    },
                );
            },
            clearModal() {
                this.userForm.clear();
                this.userForm.reset();
            },
            showUserModal(user = null) {
                if (user) {
                    // Set modal params
                    this.modal.mode = 'edit';
                    this.modal.showPasswordFields = false;
                    this.modal.title = 'Edit user';
                    this.modal.buttonTitle = 'Save';

                    // Fill form
                    this.userForm.fill(user);
                } else {
                    // Set modal params
                    this.modal.mode = 'create';
                    this.modal.showPasswordFields = true;
                    this.modal.title = 'Add new user';
                    this.modal.buttonTitle = 'Create';
                }

                // Show modal
                $(this.$refs.userModal).modal('show');
            },
            createUser() {
                this.$Progress.start();

                // Add new user
                this.userForm.post('api/user')
                    .then(response => {
                        this.$Progress.finish();

                        // Refresh the table content
                        this.loadUsers();

                        // Close the modal and clean the form
                        $(this.$refs.userModal).modal('hide');

                        // Show success message
                        toast({
                            type: 'success',
                            title: 'User added successfully'
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
            updateUser() {
                this.$Progress.start();

                // Add new user
                this.userForm.put(`api/user/${this.userForm.id}`)
                    .then(response => {
                        this.$Progress.finish();

                        // Refresh the table content
                        this.loadUsers();

                        // Close the modal and clean the form
                        $(this.$refs.userModal).modal('hide');

                        // Show success message
                        toast({
                            type: 'success',
                            title: 'User updated successfully'
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
            deleteUser(user) {
                swal({
                    title: 'Are you sure?',
                    html: `You're going to delete user <span class="font-weight-bold">${user.name}</span>!`,
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        this.$Progress.start();

                        // Delete user
                        axios.delete(`api/user/${user.id}`)
                            .then(response => {
                                // Update progress bar
                                this.$Progress.finish();

                                // Update the table
                                this.loadUsers();

                                // Show the success message
                                toast({
                                    type: 'success',
                                    title: 'User has been deleted'
                                });
                            })
                            .catch(error => {
                                this.$Progress.fail();

                                toast({
                                    type: 'error',
                                    title: 'Server error! Can`t delete the user.'
                                });
                            })
                        ;
                    }
                })
            },
            togglePasswordFields() {
                // Show / hide fields
                this.modal.showPasswordFields = !this.modal.showPasswordFields;

                // Clear
                if (this.modal.showPasswordFields) {
                    this.userForm.password = null;
                    this.userForm.passwordConfirmation = null;
                } else {
                    this.userForm.password = undefined;
                    this.userForm.passwordConfirmation = undefined;
                }
            },
        },
        computed: {
            formAction() {
                return this.modal.mode == 'create' ? this.createUser : this.updateUser;
            },
        },
        created() {
            // Events
            $(document).on("hidden.bs.modal", this.clearModal);

            // Load user roles to use role names instead of IDs
            this.loadRoles();

            // Load users to the table
            this.loadUsers();
        },
        mounted() {},
    }
</script>

<style lang="scss" scoped>
    a.link-dashed {
        text-decoration: none;
        border-bottom: 2px dashed;

        &:hover {
            color: #0000ff;
            border-bottom: 2px dashed #0000ff;
        }
    }
</style>
