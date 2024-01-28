<template>
    <div class="container container-fluid">
        <div class="row mt-5" v-if="$gate.allow('viewAll', 'place')">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Places List</h3>
                        <div class="card-tools">
                            <button class="btn btn-success" v-if="$gate.allow('create', 'place')" @click="showPlaceModal()">
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
                                    <th v-if="isAdminMode">User</th>
                                    <th>Name</th>
                                    <th class="text-right">Manage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="place in places.data">
                                    <td>{{ place.id }}</td>
                                    <td v-if="isAdminMode"><router-link :to="`/user/${place.user.id}`">{{ place.user.name }}</router-link></td>
                                    <td>{{ place.name }}</td>
                                    <td class="text-right">
                                        <button type="button" class="btn btn-link btn-as-link" v-if="$gate.allow('update', 'place', place)" @click="showPlaceModal(place)">
                                            <i class="fas fa-edit text-green"></i>
                                        </button>
                                        /
                                        <button type="button" class="btn btn-link btn-as-link" v-if="$gate.allow('delete', 'place', place)" @click="deletePlace(place)">
                                            <i class="fas fa-trash text-red"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <pagination :data="places" @pagination-change-page="loadPlaces"/>
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>

        <div v-else>
            <forbidden-page />
        </div>

        <!--modal-->
        <div class="modal fade" id="placeModal" ref="placeModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
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
                                       v-model="placeForm.name"
                                       class="form-control"
                                       :class="{'is-invalid': placeForm.errors.has('name')}"
                                       placeholder="Name">
                                <has-error :form="placeForm" field="name"></has-error>
                            </div>
                            <div class="form-group">
                                <label class="toggle">
                                    <input @change="toggleIsArchived()" class="toggle__input" type="checkbox" :checked="placeForm.is_archived">
                                    <span class="toggle__label">
                                        <span class="toggle__text">Archived</span>
                                    </span>
                                </label>
                                <has-error :form="placeForm" field="is_archived"></has-error>
                            </div>
                            <div class="form-group" v-if="isAdminMode">
                                <select v-model="placeForm.user_id"
                                       class="form-control"
                                       :class="{'is-invalid': placeForm.errors.has('user_id')}"
                                >
                                    <option value="">Select User</option>
                                    <option v-for="(id, user) in users" :value="user">{{ id }}</option>
                                </select>
                                <has-error :form="placeForm" field="user_id"></has-error>
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
                places: {},
                users: [],
                pageSize: 50,
                viewMode: 'user',
                currentUser: user,
                modal: {
                    target: this.$refs.placeModal,
                    mode: 'create',
                    title: 'Add new place',
                    buttonTitle: 'Create',
                },
                placeForm: new Form({
                    id : null,
                    user_id: null,
                    name: '',
                    is_archived: 0,
                }),
            };
        },
        methods: {
            loadPlaces(page = 1) {
                // Prepare query params
                let queryParams = {
                    page: page,
                    pageSize: this.pageSize,
                };

                if (!this.isAdminMode) {
                    queryParams.userId = this.currentUser.id;
                }

                queryParams = Object.keys(queryParams)
                    .map(k => encodeURIComponent(k) + '=' + encodeURIComponent(queryParams[k]))
                    .join('&')
                ;

                // Send request
                axios.get(`api/place?${queryParams}`).then(
                    (response) => {
                        this.places = response.data;
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
                this.placeForm.clear();
                this.placeForm.reset();
            },
            showPlaceModal(place = null) {
                if (place) {
                    // Set modal params
                    this.modal.mode = 'edit';
                    this.modal.title = 'Edit the place';
                    this.modal.buttonTitle = 'Save';

                    // Fill form
                    this.placeForm.fill(place);
                } else {
                    // Set modal params
                    this.modal.mode = 'create';
                    this.modal.title = 'Add a new place';
                    this.modal.buttonTitle = 'Create';
                    this.placeForm.user_id = this.currentUser.id;
                    this.placeForm.balance = '0.00';
                }

                // Show modal
                $(this.$refs.placeModal).modal('show');
            },
            createPlace() {
                this.$Progress.start();

                this.placeForm.post('api/place')
                    .then(response => {
                        this.$Progress.finish();

                        this.loadPlaces();

                        $(this.$refs.placeModal).modal('hide');

                        toast.fire({
                            icon: 'success',
                            title: 'Place added successfully'
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
            updatePlace() {
                this.$Progress.start();

                this.placeForm.put(`api/place/${this.placeForm.id}`)
                    .then(response => {
                        this.$Progress.finish();

                        this.loadPlaces();

                        $(this.$refs.placeModal).modal('hide');

                        toast.fire({
                            icon: 'success',
                            title: 'Place updated successfully'
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
            deletePlace(place) {
                swal.fire({
                    title: 'Are you sure?',
                    html: `You're going to delete place "<span class="font-weight-bold">${place.name}</span>"!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        this.$Progress.start();

                        axios.delete(`api/place/${place.id}`)
                            .then(response => {
                                this.$Progress.finish();

                                this.loadPlaces()

                                toast.fire({
                                    icon: 'success',
                                    title: 'Place has been deleted'
                                });
                            })
                            .catch(error => {
                                this.$Progress.fail();

                                toast.fire({
                                    icon: 'error',
                                    title: 'Server error! Can`t delete the place.'
                                });
                            })
                        ;
                    }
                })
            },
            toggleIsArchived() {
                this.placeForm.is_archived = !this.placeForm.is_archived;
            },
        },
        computed: {
            formAction() {
                return this.modal.mode === 'create' ? this.createPlace : this.updatePlace;
            },
            isAdminMode() {
                return this.viewMode === 'admin';
            },
        },
        created() {
            // Events
            $(document).on("hidden.bs.modal", this.clearModal);

            // Load places to the table
            this.loadPlaces();

            if (this.isAdminMode) {
                this.loadUsers();
            }
        },
        mounted() {},
    }
</script>
