<template>
    <div class="container container-fluid">
        <div class="row mt-5" v-if="$gate.allow('view', 'user', profile)">
            <div class="col-md-3">
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <div v-if="!profile.photo" v-html="svg('user', 'img-circle')" class="user-profile-image-main" />
                            <img v-else :src="profileImageUrl" :alt="profile.name" class="img-circle user-profile-image-main">
                        </div>

                        <h3 class="profile-username text-center">{{ profile.name }}</h3>

                        <p v-if="profile.role" class="text-muted text-center">{{ profile.role.name | capitalize }}</p>

                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>Accounts</b> <a v-if="profile.accounts" class="float-right">{{ Object.keys(profile.accounts).length }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Money</b> <a v-if="profile.accounts" class="float-right">{{ allAccountsAmount }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Transactions</b> <a v-if="profile.accounts" class="float-right">{{ profile.transactions_count }}</a>
                            </li>
                        </ul>

                        <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active show" href="#settings" data-toggle="tab">Settings</a></li>
                        </ul>
                    </div>
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
                                                       autocomplete="new-password"
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
                                                       autocomplete="new-password"
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

                                    <div class="form-group" v-if="$gate.allow('update', 'user', profile)">
                                        <div class="col-sm-offset-2 col-sm-10">
                                            <button type="submit" class="btn btn-outline-success">Update</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
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
                axios.get(`api/user/${window.user.id}`).then(
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
                this.profileForm.put(`api/user/${window.user.id}`)
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
        computed: {
            profileImageUrl() {
                // Preview new profile image before upload
                if (this.profileForm.photo) {
                    return this.profileForm.photo !== this.profile.photo ? this.profileForm.photo : this.profile.photo;
                }
            },
            allAccountsAmount() {
                let sum = 0;

                for (let account of this.profile.accounts) {
                    if (account.calculate_costs) {
                        sum += parseFloat(account.balance);
                    }
                }

                return sum.toFixed(2);
            }
        },
        created() {
            this.loadProfile();
        },
        mounted() {}
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
