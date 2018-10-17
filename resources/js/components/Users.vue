<template>
    <div class="container container-fluid">
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Users List</h3>
                        <div class="card-tools">
                            <button class="btn btn-success" data-toggle="modal" data-target="#addUserModal">
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
                                        <a href="#"><i class="fas fa-edit text-green"></i></a>
                                        /
                                        <a href="#" @click="deleteUser(user)"><i class="fas fa-trash text-red"></i></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>

        <!--modal-->
        <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add user</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="createUser">
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="text"
                                       v-model="newUserForm.name"
                                       class="form-control"
                                       :class="{'is-invalid': newUserForm.errors.has('name')}"
                                       placeholder="Name">
                                <has-error :form="newUserForm" field="name"></has-error>
                            </div>
                            <div class="form-group">
                                <input type="email"
                                       v-model="newUserForm.email"
                                       class="form-control"
                                       :class="{'is-invalid': newUserForm.errors.has('email')}"
                                       placeholder="E-mail">
                                <has-error :form="newUserForm" field="email"></has-error>
                            </div>
                            <div class="form-group">
                                <input type="password"
                                       v-model="newUserForm.password"
                                       class="form-control"
                                       :class="{'is-invalid': newUserForm.errors.has('password')}"
                                       placeholder="Password">
                                <has-error :form="newUserForm" field="password"></has-error>
                            </div>
                            <div class="form-group">
                                <input type="password"
                                       v-model="newUserForm.passwordConfirm"
                                       class="form-control"
                                       :class="{'is-invalid': newUserForm.errors.has('passwordConfirm')}"
                                       placeholder="Password Confirmation">
                                <has-error :form="newUserForm" field="passwordConfirm"></has-error>
                            </div>
                            <div class="form-group">
                                <select name="role"
                                       v-model="newUserForm.role"
                                       class="form-control"
                                       :class="{'is-invalid': newUserForm.errors.has('passwordConfirm')}"
                                >
                                    <option value="">Select Role</option>
                                    <option v-for="(id, role) in userRoles" :value="role">{{ id }}</option>
                                </select>
                                <has-error :form="newUserForm" field="role"></has-error>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Create</button>
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
                newUserForm: new Form({
                    name: '',
                    email: '',
                    password: '',
                    passwordConfirm: '',
                    role: '',
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
            createUser() {
                this.$Progress.start();

                // Add new user
                this.newUserForm.post('api/user')
                    .then(response => {
                        this.$Progress.finish();

                        // Refresh the table content
                        this.loadUsers();

                        // Close the modal and clean the form
                        let modal = $('#addUserModal');
                        modal.find('form').get(0).reset();
                        modal.modal('hide');

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
        },
        created() {
            // Load user roles to use role names instead of IDs
            this.loadRoles();

            // Load users to the table
            this.loadUsers();
        }
    }
</script>
