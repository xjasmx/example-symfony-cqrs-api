<template>
    <div class="profile">
        <div class="col-3">
            <div class="card">
                <div class="tab" v-on:click="tab = 0"> Info</div>
                <div class="tab" v-on:click="tab = 1" style="box-shadow: 0 -1px 0 0 #e5e5e5;"> Settings</div>
            </div>
        </div>
        <div class="col-9">
            <div class="card" v-if=" tab === 0 ">
                <div class="card-header">
                    Profile
                </div>
                <table class="table table-stripped mb-0" v-if="profile">
                    <tbody>
                    <tr>
                        <th>ID</th>
                        <td>{{ profile.id }}</td>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <td>{{ profile.first_name + ' ' + profile.last_name }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ profile.email }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="card" v-if=" tab === 1 && profile">
                <div class="card-header">
                    Edit Profile
                </div>
                <div class="card-body">
                    <b-alert variant="danger" v-if="errors" show>{{ errors.detail }}</b-alert>
                    <b-form @submit="edit">
                        <b-form-group label="First name" label-for="FirstNameChange">
                            <b-form-input
                                    id="FirstNameChange"
                                    type="text"
                                    v-model="profile.first_name"
                                    aria-describedby="confirmNameError"
                                    :state="/[0-9]/gi.test( profile.first_name ) ? false : null"
                                    required>
                            </b-form-input>
                            <b-form-invalid-feedback id="confirmNameError"> Unacceptable symbols!
                            </b-form-invalid-feedback>
                        </b-form-group>
                        <b-form-group label="Last name" label-for="LastNameChange">
                            <b-form-input
                                    id="LastNameChange"
                                    type="text"
                                    v-model="profile.last_name"
                                    aria-describedby="confirmNameError"
                                    :state="/[0-9]/gi.test( profile.last_name ) ? false : null"
                                    required>
                            </b-form-input>
                            <b-form-invalid-feedback id="confirmNameError"> Unacceptable symbols!
                            </b-form-invalid-feedback>
                        </b-form-group>
                        <b-form-group label="User Email" label-for="EmailChange">
                            <b-form-input
                                    id="EmailChange"
                                    type="email"
                                    v-model="profile.email"
                                    aria-describedby="confirmEmailError"
                                    :state="profile.email.match(/^[0-9a-z-\.]+\@[0-9a-z-]{2,}\.[a-z]{2,}$/i)?null:false"
                                    required>
                            </b-form-input>
                            <b-form-invalid-feedback id="confirmNameError"> Unacceptable symbols!
                            </b-form-invalid-feedback>
                        </b-form-group>
                        <div class="passwordLine"><span
                                v-on:click="passwordChange = !passwordChange"> ChangePassword  </span></div>
                        <b-form-group label="Current password" label-for="currentPassword" v-if="passwordChange">
                            <b-form-input
                                    id="currentPassword"
                                    type="password"
                                    v-model="currentPassword"
                                    aria-describedby="confirmCurrentPasswordError"
                                    :state="currentPassword.length > 5 ?null:false"
                                    required>
                            </b-form-input>
                            <b-form-invalid-feedback id="confirmCurrentPasswordError"> Password is too short
                            </b-form-invalid-feedback>
                        </b-form-group>
                        <b-form-group label="New password" label-for="FirstPassword" v-if="passwordChange">
                            <b-form-input
                                    id="FirstPassword"
                                    type="password"
                                    v-model="newPassword"
                                    aria-describedby="confirmPassError"
                                    :state=" newPassword.length < 5 ? false : null"
                                    required>
                            </b-form-input>
                            <b-form-invalid-feedback id="confirmCurrentPasswordError"> Password is too short
                            </b-form-invalid-feedback>
                        </b-form-group>
                        <b-form-group label="Replay password" label-for="replayPassword" v-if="passwordChange">
                            <b-form-input
                                    id="replayPassword"
                                    type="password"
                                    v-model="newSecondPassword"
                                    aria-describedby="confirmSecError"
                                    :state="newSecondPassword === newPassword ?null:false"
                                    required>
                            </b-form-input>
                            <b-form-invalid-feedback id="confirmSecError"> Password is not correct
                            </b-form-invalid-feedback>
                        </b-form-group>
                        <b-button type="submit" variant="primary">Confirm</b-button>
                    </b-form>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    import axios from "axios";

    export default {
        data() {
            return {
                profile: null,
                tab: 0,
                passwordChange: false,
                newPassword: '',
                newSecondPassword: '',
                startProfile: null,
                currentPassword: '',
                errors: null
            }
        },
        mounted() {
            axios
                .get('/api/v1/profile')
                .then(response => {
                    this.profile = {...response.data};
                    this.startProfile = {...response.data};
                })
                .catch(error => {
                    if (error.response) {
                        this.errors = error.response.data.errors;
                    } else {
                        console.log(error.message);
                    }
                });
        },
        methods: {
            edit(event) {
                event.preventDefault();
                if (this.profile.email !== this.startProfile.email) {
                    axios
                        .patch('/api/v1/profile/email', {email: this.profile.email, id: this.profile.id})
                        .then(() => {
                            this.startProfile.email = this.profile.email;
                        })
                        .catch(error => {
                            if (error.response) {
                                this.errors = error.response.data.errors;
                            } else {
                                console.log(error.message);
                            }
                        });
                }
                if (this.profile.first_name !== this.startProfile.first_name || this.profile.last_name !== this.startProfile.last_name) {
                    axios
                        .patch('/api/v1/profile/name', {
                            first_name: this.profile.first_name,
                            last_name: this.profile.last_name,
                            id: this.profile.id
                        })
                        .then(() => {
                            this.startProfile.first_name = this.profile.first_name;
                            this.startProfile.last_name = this.profile.last_name;
                        })
                        .catch(error => {
                            if (error.response) {
                                this.errors = error.response.data.errors;
                            } else {
                                console.log(error.message);
                            }
                        });
                }
                if (this.passwordChange && this.newPassword === this.newSecondPassword) {
                    axios
                        .patch('/api/v1/profile/password', {
                            id: this.profile.id,
                            current_password: this.currentPassword,
                            new_password: this.newPassword
                        })
                        .then(() => {
                            this.currentPassword = '';
                            this.newPassword = '';
                            this.newSecondPassword = '';
                        })
                        .catch(error => {
                            if (error.response) {
                                this.errors = error.response.data.errors;
                            } else {
                                console.log(error.message);
                            }
                        });
                }
            }
        }
    }
</script>

<style>
    .tab {
        padding: 10px;
        transition: all 0.4s;
        color: #4183c4;
    }

    .tab:hover {
        cursor: pointer;
        opacity: 0.8;
    }

    .passwordLine {
        text-align: left;
        padding-top: 10px;
        padding-bottom: 10px;
    }

    .passwordLine > span {
        background: none;
        border: none;
        padding-bottom: 4px;
        transition: all 0.4s;
        color: #4183c4;
        box-shadow: 0 1px 0 0 transparent;
    }

    .passwordLine > span:focus {
        outline: none;
    }

    .passwordLine > span:hover {
        opacity: 0.8;
        box-shadow: 0 1px 0 0 #4183c4;
        cursor: pointer;
    }

    .profile {
        display: flex;
        flex-wrap: wrap;
    }
</style>