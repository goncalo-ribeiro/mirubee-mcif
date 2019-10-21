<template>
    <div class="container" id="login-form">
        <div class="row my-5">
            <div class="col-md-8 m-auto animatebottom">
                <div class="jumbotron">
                    <h1 class="display-4">Mirubee MCIF</h1>
                    <p class="lead">A secure Energy Consumption Monitoring Platform</p>
                    <hr class="my-4">
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <input autofocus type="text" class="form-control" id="usernameInput" placeholder="username">
                        </div>
                        <div class="input-group mb-3">
                            <input autofocus type="password" class="form-control" id="passwordInput" placeholder="password">
                        </div>
                        <div class="input-group mb-3">
                            <button type="button" class="btn btn-primary">login</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>