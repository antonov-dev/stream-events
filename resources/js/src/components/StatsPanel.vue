<script>
import axios from "axios";

export default {
    name: "StatsPanel",
    data() {
        return {
            stats: {},
            isLoaded: false
        }
    },
    methods: {
        async getStats() {
            await axios
                .get(window.location.origin + import.meta.env.VITE_API_URL +'/event-stats')
                .then((response) => {
                    if(response.data.status === 'success'
                        && (response.data.data.best_merch_sales || response.data.data.total_followers || response.data.data.total_revenue)) {
                        this.stats = response.data.data;
                        this.isLoaded = true;
                    }
                })
                .catch((e) => {
                    console.log(e);
                })
        },
    },
    mounted() {
        this.getStats();

        // Try to load one more time if DB seeding will take to much
        setTimeout(() => {
            if(!this.isLoaded) {
                this.getStats();
            }
        }, 15000);
    }
}
</script>

<template>
    <div v-if="isLoaded" class="row row-cols-1 row-cols-md-3 my-5 text-center">
        <div class="col">
            <div class="card rounded-3 shadow-sm">
                <div class="card-header py-3">
                    <h4 class="my-0 fw-normal">Total Revenue <small class="text-muted fw-light"> / 30d</small></h4>
                </div>
                <div class="card-body">
                    <h1 class="card-title pricing-card-title fs-4 mb-0">{{ stats.total_revenue.amount }} {{ stats.total_revenue.currency }}</h1>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card rounded-3 shadow-sm">
                <div class="card-header py-3">
                    <h4 class="my-0 fw-normal">Followers <small class="text-muted fw-light"> / 30d</small></h4>
                </div>
                <div class="card-body">
                    <h1 class="card-title pricing-card-title fs-4 mb-0">{{ stats.total_followers }} persons</h1>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card rounded-3 shadow-sm">
                <div class="card-header py-3">
                    <h4 class="my-0 fw-normal">Top 3 Best Sales <small class="text-muted fw-light"> / 30d</small></h4>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li v-for="sale in stats.best_merch_sales">{{ sale.name }} * {{ sale.amount }}</li>

                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
