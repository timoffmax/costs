<template>
    <div class="container container-fluid">
        <div class="row mt-5">

            <div class="col-md-3">
                <!-- Profile Image -->
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <div v-html="svg('user', 'img-circle')" class="user-profile-image-main" />
                        </div>

                        <h3 class="profile-username text-center">{{ profile.name }}</h3>

                        <p class="text-muted text-center">Software Engineer</p>

                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>Followers</b> <a class="float-right">1,322</a>
                            </li>
                            <li class="list-group-item">
                                <b>Following</b> <a class="float-right">543</a>
                            </li>
                            <li class="list-group-item">
                                <b>Friends</b> <a class="float-right">13,287</a>
                            </li>
                        </ul>

                        <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->

                <!-- About Me Box -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">About Me</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <strong><i class="fa fa-book mr-1"></i> Education</strong>

                        <p class="text-muted">
                            B.S. in Computer Science from the University of Tennessee at Knoxville
                        </p>

                        <hr>

                        <strong><i class="fa fa-map-marker mr-1"></i> Location</strong>

                        <p class="text-muted">Malibu, California</p>

                        <hr>

                        <strong><i class="fa fa-pencil mr-1"></i> Skills</strong>

                        <p class="text-muted">
                            <span class="tag tag-danger">UI Design</span>
                            <span class="tag tag-success">Coding</span>
                            <span class="tag tag-info">Javascript</span>
                            <span class="tag tag-warning">PHP</span>
                            <span class="tag tag-primary">Node.js</span>
                        </p>

                        <hr>

                        <strong><i class="fa fa-file-text-o mr-1"></i> Notes</strong>

                        <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fermentum enim neque.</p>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active show" href="#settings" data-toggle="tab">Settings</a></li>
                        </ul>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active show" id="settings">
                                <form class="form-horizontal" @submit.prevent="updateProfile">
                                    <div class="form-group">
                                        <label for="profileInputName" class="col-sm-6 control-label">Name</label>

                                        <div class="col-md-12 col-lg-6">
                                            <input type="text"
                                                   id="profileInputName"
                                                   v-model="profileForm.name"
                                                   class="form-control"
                                                   :class="{'is-invalid': profileForm.errors.has('name')}"
                                                   placeholder="Name">
                                            <has-error :form="profileForm" field="name"></has-error>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="profileInputEmail" class="col-sm-6 control-label">E-mail</label>

                                        <div class="col-md-12 col-lg-6">
                                            <input type="email"
                                                   id="profileInputEmail"
                                                   v-model="profileForm.email"
                                                   class="form-control"
                                                   :class="{'is-invalid': profileForm.errors.has('email')}"
                                                   placeholder="E-mail">
                                            <has-error :form="profileForm" field="email"></has-error>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="profileInputImage" class="col-sm-6 control-label">Profile image</label>

                                        <div class="col-md-12 col-lg-6">
                                            <input type="file"
                                                   id="profileInputImage"
                                                   @change="encodeProfileImage"
                                                   class="form-control"
                                                   :class="{'is-invalid': profileForm.errors.has('photo')}"
                                            >
                                            <has-error :form="profileForm" field="photo"></has-error>
                                        </div>
                                    </div>


                                    <div v-show="showPasswordFields">
                                        <div class="form-group">
                                            <label for="profileInputPassword" class="col-sm-6 control-label">Password</label>

                                            <div class="col-md-12 col-lg-6">
                                                <input type="password"
                                                       id="profileInputPassword"
                                                       v-model="profileForm.password"
                                                       class="form-control"
                                                       :class="{'is-invalid': profileForm.errors.has('password')}"
                                                       placeholder="Password">
                                                <has-error :form="profileForm" field="password"></has-error>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="profileInputPasswordConfirmation" class="col-sm-6 control-label">Password confirmation</label>

                                            <div class="col-md-12 col-lg-6">
                                                <input type="password"
                                                       id="profileInputPasswordConfirmation"
                                                       v-model="profileForm.passwordConfirmation"
                                                       class="form-control"
                                                       :class="{'is-invalid': profileForm.errors.has('passwordConfirmation')}"
                                                       placeholder="Password confirmation">
                                                <has-error :form="profileForm" field="passwordConfirmation"></has-error>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-10">
                                            <a href="#"
                                               class="link-dashed"
                                               @click="togglePasswordFields"
                                            >
                                                <span v-show="!showPasswordFields">Change password</span>
                                                <span v-show="showPasswordFields">Don't change the password</span>
                                            </a>
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <div class="col-sm-offset-2 col-sm-10">
                                            <button type="submit" class="btn btn-outline-success">Update</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                    </div><!-- /.card-body -->
                </div>
                <!-- /.nav-tabs-custom -->
            </div>

        </div>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                profile: {},
                profileForm: new Form({
                    id: null,
                    name: '',
                    email: '',
                    password: '',
                    passwordConfirmation: '',
                    photo: '',
                }),
                showPasswordFields: false,
            };
        },
        methods: {
            loadProfile() {
                // Get current user data
                axios.get('api/profile').then(
                    (response) => {
                        this.profile = response.data;

                        // Fill form
                        this.profileForm.fill(this.profile);
                    },
                );
            },
            updateProfile() {
                this.$Progress.start();

                // Add new user
                this.profileForm.put(`api/profile/${this.profileForm.id}`)
                    .then(response => {
                        this.$Progress.finish();

                        // Refresh content
                        this.loadProfile();

                        // Show success message
                        toast({
                            type: 'success',
                            title: 'Profile updated successfully'
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
            togglePasswordFields() {
                // Show / hide fields
                this.showPasswordFields = !this.showPasswordFields;

                // Clear
                if (this.showPasswordFields) {
                    this.profileForm.password = null;
                    this.profileForm.passwordConfirmation = null;
                } else {
                    this.profileForm.password = undefined;
                    this.profileForm.passwordConfirmation = undefined;
                }
            },
            // Image to base64
            encodeProfileImage(e) {
                let file = e.target.files[0];
                let reader = new FileReader();
                
                reader.onloadend = () => {
                    this.profileForm.photo = reader.result;
                };

                reader.readAsDataURL(file);
            },
        },
        created() {
            this.loadProfile();
        },
        mounted() {}
    }
</script>

<style lang="scss" scoped>
    .user-profile-image-main {
        width: 84px;
        margin: auto;
    }

    a.link-dashed {
        text-decoration: none;
        border-bottom: 2px dashed;

        &:hover {
            color: #0000ff;
            border-bottom: 2px dashed #0000ff;
        }
    }
</style>
