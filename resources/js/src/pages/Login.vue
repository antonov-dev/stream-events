<script>
import {defineComponent} from 'vue'

export default defineComponent({
    name: "Login"
})
</script>

<template>
    <div class="row justify-content-md-center m-5">
        <div class="col-6">
            <div class="border border border-opacity-25 border-light p-5 rounded text-center">
                <h1>We don't know you!</h1>
                <p class="lead">You need to login to get access to all features</p>
                <button class="btn btn-lg btn-primary" @click="$store.dispatch('auth/login')" role="button">Login with GitHub</button>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
