<script>
import {defineComponent} from 'vue'

export default defineComponent({
    name: "AppNavigation"
})
</script>

<template>
    <header class="d-flex flex-wrap justify-content-center py-3 mb-4 border-bottom">
        <span class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <span class="fs-4">Stream Events</span>
        </span>

        <ul class="nav nav-pills">
            <li class="nav-item"><router-link to="/spa" class="nav-link" aria-current="page">Home</router-link></li>
            <li class="nav-item"><router-link to="/spa/events" class="nav-link">Events</router-link></li>
            <li class="nav-item"><router-link to="/spa/login" class="nav-link">Login</router-link></li>
        </ul>
    </header>
</template>

<style scoped>

</style>
