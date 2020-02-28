import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        currentEmail: null,
        user: JSON.parse(localStorage.getItem('user')),
        currentTab:0
    },
    getters: {
        isLoggedIn(state) {
            return !!state.user;
        }
    },
    mutations: {
        changeCurrentEmail(state, email) {
            state.currentEmail = email;
        },
        login(state, user) {
            state.user = user;
        },
        logout(state) {
            state.user = null;
        },
        changeCurrentTab( state , newTab ){
            state.currentTab = newTab;
        }
    },
    actions: {
        login(context, data) {
            return new Promise((resolve, reject) => {
                context.commit('logout');
                const formData = new FormData();
                formData.set('grant_type',  'password');
                formData.set('username',  data.username);
                formData.set('password',  data.password);
                formData.set('client_id',  '67f073edbe5fd67b9fa41570e9fd1d29');
                formData.set('client_secret',  'd912e059a87d1a3cdad6082e4c6eec966bb78b8c183b4ea230c80d3a0c87e598524b382291919a7824f7cea2308550003d6134d18d58b2be94874e64627b59f1');
                formData.set('scope',  '');

                axios.post('/token', formData)
                    .then(response => {
                        const user = response.data;
                        localStorage.setItem('user', JSON.stringify(user));
                        axios.defaults.headers.common['Authorization'] = 'Bearer ' + user.access_token;
                        context.commit('login', user);
                        resolve(user)
                    })
                    .catch(error => {
                        context.commit('logout');
                        localStorage.removeItem('user');
                        reject(error)
                    })
            })
        },
        logout(context) {
            return new Promise((resolve) => {
                context.commit('logout');
                localStorage.removeItem('user');
                delete axios.defaults.headers.common['Authorization'];
                resolve()
            });
        },
        refresh(context) {
            return new Promise((resolve, reject) => {
                if (context.state.user) {
                    delete axios.defaults.headers.common['Authorization'];
                    const formData = new FormData();
                    formData.set('grant_type',  'refresh_token');
                    formData.set('refresh_token',  context.state.user.refresh_token);
                    formData.set('client_id',  '67f073edbe5fd67b9fa41570e9fd1d29');
                    formData.set('client_secret',  'd912e059a87d1a3cdad6082e4c6eec966bb78b8c183b4ea230c80d3a0c87e598524b382291919a7824f7cea2308550003d6134d18d58b2be94874e64627b59f1');
                    formData.set('scope',  '');

                    return axios.post('/token', formData)
                        .then(response => {
                            const user = response.data;
                            localStorage.setItem('user', JSON.stringify(user));
                            axios.defaults.headers.common['Authorization'] = 'Bearer ' + user.access_token;
                            context.commit('login', user);
                            resolve(response)
                        })
                        .catch(error => {
                            context.dispatch('logout');
                            reject(error)
                        })
                }
                resolve()
            });
        }
    }
})