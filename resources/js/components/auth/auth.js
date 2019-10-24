import Vue from 'vue';
//import Cookies from 'js-cookie';

const BASE_URL = myUrl;
const LOGIN_URL = 'api/login';
const LOGOUT_URL = 'api/user/logout';
let tokenType = undefined;
let token = undefined;


export default {

    user : undefined,

    install(Vue, options) {
        Vue.prototype.$auth = Vue.auth = this;
        //this.loadCookies();
        this.requestUserInfo();
    },

    login(credentials){
        //TODO: nao retornar o pedido axios mas sim lancar um evento para o login a indicar o estado do pedido
        console.log(credentials)
        return axios.post(LOGIN_URL, { email : credentials.email, password : credentials.password })
            .then( success => {
                let data = success.data;
                tokenType = data.token_type;
                token = data.access_token;

                // Cookies.set('tokenType', tokenType, {expires: 7, path: BASE_URL});
                // Cookies.set('token', token, {expires: 7, path: BASE_URL});
                axios.defaults.headers.common['Authorization'] = tokenType + " " + token;

                return axios.get('api/users/email/' + credentials.email); //VAI ser avaliada pelo proximo then
            })
            .then( success => {
                this.user = success.data.data;
                console.log(JSON.stringify(this.user));
                // Cookies.set('user', JSON.stringify(this.user), {expires: 7, path: BASE_URL});

                Event.$emit('login-success');
            });
    },

    logout() {
        return axios.post(LOGOUT_URL)
            .then(response=> {
                Event.$emit('logout-success');
                // this.clearCookies();
                this.user = undefined;
            });
            //This way i can have the code calling this method a Then with a catch.
        //if the catch was here it would not be possible to call a then when calling this function
    },

    isLogged() {
        // return (Cookies.get('token')) ? true : false;
        return (this.user) ? true : false;
    },

    requestUserInfo() {
        if(this.isLogged()){
            axios.get('api/users/email/' + this.user.email)
                .then((msg) => {
                    console.log("RequestUserInfo: ", msg);
                    this.refreshUser(msg.data.data);
                })
                .catch((error) => {

                });
        }
    },

    refreshUser(newUser){
        this.user = newUser;
        //Cookies.set('user', JSON.stringify(this.user), {expires: 7, path: BASE_URL});
        Event.$emit('user-updated', this.user);
    },

    storeUserInfo(){
        // Cookies.set('user', JSON.stringify(this.user), {expires: 7, path: BASE_URL});
    },
/*
    loadCookies(){
        console.log("Loading Cookies");
        let cachedTokenType = Cookies.get('tokenType');
        let cachedToken = Cookies.get('token');
        let unparsedCachedUser = Cookies.get('user');
        let cachedUser = (unparsedCachedUser != null ? JSON.parse(unparsedCachedUser) : null);

        if(cachedToken !== undefined && cachedToken !== null && cachedUser !== undefined && cachedUser !== null){
            console.log("loginTokenType = "+ cachedTokenType);
            console.log("cachedUser = ", cachedUser);

            tokenType = cachedTokenType;
            token = cachedToken;
            this.user = cachedUser;
            axios.defaults.headers.common['Authorization'] = tokenType + " " + token;

        }
    },

    clearCookies(){
        Cookies.delete('tokenType', {path: BASE_URL});
        Cookies.delete('token', {path: BASE_URL});
        Cookies.delete('user', {path: BASE_URL});
        console.log("Cookies Cleared");
    },
*/

}